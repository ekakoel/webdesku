<?php

namespace Tests\Feature;

use App\Models\Village;
use App\Models\VillageHamlet;
use App\Models\VillageHouseholdWelfare;
use App\Services\DesilAnalysisService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DesilAnalysisTest extends TestCase
{
    use RefreshDatabase;

    public function test_desil_report_uses_database_records_for_aggregates_and_quality(): void
    {
        $village = Village::query()->create(['name' => 'Desa Uji', 'slug' => 'desa-uji', 'district' => 'Kecamatan Uji', 'city' => 'Kabupaten Uji', 'province' => 'Bali', 'address' => 'Jl. Uji']);
        $hamlet = VillageHamlet::query()->create(['village_id' => $village->id, 'name' => 'Puncak Sari', 'normalized_name' => 'PUCAK SARI']);
        foreach (['D1', 'D1', 'D2', 'D3', 'D4', 'D5', null] as $index => $decile) {
            VillageHouseholdWelfare::query()->create(['village_id' => $village->id, 'village_hamlet_id' => $index === 6 ? null : $hamlet->id, 'year' => 2026, 'reference_code' => 'R'.$index, 'decile' => $decile, 'is_published' => true, 'published_at' => now()]);
        }
        $report = app(DesilAnalysisService::class)->report($village, ['end_year' => 2026]);

        $this->assertSame(7, $report['totalHouseholds']);
        $this->assertSame(4, $report['priorityHouseholds']);
        $this->assertSame(5, $report['vulnerableHouseholds']);
        $this->assertSame(1, $report['quality']['invalid_decile']);
        $this->get('/analisis-desil?end_year=2026')->assertOk()->assertSee('Analisis Desil')->assertSee('7 KK');
        $this->get('/analisis-desil/pdf?end_year=2026')->assertOk()->assertHeader('content-type', 'application/pdf');
        $excel = $this->get('/analisis-desil/excel?end_year=2026');
        $excel->assertOk();
        $this->assertStringContainsString('Puncak Sari', $excel->streamedContent());
    }

    public function test_household_reference_is_unique_per_village_and_year(): void
    {
        $village = Village::query()->create(['name' => 'Desa Uji', 'slug' => 'desa-uji', 'district' => 'Kecamatan Uji', 'city' => 'Kabupaten Uji', 'province' => 'Bali', 'address' => 'Jl. Uji']);
        VillageHouseholdWelfare::query()->create(['village_id' => $village->id, 'year' => 2026, 'reference_code' => 'KK-001']);

        $this->expectException(UniqueConstraintViolationException::class);
        VillageHouseholdWelfare::query()->create(['village_id' => $village->id, 'year' => 2026, 'reference_code' => 'KK-001']);
    }
}
