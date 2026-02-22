<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class ServiceRequestController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->resolveFilters($request);

        $requests = $this->filteredQuery($filters)
            ->latest('submitted_at')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $completed = $this->filteredQuery($filters)
            ->with('service:id,name,sla_target_hours')
            ->where('status', 'selesai')
            ->whereNotNull('submitted_at')
            ->whereNotNull('processed_at')
            ->get(['id', 'service_id', 'submitted_at', 'processed_at', 'status']);

        $completedCount = $completed->count();
        $completedWithinSlaCount = $completed
            ->filter(fn (ServiceRequest $row) => $this->isWithinSla($row))
            ->count();
        $slaPercent = $completedCount > 0 ? round(($completedWithinSlaCount / $completedCount) * 100, 1) : 0;
        $avgHoursOverall = $completedCount > 0
            ? round((float) $completed->avg(fn (ServiceRequest $row) => $this->processingHours($row)), 2)
            : 0;

        $serviceAverages = $completed
            ->groupBy('service_id')
            ->map(function ($rows) {
                /** @var \Illuminate\Support\Collection<int, ServiceRequest> $rows */
                $first = $rows->first();
                $avgHours = (float) $rows->avg(fn (ServiceRequest $item) => $this->processingHours($item));
                $slaTarget = (int) ($first?->service?->sla_target_hours ?? 72);
                $slaHits = $rows->filter(fn (ServiceRequest $item) => $this->isWithinSla($item))->count();
                $slaPercent = $rows->count() > 0 ? round(($slaHits / $rows->count()) * 100, 1) : 0;

                return [
                    'service_name' => $first?->service?->name ?? 'Layanan',
                    'completed_count' => $rows->count(),
                    'avg_hours' => round($avgHours, 2),
                    'sla_target_hours' => $slaTarget,
                    'sla_percent' => $slaPercent,
                ];
            })
            ->sortBy('avg_hours')
            ->values();

        $stats = [
            'total_requests' => $this->filteredQuery($filters)->count(),
            'completed_requests' => $this->filteredQuery($filters)->where('status', 'selesai')->count(),
            'avg_hours_overall' => $avgHoursOverall,
            'sla_percent' => $slaPercent,
        ];

        $status = (string) ($filters['status'] ?? '');
        $dateFrom = $filters['date_from']?->format('Y-m-d');
        $dateTo = $filters['date_to']?->format('Y-m-d');

        return view('admin.service-requests.index', compact(
            'requests',
            'status',
            'dateFrom',
            'dateTo',
            'stats',
            'serviceAverages'
        ));
    }

    public function show(ServiceRequest $serviceRequest): View
    {
        $serviceRequest->load('service', 'village');

        return view('admin.service-requests.show', compact('serviceRequest'));
    }

    public function update(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:diajukan,diverifikasi,diproses,selesai,ditolak'],
            'status_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $oldStatus = $serviceRequest->status;
        $serviceRequest->status = $validated['status'];
        $serviceRequest->status_note = $validated['status_note'] ?? null;

        if ($oldStatus !== $serviceRequest->status && in_array($serviceRequest->status, ['selesai', 'ditolak'], true)) {
            $serviceRequest->processed_at = now();
        }

        $serviceRequest->save();

        return redirect()
            ->route('admin.service-requests.show', $serviceRequest)
            ->with('status', 'Status pengajuan layanan berhasil diperbarui.');
    }

    public function destroy(ServiceRequest $serviceRequest): RedirectResponse
    {
        if ($serviceRequest->attachment_path && Storage::disk('public')->exists($serviceRequest->attachment_path)) {
            Storage::disk('public')->delete($serviceRequest->attachment_path);
        }

        $serviceRequest->delete();

        return redirect()->route('admin.service-requests.index')->with('status', 'Pengajuan layanan berhasil dihapus.');
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $filters = $this->resolveFilters($request);
        $rows = $this->filteredQuery($filters)
            ->orderBy('submitted_at')
            ->get();

        $fileName = 'laporan-pengajuan-layanan-'.now()->format('Ymd-His').'.xls';

        return response()->streamDownload(function () use ($rows): void {
            echo '<table border="1">';
            echo '<tr><th>No Tiket</th><th>Layanan</th><th>Nama</th><th>NIK</th><th>HP</th><th>Status</th><th>SLA (Jam)</th><th>Waktu Proses (Jam)</th><th>Tgl Pengajuan</th><th>Tgl Selesai</th></tr>';
            foreach ($rows as $row) {
                $serviceName = e($row->service?->name ?? '-');
                $processingHours = number_format($this->processingHours($row), 2, '.', '');
                $slaTarget = (int) ($row->service?->sla_target_hours ?? 72);
                echo '<tr>';
                echo '<td>'.e($row->ticket_code).'</td>';
                echo '<td>'.$serviceName.'</td>';
                echo '<td>'.e($row->applicant_name).'</td>';
                echo '<td>'.e($row->nik).'</td>';
                echo '<td>'.e($row->phone).'</td>';
                echo '<td>'.e(ucfirst($row->status)).'</td>';
                echo '<td>'.$slaTarget.'</td>';
                echo '<td>'.$processingHours.'</td>';
                echo '<td>'.e($row->submitted_at?->format('d-m-Y H:i') ?? '-').'</td>';
                echo '<td>'.e($row->processed_at?->format('d-m-Y H:i') ?? '-').'</td>';
                echo '</tr>';
            }
            echo '</table>';
        }, $fileName, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $filters = $this->resolveFilters($request);
        $rows = $this->filteredQuery($filters)
            ->orderBy('submitted_at')
            ->get();

        $period = [
            'from' => $filters['date_from']?->format('d M Y'),
            'to' => $filters['date_to']?->format('d M Y'),
            'status' => $filters['status'] ?: 'Semua',
        ];

        return Pdf::loadView('admin.service-requests.report-pdf', [
            'rows' => $rows,
            'period' => $period,
        ])->setPaper('a4', 'landscape')->download('laporan-pengajuan-layanan-'.now()->format('Ymd-His').'.pdf');
    }

    private function resolveFilters(Request $request): array
    {
        $status = (string) $request->query('status', '');
        $dateFrom = (string) $request->query('date_from', '');
        $dateTo = (string) $request->query('date_to', '');

        $from = null;
        $to = null;
        try {
            $from = $dateFrom !== '' ? Carbon::createFromFormat('Y-m-d', $dateFrom)->startOfDay() : null;
        } catch (\Throwable) {
            $from = null;
        }
        try {
            $to = $dateTo !== '' ? Carbon::createFromFormat('Y-m-d', $dateTo)->endOfDay() : null;
        } catch (\Throwable) {
            $to = null;
        }

        return [
            'status' => in_array($status, ['diajukan', 'diverifikasi', 'diproses', 'selesai', 'ditolak'], true) ? $status : '',
            'date_from' => $from,
            'date_to' => $to,
        ];
    }

    private function filteredQuery(array $filters)
    {
        return ServiceRequest::query()
            ->with('service:id,name,sla_target_hours')
            ->when($filters['status'] !== '', fn ($query) => $query->where('status', $filters['status']))
            ->when($filters['date_from'], fn ($query, $from) => $query->where('submitted_at', '>=', $from))
            ->when($filters['date_to'], fn ($query, $to) => $query->where('submitted_at', '<=', $to));
    }

    private function processingHours(ServiceRequest $row): float
    {
        if (!$row->submitted_at || !$row->processed_at) {
            return 0.0;
        }

        return round($row->submitted_at->diffInMinutes($row->processed_at) / 60, 2);
    }

    private function isWithinSla(ServiceRequest $row): bool
    {
        if (!$row->submitted_at || !$row->processed_at) {
            return false;
        }

        $target = (int) ($row->service?->sla_target_hours ?? 72);

        return $row->submitted_at->diffInHours($row->processed_at) <= $target;
    }
}
