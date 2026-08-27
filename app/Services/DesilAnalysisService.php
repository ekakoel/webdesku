<?php

namespace App\Services;

use App\Models\Village;
use App\Models\VillageHamlet;
use App\Models\VillageHouseholdWelfare;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class DesilAnalysisService
{
    public function report(?Village $village, array $filters = []): array
    {
        $years = $this->availableYears($village);
        $endYear = (int) ($filters['end_year'] ?? $years->last() ?? now()->year);
        $startYear = (int) ($filters['start_year'] ?? $endYear);
        $current = $this->query($village, $endYear, $endYear, $filters);
        $total = (clone $current)->count();
        $distribution = $this->distribution($current, $total);
        $quality = $this->quality($current);

        return [
            'village' => $village,
            'startYear' => $startYear,
            'endYear' => $endYear,
            'yearOptions' => $years->all(),
            'hamlets' => $this->hamlets($village),
            'filters' => $filters,
            'totalHouseholds' => $total,
            'distribution' => $distribution,
            'priorityHouseholds' => collect($distribution)->whereIn('decile', ['D1', 'D2', 'D3'])->sum('total'),
            'vulnerableHouseholds' => collect($distribution)->whereIn('decile', ['D1', 'D2', 'D3', 'D4'])->sum('total'),
            'hamletDistribution' => $this->hamletDistribution($current),
            'genderDistribution' => $this->genderDistribution($current, $total),
            'quality' => $quality,
            'qualityTotal' => $this->qualityTotal($current),
            'comparison' => $startYear < $endYear ? $this->comparison($village, $startYear, $endYear, $filters) : null,
            'generatedAt' => now(),
            'methodology' => [
                'Sumber data' => 'Tabel village_household_welfares, hanya data yang dipublikasikan.',
                'Klasifikasi' => 'D1 prioritas tertinggi; D2 prioritas tinggi; D3 prioritas; D4 rentan; D5 menuju sejahtera.',
                'Penggunaan' => 'Analisis ini tidak menetapkan kelayakan bantuan. Keputusan mengikuti verifikasi dan kebijakan program terkait.',
            ],
        ];
    }

    private function query(?Village $village, int $startYear, int $endYear, array $filters): Builder
    {
        return VillageHouseholdWelfare::query()
            ->when($village, fn (Builder $query) => $query->where('village_household_welfares.village_id', $village->id))
            ->where('village_household_welfares.is_published', true)
            ->whereBetween('village_household_welfares.year', [$startYear, $endYear])
            ->when($filters['hamlet_id'] ?? null, fn (Builder $query, $id) => $query->where('village_household_welfares.village_hamlet_id', $id))
            ->when($filters['decile'] ?? null, fn (Builder $query, $decile) => $query->where('village_household_welfares.decile', $decile))
            ->when($filters['head_gender'] ?? null, fn (Builder $query, $gender) => $query->where('village_household_welfares.head_gender', $gender));
    }

    private function availableYears(?Village $village): Collection
    {
        return VillageHouseholdWelfare::query()->when($village, fn (Builder $query) => $query->where('village_id', $village->id))
            ->where('is_published', true)->distinct()->orderBy('year')->pluck('year')->map(fn ($year) => (int) $year);
    }

    private function distribution(Builder $query, int $total): array
    {
        $counts = (clone $query)->whereIn('decile', VillageHouseholdWelfare::DECILES)->selectRaw('decile, COUNT(*) total')->groupBy('decile')->pluck('total', 'decile');

        return collect(VillageHouseholdWelfare::DECILES)->map(fn (string $decile) => [
            'decile' => $decile, 'label' => $this->decileLabel($decile), 'total' => (int) ($counts[$decile] ?? 0),
            'percentage' => $total > 0 ? round(((int) ($counts[$decile] ?? 0) / $total) * 100, 2) : 0,
        ])->all();
    }

    private function hamletDistribution(Builder $query): Collection
    {
        return (clone $query)->leftJoin('village_hamlets', 'village_household_welfares.village_hamlet_id', '=', 'village_hamlets.id')
            ->selectRaw("COALESCE(village_hamlets.name, 'Belum ditetapkan') hamlet, SUM(CASE WHEN decile = 'D1' THEN 1 ELSE 0 END) d1, SUM(CASE WHEN decile = 'D2' THEN 1 ELSE 0 END) d2, SUM(CASE WHEN decile = 'D3' THEN 1 ELSE 0 END) d3, SUM(CASE WHEN decile = 'D4' THEN 1 ELSE 0 END) d4, SUM(CASE WHEN decile = 'D5' THEN 1 ELSE 0 END) d5, COUNT(*) total")
            ->groupBy('village_hamlets.id', 'village_hamlets.name')->get()->map(function ($row) {
                $row->priority_percentage = $row->total > 0 ? round((($row->d1 + $row->d2 + $row->d3) / $row->total) * 100, 2) : 0;

                return $row;
            })->sortByDesc('priority_percentage')->values();
    }

    private function genderDistribution(Builder $query, int $total): Collection
    {
        return (clone $query)->whereNotNull('head_gender')->selectRaw('head_gender, decile, COUNT(*) total')->groupBy('head_gender', 'decile')->get()
            ->groupBy('head_gender')->map(function (Collection $rows, string $gender) use ($total) {
                $genderTotal = (int) $rows->sum('total');

                return ['gender' => VillageHouseholdWelfare::GENDERS[$gender] ?? $gender, 'total' => $genderTotal, 'percentage' => $total > 0 ? round(($genderTotal / $total) * 100, 2) : 0, 'items' => $rows->pluck('total', 'decile')];
            })->values();
    }

    private function quality(Builder $query): array
    {
        return ['missing_hamlet' => (clone $query)->whereNull('village_hamlet_id')->count(), 'invalid_decile' => (clone $query)->where(fn (Builder $q) => $q->whereNull('decile')->orWhereNotIn('decile', VillageHouseholdWelfare::DECILES))->count(), 'outside_village' => (clone $query)->where('is_outside_village', true)->count(), 'requires_verification' => (clone $query)->where('requires_verification', true)->count(), 'non_standard_hamlet' => 0];
    }

    private function qualityTotal(Builder $query): int
    {
        return (clone $query)->where(function (Builder $qualityQuery) {
            $qualityQuery->whereNull('village_hamlet_id')
                ->orWhereNull('decile')
                ->orWhereNotIn('decile', VillageHouseholdWelfare::DECILES)
                ->orWhere('is_outside_village', true)
                ->orWhere('requires_verification', true);
        })->count();
    }

    private function comparison(?Village $village, int $startYear, int $endYear, array $filters): array
    {
        $from = $this->distribution($this->query($village, $startYear, $startYear, $filters), $this->query($village, $startYear, $startYear, $filters)->count());
        $to = $this->distribution($this->query($village, $endYear, $endYear, $filters), $this->query($village, $endYear, $endYear, $filters)->count());

        return collect($to)->map(fn (array $row, int $index) => ['decile' => $row['decile'], 'from' => $from[$index]['total'], 'to' => $row['total'], 'change' => $row['total'] - $from[$index]['total']])->all();
    }

    private function hamlets(?Village $village): Collection
    {
        return VillageHamlet::query()->when($village, fn (Builder $q) => $q->where('village_id', $village->id))->where('is_active', true)->orderBy('name')->get();
    }

    private function decileLabel(string $decile): string
    {
        return ['D1' => 'Prioritas tertinggi', 'D2' => 'Prioritas tinggi', 'D3' => 'Prioritas', 'D4' => 'Rentan', 'D5' => 'Menuju sejahtera'][$decile];
    }
}
