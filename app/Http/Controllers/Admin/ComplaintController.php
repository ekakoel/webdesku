<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintResponse;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->resolveFilters($request);

        $complaints = $this->filteredQuery($filters)
            ->latest('submitted_at')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => $this->filteredQuery($filters)->count(),
            'baru' => $this->filteredQuery($filters)->where('status', 'baru')->count(),
            'diproses' => $this->filteredQuery($filters)->where('status', 'diproses')->count(),
            'selesai' => $this->filteredQuery($filters)->where('status', 'selesai')->count(),
            'ditolak' => $this->filteredQuery($filters)->where('status', 'ditolak')->count(),
            'reachable' => $this->filteredQuery($filters)
                ->where(function ($query) {
                    $query->whereNotNull('email')->where('email', '!=', '')
                        ->orWhereNotNull('whatsapp')->where('whatsapp', '!=', '');
                })->count(),
        ];
        $stats['completion_rate'] = $stats['total'] > 0 ? round(($stats['selesai'] / $stats['total']) * 100, 1) : 0;
        $stats['response_rate'] = $stats['total'] > 0 ? round((($stats['diproses'] + $stats['selesai']) / $stats['total']) * 100, 1) : 0;

        $statusBreakdown = collect(['baru', 'diproses', 'selesai', 'ditolak'])
            ->map(function (string $statusKey) use ($stats) {
                $count = (int) ($stats[$statusKey] ?? 0);
                $total = (int) ($stats['total'] ?? 0);

                return [
                    'key' => $statusKey,
                    'label' => match ($statusKey) {
                        'baru' => 'Baru',
                        'diproses' => 'Diproses',
                        'selesai' => 'Selesai',
                        'ditolak' => 'Ditolak',
                        default => ucfirst($statusKey),
                    },
                    'count' => $count,
                    'percent' => $total > 0 ? round(($count / $total) * 100, 1) : 0,
                ];
            })->all();

        $categorySummary = $this->filteredQuery($filters)
            ->selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(8)
            ->get()
            ->map(function ($row) use ($stats) {
                $count = (int) $row->total;
                $total = (int) ($stats['total'] ?? 0);

                return [
                    'category' => (string) $row->category,
                    'label' => \Illuminate\Support\Str::headline((string) $row->category),
                    'count' => $count,
                    'percent' => $total > 0 ? round(($count / $total) * 100, 1) : 0,
                ];
            })->values()->all();

        $monthlyTrend = collect(range(1, 12))
            ->map(function (int $month) use ($filters) {
                $monthFilters = $filters;
                $monthFilters['month'] = $month;
                $count = $this->filteredQuery($monthFilters)->count();

                return [
                    'month' => $month,
                    'label' => Carbon::create()->month($month)->translatedFormat('M'),
                    'count' => $count,
                ];
            })->all();

        $status = (string) ($filters['status'] ?? '');
        $month = (int) ($filters['month'] ?? 0);
        $year = (int) ($filters['year'] ?? now()->year);
        $yearOptions = $this->availableYears();

        $activeModalTicket = (string) session('complaint_modal_ticket', '');

        return view('admin.complaints.index', compact(
            'complaints',
            'stats',
            'statusBreakdown',
            'categorySummary',
            'monthlyTrend',
            'status',
            'month',
            'year',
            'yearOptions',
            'activeModalTicket',
        ));
    }

    public function update(Request $request, Complaint $complaint): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:baru,diproses,selesai,ditolak'],
            'status_note' => ['required', 'string', 'max:2000'],
        ]);

        $oldStatus = $complaint->status;
        if ($oldStatus !== 'baru' && $validated['status'] === 'baru') {
            return redirect()->route('admin.complaints.index', $request->query())
                ->withErrors(['status' => 'Status tidak bisa kembali ke Baru setelah pengaduan ditindaklanjuti.'])
                ->withInput()
                ->with('complaint_modal_ticket', $complaint->ticket_code);
        }

        $complaint->status = $validated['status'];
        $complaint->status_note = $validated['status_note'] ?? null;

        if ($oldStatus !== $complaint->status && in_array($complaint->status, ['selesai', 'ditolak'], true)) {
            $complaint->processed_at = now();
        } elseif ($oldStatus !== $complaint->status && $complaint->status === 'diproses' && !$complaint->processed_at) {
            $complaint->processed_at = now();
        }

        $complaint->save();
        ComplaintResponse::query()->create([
            'complaint_id' => $complaint->id,
            'user_id' => $request->user()?->id,
            'from_status' => $oldStatus,
            'to_status' => $complaint->status,
            'note' => $validated['status_note'] ?? null,
        ]);

        return redirect()
            ->route('admin.complaints.index', $request->query())
            ->with('status', 'Status pengaduan berhasil diperbarui.')
            ->with('complaint_modal_ticket', $complaint->ticket_code);
    }

    public function showRedirect(Complaint $complaint): RedirectResponse
    {
        return redirect()
            ->route('admin.complaints.index')
            ->with('complaint_modal_ticket', $complaint->ticket_code);
    }

    private function resolveFilters(Request $request): array
    {
        $status = (string) $request->query('status', '');
        $month = (int) $request->query('month', 0);
        $yearInput = (int) $request->query('year', 0);

        $currentYear = (int) now()->year;
        $year = $yearInput >= 2000 && $yearInput <= ($currentYear + 1) ? $yearInput : $currentYear;

        return [
            'status' => in_array($status, ['baru', 'diproses', 'selesai', 'ditolak'], true) ? $status : '',
            'month' => $month >= 1 && $month <= 12 ? $month : 0,
            'year' => $year,
        ];
    }

    private function filteredQuery(array $filters)
    {
        return Complaint::query()
            ->with(['village:id,name', 'responses.user:id,name'])
            ->when(($filters['year'] ?? 0) > 0, fn (Builder $query) => $query->whereYear('submitted_at', (int) $filters['year']))
            ->when(($filters['month'] ?? 0) > 0, fn (Builder $query) => $query->whereMonth('submitted_at', (int) $filters['month']))
            ->when($filters['status'] !== '', fn ($query) => $query->where('status', $filters['status']))
        ;
    }

    private function availableYears(): array
    {
        $years = Complaint::query()
            ->selectRaw('YEAR(submitted_at) as year_value')
            ->whereNotNull('submitted_at')
            ->distinct()
            ->orderByDesc('year_value')
            ->pluck('year_value')
            ->map(fn ($year) => (int) $year)
            ->filter(fn (int $year) => $year > 0)
            ->values()
            ->all();

        if ($years === []) {
            $years = [(int) now()->year];
        }

        return $years;
    }
}
