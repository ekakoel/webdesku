<?php

namespace App\Http\Controllers;

use App\Http\Requests\DesilAnalysisFilterRequest;
use App\Models\Village;
use App\Services\DesilAnalysisService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DesilAnalysisController extends Controller
{
    public function index(DesilAnalysisFilterRequest $request, DesilAnalysisService $service): View
    {
        return view('web.desil.index', $this->report($request, $service));
    }

    public function pdf(DesilAnalysisFilterRequest $request, DesilAnalysisService $service)
    {
        $report = $this->report($request, $service);

        return Pdf::loadView('pdf.desil-report', $report)->setPaper('a4', 'landscape')->download('laporan-analisis-desil-'.$report['endYear'].'.pdf');
    }

    public function excel(DesilAnalysisFilterRequest $request, DesilAnalysisService $service): StreamedResponse
    {
        $report = $this->report($request, $service);

        return response()->streamDownload(function () use ($report) { ?>
            <table border="1">
                <tr>
                    <th colspan="3">Analisis Desil Kesejahteraan</th>
                </tr>
                <tr>
                    <td>Desa</td>
                    <td colspan="2"><?= e($report['village']?->name ?? 'Pemerintah Desa') ?></td>
                </tr>
                <tr>
                    <td>Periode</td>
                    <td colspan="2"><?= $report['startYear'] ?><?= $report['startYear'] !== $report['endYear'] ? ' s.d. '.$report['endYear'] : '' ?></td>
                </tr>
                <tr>
                    <th>Desil</th>
                    <th>KK</th>
                    <th>Persentase</th>
                </tr><?php foreach ($report['distribution'] as $row) { ?><tr>
                        <td><?= $row['decile'] ?></td>
                        <td><?= $row['total'] ?></td>
                        <td><?= $row['percentage'] ?>%</td>
                    </tr><?php } ?>
            </table><br>
            <table border="1">
                <tr>
                    <th>Jenis Kelamin Kepala KK</th>
                    <th>Total</th>
                    <th>Persentase</th>
                    <th>D1</th>
                    <th>D2</th>
                    <th>D3</th>
                    <th>D4</th>
                    <th>D5</th>
                </tr><?php foreach ($report['genderDistribution'] as $row) { ?><tr>
                        <td><?= e($row['gender']) ?></td>
                        <td><?= $row['total'] ?></td>
                        <td><?= $row['percentage'] ?>%</td><?php foreach (['D1', 'D2', 'D3', 'D4', 'D5'] as $decile) { ?><td><?= $row['items'][$decile] ?? 0 ?></td><?php } ?>
                    </tr><?php } ?>
            </table><br>
            <table border="1">
                <tr>
                    <th>Banjar</th>
                    <th>D1</th>
                    <th>D2</th>
                    <th>D3</th>
                    <th>D4</th>
                    <th>D5</th>
                    <th>Total</th>
                    <th>D1-D3</th>
                </tr><?php foreach ($report['hamletDistribution'] as $row) { ?><tr>
                        <td><?= e($row->hamlet) ?></td>
                        <td><?= $row->d1 ?></td>
                        <td><?= $row->d2 ?></td>
                        <td><?= $row->d3 ?></td>
                        <td><?= $row->d4 ?></td>
                        <td><?= $row->d5 ?></td>
                        <td><?= $row->total ?></td>
                        <td><?= $row->priority_percentage ?>%</td>
                    </tr><?php } ?>
            </table><?php if ($report['comparison']) { ?><br>
                <table border="1">
                    <tr>
                        <th>Perubahan Distribusi Data Desil</th>
                        <th><?= $report['startYear'] ?></th>
                        <th><?= $report['endYear'] ?></th>
                        <th>Perubahan</th>
                    </tr><?php foreach ($report['comparison'] as $row) { ?><tr>
                            <td><?= $row['decile'] ?></td>
                            <td><?= $row['from'] ?></td>
                            <td><?= $row['to'] ?></td>
                            <td><?= $row['change'] ?></td>
                        </tr><?php } ?>
                </table><?php } ?><br>
            <table border="1">
                <tr>
                    <th>Kualitas Data</th>
                    <th>Jumlah</th>
                </tr><?php foreach ($report['quality'] as $label => $value) { ?><tr>
                        <td><?= e(str_replace('_', ' ', $label)) ?></td>
                        <td><?= $value ?></td>
                    </tr><?php } ?>
            </table><?php }, 'analisis-desil-'.$report['endYear'].'.xls', ['Content-Type' => 'application/vnd.ms-excel; charset=UTF-8']);
    }

    private function report(DesilAnalysisFilterRequest $request, DesilAnalysisService $service): array
    {
        return $service->report(app()->bound('currentVillage') ? app('currentVillage') : Village::query()->first(), $request->validated());
    }
}
