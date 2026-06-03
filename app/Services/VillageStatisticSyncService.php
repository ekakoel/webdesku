<?php

namespace App\Services;

use App\Models\Village;
use App\Models\VillageApbdesItem;
use App\Models\VillageAsset;
use App\Models\VillagePopulation;

class VillageStatisticSyncService
{
    public function syncPopulationSummary(Village $village): void
    {
        $latest = VillagePopulation::query()
            ->where('village_id', $village->id)
            ->where('is_published', true)
            ->orderByDesc('year')
            ->orderBy('sort_order')
            ->first();

        $male = (int) ($latest?->male ?? 0);
        $female = (int) ($latest?->female ?? 0);
        $households = (int) ($latest?->households ?? 0);

        $village->forceFill([
            'population_male' => $male,
            'population_female' => $female,
            'population' => $male + $female,
            'households' => $households,
        ])->save();
    }

    public function syncApbdesSummary(Village $village): void
    {
        $latestYear = VillageApbdesItem::query()
            ->where('village_id', $village->id)
            ->where('is_published', true)
            ->max('fiscal_year');

        if (!$latestYear) {
            $village->forceFill([
                'apb_income' => 0,
                'apb_expense' => 0,
                'apb_financing' => 0,
            ])->save();
            return;
        }

        $totals = VillageApbdesItem::query()
            ->where('village_id', $village->id)
            ->where('is_published', true)
            ->where('fiscal_year', (int) $latestYear)
            ->selectRaw('type, SUM(amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $village->forceFill([
            'apb_income' => (int) ($totals['pendapatan'] ?? 0),
            'apb_expense' => (int) ($totals['belanja'] ?? 0),
            'apb_financing' => (int) ($totals['pembiayaan'] ?? 0),
        ])->save();
    }

    public function latestAssetSummary(Village $village): array
    {
        $totalAssets = VillageAsset::query()
            ->where('village_id', $village->id)
            ->where('is_published', true)
            ->count();

        $byType = VillageAsset::query()
            ->where('village_id', $village->id)
            ->where('is_published', true)
            ->selectRaw('type, COUNT(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type')
            ->map(fn ($value) => (int) $value)
            ->toArray();

        return [
            'total' => $totalAssets,
            'by_type' => $byType,
        ];
    }

    public function latestApbdesSummary(Village $village): array
    {
        $latestYear = VillageApbdesItem::query()
            ->where('village_id', $village->id)
            ->where('is_published', true)
            ->max('fiscal_year');

        if (!$latestYear) {
            return [
                'year' => null,
                'pendapatan' => 0,
                'belanja' => 0,
                'pembiayaan' => 0,
            ];
        }

        $totals = VillageApbdesItem::query()
            ->where('village_id', $village->id)
            ->where('is_published', true)
            ->where('fiscal_year', (int) $latestYear)
            ->selectRaw('type, SUM(amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        return [
            'year' => (int) $latestYear,
            'pendapatan' => (int) ($totals['pendapatan'] ?? 0),
            'belanja' => (int) ($totals['belanja'] ?? 0),
            'pembiayaan' => (int) ($totals['pembiayaan'] ?? 0),
        ];
    }
}
