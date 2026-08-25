<?php

namespace App\Services;

use App\Models\Agenda;
use App\Models\Announcement;
use App\Models\Complaint;
use App\Models\Gallery;
use App\Models\News;
use App\Models\ServiceRequest;
use App\Models\Village;
use App\Models\VillageAsset;
use App\Models\VillageInfographicItem;
use App\Models\VillagePopulation;
use App\Models\VillageService;
use App\Support\ModuleManager;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StatisticsService
{
    public function report(?Village $village, ?int $startYear = null, ?int $endYear = null): array
    {
        $moduleStates = $this->moduleStates();
        $yearOptions = $this->availableYears($village, $moduleStates);
        $defaultYear = (int) ($yearOptions->filter(fn (int $year) => $year <= now()->year)->last() ?? now()->year);
        $startYear = $startYear ?: $defaultYear;
        $endYear = $endYear ?: $startYear;

        if ($startYear > $endYear) {
            [$startYear, $endYear] = [$endYear, $startYear];
        }

        $periodStart = Carbon::create($startYear, 1, 1)->startOfDay();
        $periodEnd = Carbon::create($endYear, 12, 31)->endOfDay();
        $periodLabel = $startYear === $endYear
            ? "Tahun {$startYear}"
            : "Tahun {$startYear} s.d. {$endYear}";
        $periodDateLabel = $periodStart->translatedFormat('d F Y').' s.d. '.$periodEnd->translatedFormat('d F Y');

        $population = $this->populationSnapshot($village, $startYear, $endYear);
        $periodicCounts = $this->periodicCounts($village, $moduleStates, $periodStart, $periodEnd);
        $masterCounts = $this->masterCounts($village, $moduleStates);
        $trend = $this->trend($village, $moduleStates, $startYear, $endYear);
        $complaintByStatus = $this->complaintByStatus($village, $moduleStates, $periodStart, $periodEnd);
        $complaintByCategory = $this->complaintByCategory($village, $moduleStates, $periodStart, $periodEnd);
        $assetTypeStats = $this->assetTypeStats($village, $moduleStates);
        $infographicIndicators = $this->infographicIndicators($village, $moduleStates, $startYear, $endYear);

        $kpis = collect([
            [
                'label' => 'Total Penduduk',
                'value' => $population['available']
                    ? number_format((int) $population['total'], 0, ',', '.').' Jiwa'
                    : 'Tidak tersedia',
                'scope' => $population['scope_label'],
                'type' => 'snapshot',
            ],
            ...($moduleStates['complaints'] ? [[
                'label' => 'Aduan Periode Ini',
                'value' => number_format($periodicCounts['complaints'], 0, ',', '.'),
                'scope' => $periodLabel,
                'type' => 'periodic',
            ]] : []),
            ...($moduleStates['services'] && Schema::hasTable('service_requests') ? [[
                'label' => 'Pengajuan Layanan',
                'value' => number_format($periodicCounts['service_requests'], 0, ',', '.'),
                'scope' => $periodLabel,
                'type' => 'periodic',
            ]] : []),
            ...($moduleStates['news'] ? [[
                'label' => 'Berita Periode Ini',
                'value' => number_format($periodicCounts['news'], 0, ',', '.'),
                'scope' => $periodLabel,
                'type' => 'periodic',
            ]] : []),
            ...($moduleStates['agendas'] ? [[
                'label' => 'Agenda Periode Ini',
                'value' => number_format($periodicCounts['agendas'], 0, ',', '.'),
                'scope' => $periodLabel,
                'type' => 'periodic',
            ]] : []),
            ...($moduleStates['announcements'] ? [[
                'label' => 'Pengumuman Periode Ini',
                'value' => number_format($periodicCounts['announcements'], 0, ',', '.'),
                'scope' => $periodLabel,
                'type' => 'periodic',
            ]] : []),
            ...($moduleStates['galleries'] ? [[
                'label' => 'Galeri Periode Ini',
                'value' => number_format($periodicCounts['galleries'], 0, ',', '.'),
                'scope' => $periodLabel,
                'type' => 'periodic',
            ]] : []),
            ...($moduleStates['services'] ? [[
                'label' => 'Layanan Aktif',
                'value' => number_format($masterCounts['services'], 0, ',', '.'),
                'scope' => 'Data terkini',
                'type' => 'master',
            ]] : []),
            ...($moduleStates['infographics'] ? [[
                'label' => 'Total Aset Desa',
                'value' => number_format($masterCounts['assets'], 0, ',', '.'),
                'scope' => 'Data terkini',
                'type' => 'master',
            ]] : []),
        ])->values()->all();

        return [
            'village' => $village,
            'startYear' => $startYear,
            'endYear' => $endYear,
            'periodStart' => $periodStart,
            'periodEnd' => $periodEnd,
            'periodLabel' => $periodLabel,
            'periodDateLabel' => $periodDateLabel,
            'yearOptions' => $yearOptions->all(),
            'kpis' => $kpis,
            'periodicCounts' => $periodicCounts,
            'masterCounts' => $masterCounts,
            'complaintByStatus' => $complaintByStatus,
            'complaintByCategory' => $complaintByCategory,
            'trend' => $trend,
            'monthly' => $trend,
            'population' => $population,
            'moduleStates' => $moduleStates,
            'assetTypeStats' => $assetTypeStats,
            'infographicIndicators' => $infographicIndicators,
            'hasPeriodData' => collect($periodicCounts)->sum() > 0,
            'generatedAt' => now(),
            'methodology' => $this->methodology(),
        ];
    }

    public function filename(array $report): string
    {
        $suffix = $report['startYear'] === $report['endYear']
            ? (string) $report['startYear']
            : $report['startYear'].'-'.$report['endYear'];

        return "laporan-statistik-desa-{$suffix}.pdf";
    }

    public function excelFilename(array $report): string
    {
        $suffix = $report['startYear'] === $report['endYear']
            ? (string) $report['startYear']
            : $report['startYear'].'-'.$report['endYear'];

        return "laporan-statistik-desa-{$suffix}.xls";
    }

    private function moduleStates(): array
    {
        return collect(ModuleManager::allModules())
            ->mapWithKeys(fn (string $label, string $key) => [$key => ModuleManager::isEnabled($key)])
            ->all();
    }

    private function availableYears(?Village $village, array $moduleStates): Collection
    {
        $years = collect([now()->year]);

        if ($moduleStates['news'] && Schema::hasTable('news') && Schema::hasColumn('news', 'published_at')) {
            $years = $years->merge($this->dateYears($this->publishedNewsQuery($village), 'published_at'));
        }

        if ($moduleStates['agendas'] && Schema::hasTable('agendas') && Schema::hasColumn('agendas', 'start_at')) {
            $years = $years->merge($this->dateYears($this->publishedAgendasQuery($village), 'start_at'));
        }

        if ($moduleStates['announcements'] && Schema::hasTable('announcements') && Schema::hasColumn('announcements', 'published_at')) {
            $years = $years->merge($this->dateYears($this->publishedAnnouncementsQuery($village), 'published_at'));
        }

        if ($moduleStates['galleries'] && Schema::hasTable('galleries') && Schema::hasColumn('galleries', 'published_at')) {
            $years = $years->merge($this->dateYears($this->publishedGalleriesQuery($village), 'published_at'));
        }

        if ($moduleStates['complaints'] && Schema::hasTable('complaints') && Schema::hasColumn('complaints', 'submitted_at')) {
            $years = $years->merge($this->dateYears($this->complaintsQuery($village), 'submitted_at'));
        }

        if ($moduleStates['services'] && Schema::hasTable('service_requests') && Schema::hasColumn('service_requests', 'submitted_at')) {
            $years = $years->merge($this->dateYears($this->serviceRequestsQuery($village), 'submitted_at'));
        }

        if (Schema::hasTable('village_populations') && Schema::hasColumn('village_populations', 'year')) {
            $years = $years->merge($this->publishedPopulationQuery($village)->pluck('year'));
        }

        if (Schema::hasTable('village_infographic_items') && Schema::hasColumn('village_infographic_items', 'year')) {
            $years = $years->merge($this->publishedInfographicIndicatorsQuery($village)->whereNotNull('year')->pluck('year'));
        }

        $max = now()->year + 1;

        return $years
            ->map(fn ($year) => (int) $year)
            ->filter(fn (int $year) => $year >= 2000 && $year <= $max)
            ->unique()
            ->sort()
            ->values();
    }

    private function dateYears(Builder $query, string $column): Collection
    {
        $wrappedColumn = DB::connection()->getQueryGrammar()->wrap($column);
        $expression = DB::connection()->getDriverName() === 'sqlite'
            ? "CAST(strftime('%Y', {$wrappedColumn}) AS INTEGER)"
            : "YEAR({$wrappedColumn})";

        return $query
            ->whereNotNull($column)
            ->selectRaw("{$expression} as year")
            ->distinct()
            ->pluck('year');
    }

    private function periodicCounts(?Village $village, array $moduleStates, Carbon $start, Carbon $end): array
    {
        return [
            'news' => $moduleStates['news'] && Schema::hasTable('news')
                ? $this->betweenDates($this->publishedNewsQuery($village), 'published_at', $start, $end)->count()
                : 0,
            'agendas' => $moduleStates['agendas'] && Schema::hasTable('agendas')
                ? $this->betweenDates($this->publishedAgendasQuery($village), 'start_at', $start, $end)->count()
                : 0,
            'announcements' => $moduleStates['announcements'] && Schema::hasTable('announcements')
                ? $this->betweenDates($this->publishedAnnouncementsQuery($village), 'published_at', $start, $end)->count()
                : 0,
            'galleries' => $moduleStates['galleries'] && Schema::hasTable('galleries')
                ? $this->betweenDates($this->publishedGalleriesQuery($village), 'published_at', $start, $end)->count()
                : 0,
            'complaints' => $moduleStates['complaints'] && Schema::hasTable('complaints')
                ? $this->betweenDates($this->complaintsQuery($village), 'submitted_at', $start, $end)->count()
                : 0,
            'service_requests' => $moduleStates['services'] && Schema::hasTable('service_requests')
                ? $this->betweenDates($this->serviceRequestsQuery($village), 'submitted_at', $start, $end)->count()
                : 0,
        ];
    }

    private function masterCounts(?Village $village, array $moduleStates): array
    {
        return [
            'services' => $moduleStates['services'] && Schema::hasTable('services')
                ? $this->publishedServicesQuery($village)->count()
                : 0,
            'assets' => $moduleStates['infographics'] && Schema::hasTable('village_assets')
                ? $this->publishedAssetsQuery($village)->count()
                : 0,
        ];
    }

    private function populationSnapshot(?Village $village, int $startYear, int $endYear): array
    {
        $snapshot = null;

        if ($village && Schema::hasTable('village_populations')) {
            $snapshot = $this->publishedPopulationQuery($village)
                ->whereBetween('year', [$startYear, $endYear])
                ->orderByDesc('year')
                ->orderBy('sort_order')
                ->first();
        }

        if ($snapshot) {
            return [
                'available' => true,
                'male' => (int) $snapshot->male,
                'female' => (int) $snapshot->female,
                'households' => (int) $snapshot->households,
                'total' => (int) $snapshot->total(),
                'year' => (int) $snapshot->year,
                'scope_label' => 'Snapshot tahun '.$snapshot->year,
            ];
        }

        if ($village && ! Schema::hasTable('village_populations')) {
            $male = (int) ($village->population_male ?? 0);
            $female = (int) ($village->population_female ?? 0);
            $total = $male + $female;
            if ($total <= 0) {
                $total = (int) ($village->population ?? 0);
            }

            return [
                'available' => $total > 0 || (int) ($village->households ?? 0) > 0,
                'male' => $male,
                'female' => $female,
                'households' => (int) ($village->households ?? 0),
                'total' => $total,
                'year' => null,
                'scope_label' => 'Data terkini',
            ];
        }

        return [
            'available' => false,
            'male' => 0,
            'female' => 0,
            'households' => 0,
            'total' => 0,
            'year' => null,
            'scope_label' => "Tidak tersedia untuk {$startYear}".($startYear === $endYear ? '' : " s.d. {$endYear}"),
        ];
    }

    private function trend(?Village $village, array $moduleStates, int $startYear, int $endYear): Collection
    {
        if ($startYear === $endYear) {
            $year = $startYear;

            return collect(range(1, 12))->map(function (int $month) use ($village, $moduleStates, $year) {
                $start = Carbon::create($year, $month, 1)->startOfDay();
                $end = $start->copy()->endOfMonth()->endOfDay();

                return [
                    'label' => $start->translatedFormat('M'),
                    ...$this->periodicCounts($village, $moduleStates, $start, $end),
                ];
            });
        }

        return collect(range($startYear, $endYear))->map(function (int $year) use ($village, $moduleStates) {
            return [
                'label' => (string) $year,
                ...$this->periodicCounts(
                    $village,
                    $moduleStates,
                    Carbon::create($year, 1, 1)->startOfDay(),
                    Carbon::create($year, 12, 31)->endOfDay()
                ),
            ];
        });
    }

    private function complaintByStatus(?Village $village, array $moduleStates, Carbon $start, Carbon $end): Collection
    {
        if (! $moduleStates['complaints'] || ! Schema::hasTable('complaints')) {
            return collect();
        }

        return $this->betweenDates($this->complaintsQuery($village), 'submitted_at', $start, $end)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
    }

    private function complaintByCategory(?Village $village, array $moduleStates, Carbon $start, Carbon $end): Collection
    {
        if (! $moduleStates['complaints'] || ! Schema::hasTable('complaints')) {
            return collect();
        }

        return $this->betweenDates($this->complaintsQuery($village), 'submitted_at', $start, $end)
            ->selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(8)
            ->get();
    }

    private function assetTypeStats(?Village $village, array $moduleStates): Collection
    {
        if (! $moduleStates['infographics'] || ! Schema::hasTable('village_assets')) {
            return collect();
        }

        $assetCountsByType = $this->publishedAssetsQuery($village)
            ->selectRaw('type, COUNT(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        return collect(VillageAsset::typeOptions())
            ->map(function (array $meta, string $type) use ($assetCountsByType) {
                return [
                    'type' => $type,
                    'label' => $meta['label'],
                    'color' => $meta['color'],
                    'total' => (int) ($assetCountsByType[$type] ?? 0),
                ];
            })
            ->filter(fn (array $row) => $row['total'] > 0)
            ->values();
    }

    private function infographicIndicators(?Village $village, array $moduleStates, int $startYear, int $endYear): Collection
    {
        if (! $moduleStates['infographics'] || ! Schema::hasTable('village_infographic_items')) {
            return collect();
        }

        $query = $this->publishedInfographicIndicatorsQuery($village)
            ->orderBy('category')
            ->orderByDesc('year')
            ->orderBy('sort_order')
            ->orderBy('title');

        if (Schema::hasColumn('village_infographic_items', 'year')) {
            $query->where(function (Builder $builder) use ($startYear, $endYear) {
                $builder->whereNull('year')
                    ->orWhereBetween('year', [$startYear, $endYear]);
            });
        }

        return $query
            ->get()
            ->groupBy('category')
            ->map(function (Collection $items, string $category) {
                return [
                    'category' => $category,
                    'label' => VillageInfographicItem::categoryOptions()[$category] ?? str($category)->headline()->toString(),
                    'items' => $items->map(fn (VillageInfographicItem $item) => [
                        'title' => $item->title,
                        'value' => $item->value,
                        'unit' => $item->unit,
                        'year' => $item->year,
                        'source' => $item->source,
                        'notes' => $item->notes,
                        'description' => $item->description,
                    ])->values(),
                ];
            })
            ->values();
    }

    private function betweenDates(Builder $query, string $column, Carbon $start, Carbon $end): Builder
    {
        return $query->whereNotNull($column)->whereBetween($column, [$start, $end]);
    }

    private function publishedNewsQuery(?Village $village): Builder
    {
        return News::query()
            ->when(Schema::hasColumn('news', 'is_published'), fn (Builder $query) => $query->where('is_published', true))
            ->when($village && Schema::hasColumn('news', 'village_id'), fn (Builder $query) => $query->where('village_id', $village->id));
    }

    private function publishedAgendasQuery(?Village $village): Builder
    {
        return Agenda::query()
            ->when(Schema::hasColumn('agendas', 'is_published'), fn (Builder $query) => $query->where('is_published', true))
            ->when($village && Schema::hasColumn('agendas', 'village_id'), fn (Builder $query) => $query->where('village_id', $village->id));
    }

    private function publishedAnnouncementsQuery(?Village $village): Builder
    {
        return Announcement::query()
            ->when(Schema::hasColumn('announcements', 'is_published'), fn (Builder $query) => $query->where('is_published', true))
            ->when($village && Schema::hasColumn('announcements', 'village_id'), fn (Builder $query) => $query->where('village_id', $village->id));
    }

    private function publishedGalleriesQuery(?Village $village): Builder
    {
        return Gallery::query()
            ->when(Schema::hasColumn('galleries', 'is_published'), fn (Builder $query) => $query->where('is_published', true))
            ->when($village && Schema::hasColumn('galleries', 'village_id'), fn (Builder $query) => $query->where('village_id', $village->id));
    }

    private function publishedServicesQuery(?Village $village): Builder
    {
        return VillageService::query()
            ->when(Schema::hasColumn('services', 'is_published'), fn (Builder $query) => $query->where('is_published', true))
            ->when($village && Schema::hasColumn('services', 'village_id'), fn (Builder $query) => $query->where('village_id', $village->id));
    }

    private function publishedAssetsQuery(?Village $village): Builder
    {
        return VillageAsset::query()
            ->when(Schema::hasColumn('village_assets', 'is_published'), fn (Builder $query) => $query->where('is_published', true))
            ->when($village && Schema::hasColumn('village_assets', 'village_id'), fn (Builder $query) => $query->where('village_id', $village->id));
    }

    private function publishedPopulationQuery(?Village $village): Builder
    {
        return VillagePopulation::query()
            ->when(Schema::hasColumn('village_populations', 'is_published'), fn (Builder $query) => $query->where('is_published', true))
            ->when($village && Schema::hasColumn('village_populations', 'village_id'), fn (Builder $query) => $query->where('village_id', $village->id));
    }

    private function publishedInfographicIndicatorsQuery(?Village $village): Builder
    {
        return VillageInfographicItem::query()
            ->when(Schema::hasColumn('village_infographic_items', 'is_published'), fn (Builder $query) => $query->where('is_published', true))
            ->when($village && Schema::hasColumn('village_infographic_items', 'village_id'), fn (Builder $query) => $query->where('village_id', $village->id));
    }

    private function complaintsQuery(?Village $village): Builder
    {
        return Complaint::query()
            ->when($village && Schema::hasColumn('complaints', 'village_id'), fn (Builder $query) => $query->where('village_id', $village->id));
    }

    private function serviceRequestsQuery(?Village $village): Builder
    {
        return ServiceRequest::query()
            ->when($village && Schema::hasColumn('service_requests', 'village_id'), fn (Builder $query) => $query->where('village_id', $village->id));
    }

    private function methodology(): array
    {
        return [
            'Berita' => 'Tanggal publikasi (`news.published_at`).',
            'Agenda' => 'Tanggal kegiatan mulai (`agendas.start_at`).',
            'Pengumuman' => 'Tanggal publikasi (`announcements.published_at`).',
            'Galeri' => 'Tanggal publikasi (`galleries.published_at`).',
            'Pengaduan' => 'Tanggal pengajuan (`complaints.submitted_at`).',
            'Pengajuan layanan' => 'Tanggal pengajuan (`service_requests.submitted_at`).',
            'Penduduk' => 'Snapshot tahun pada `village_populations.year`; jika tidak ada pada periode terpilih, data historis periode dinyatakan tidak tersedia.',
            'Layanan aktif dan aset desa' => 'Data master terkini, tidak dipaksa mengikuti filter periode.',
            'Indikator agregat lainnya' => 'Data agregat non-personal dari `village_infographic_items`; data bertahun mengikuti filter periode, data tanpa tahun diperlakukan sebagai data terkini.',
        ];
    }
}
