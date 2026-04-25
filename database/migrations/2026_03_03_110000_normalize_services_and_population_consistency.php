<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->syncVillagePopulationSnapshot();
        $this->normalizeServicesSlugUniqueness();
    }

    public function down(): void
    {
        if (!Schema::hasTable('services')) {
            return;
        }

        Schema::table('services', function (Blueprint $table) {
            try {
                $table->dropUnique('services_village_slug_unique');
            } catch (\Throwable $e) {
            }
        });
    }

    private function syncVillagePopulationSnapshot(): void
    {
        if (!Schema::hasTable('villages') || !Schema::hasTable('village_populations')) {
            return;
        }

        if (
            !Schema::hasColumn('villages', 'population')
            || !Schema::hasColumn('villages', 'population_male')
            || !Schema::hasColumn('villages', 'population_female')
            || !Schema::hasColumn('villages', 'households')
            || !Schema::hasColumn('village_populations', 'village_id')
            || !Schema::hasColumn('village_populations', 'year')
            || !Schema::hasColumn('village_populations', 'male')
            || !Schema::hasColumn('village_populations', 'female')
            || !Schema::hasColumn('village_populations', 'households')
        ) {
            return;
        }

        $villages = DB::table('villages')->select('id')->get();
        foreach ($villages as $village) {
            $latest = DB::table('village_populations')
                ->where('village_id', $village->id)
                ->orderByDesc('year')
                ->orderByDesc('id')
                ->first(['male', 'female', 'households']);

            if (!$latest) {
                continue;
            }

            $male = (int) ($latest->male ?? 0);
            $female = (int) ($latest->female ?? 0);
            $households = (int) ($latest->households ?? 0);

            DB::table('villages')
                ->where('id', $village->id)
                ->update([
                    'population_male' => $male,
                    'population_female' => $female,
                    'population' => $male + $female,
                    'households' => $households,
                    'updated_at' => now(),
                ]);
        }
    }

    private function normalizeServicesSlugUniqueness(): void
    {
        if (!Schema::hasTable('services') || !Schema::hasColumn('services', 'village_id') || !Schema::hasColumn('services', 'slug')) {
            return;
        }

        // Drop old global unique slug index if exists.
        Schema::table('services', function (Blueprint $table) {
            try {
                $table->dropUnique('services_slug_unique');
            } catch (\Throwable $e) {
            }
        });

        // Ensure no duplicate slug inside the same village before adding composite unique.
        $duplicates = DB::table('services')
            ->select('village_id', 'slug', DB::raw('COUNT(*) as total'))
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->groupBy('village_id', 'slug')
            ->having('total', '>', 1)
            ->get();

        foreach ($duplicates as $duplicate) {
            $rows = DB::table('services')
                ->where('village_id', $duplicate->village_id)
                ->where('slug', $duplicate->slug)
                ->orderBy('id')
                ->get(['id', 'slug']);

            $keepFirst = true;
            foreach ($rows as $row) {
                if ($keepFirst) {
                    $keepFirst = false;
                    continue;
                }

                DB::table('services')
                    ->where('id', $row->id)
                    ->update(['slug' => $row->slug.'-'.$row->id]);
            }
        }

        Schema::table('services', function (Blueprint $table) {
            try {
                $table->unique(['village_id', 'slug'], 'services_village_slug_unique');
            } catch (\Throwable $e) {
            }
        });
    }
};

