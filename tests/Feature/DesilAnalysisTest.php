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
        $this->get('/analisis-desil?end_year=2026')
            ->assertOk()
            ->assertSee('Analisis Desil')
            ->assertSee('7 KK')
            ->assertDontSee('R0');
        $this->get('/analisis-desil/pdf?end_year=2026')->assertOk()->assertHeader('content-type', 'application/pdf');
        $excel = $this->get('/analisis-desil/excel?end_year=2026');
        $excel->assertOk();
        $excelContent = $excel->streamedContent();
        $this->assertStringContainsString('Puncak Sari', $excelContent);
        $this->assertStringNotContainsString('R0', $excelContent);
    }

    public function test_public_outputs_expose_gender_distribution_and_year_comparison_without_reference_codes(): void
    {
        $village = Village::query()->create(['name' => 'Desa Uji', 'slug' => 'desa-uji', 'district' => 'Kecamatan Uji', 'city' => 'Kabupaten Uji', 'province' => 'Bali', 'address' => 'Jl. Uji']);
        $hamlet = VillageHamlet::query()->create(['village_id' => $village->id, 'name' => 'Puncak Sari', 'normalized_name' => 'PUCAK SARI']);

        VillageHouseholdWelfare::query()->create(['village_id' => $village->id, 'village_hamlet_id' => $hamlet->id, 'year' => 2025, 'reference_code' => 'PRIVATE-2025-A', 'decile' => 'D1', 'head_gender' => 'laki_laki', 'is_published' => true, 'published_at' => now()]);
        VillageHouseholdWelfare::query()->create(['village_id' => $village->id, 'village_hamlet_id' => $hamlet->id, 'year' => 2026, 'reference_code' => 'PRIVATE-2026-A', 'decile' => 'D2', 'head_gender' => 'perempuan', 'is_published' => true, 'published_at' => now()]);
        VillageHouseholdWelfare::query()->create(['village_id' => $village->id, 'village_hamlet_id' => $hamlet->id, 'year' => 2026, 'reference_code' => 'PRIVATE-2026-B', 'decile' => 'D2', 'head_gender' => 'perempuan', 'is_published' => true, 'published_at' => now()]);

        $report = app(DesilAnalysisService::class)->report($village, ['start_year' => 2025, 'end_year' => 2026]);

        $this->assertSame('Perempuan', $report['genderDistribution']->first()['gender']);
        $this->assertSame(2, $report['genderDistribution']->first()['total']);
        $this->assertSame(['decile' => 'D1', 'from' => 1, 'to' => 0, 'change' => -1], $report['comparison'][0]);
        $this->assertSame(['decile' => 'D2', 'from' => 0, 'to' => 2, 'change' => 2], $report['comparison'][1]);

        $pdfHtml = view('pdf.desil-report', $report)->render();
        $this->assertStringContainsString('Profil Kepala Keluarga', $pdfHtml);
        $this->assertStringContainsString('Perubahan Distribusi Data Desil', $pdfHtml);
        $this->assertStringNotContainsString('PRIVATE-2026-A', $pdfHtml);

        $this->get('/analisis-desil?start_year=2025&end_year=2026')
            ->assertOk()
            ->assertSee('Profil Kepala Keluarga')
            ->assertSee('Perempuan')
            ->assertSee('Perubahan Distribusi Data Desil')
            ->assertDontSee('PRIVATE-2026-A');

        $excelContent = $this->get('/analisis-desil/excel?start_year=2025&end_year=2026')->streamedContent();

        $this->assertStringContainsString('Jenis Kelamin Kepala KK', $excelContent);
        $this->assertStringContainsString('Perempuan', $excelContent);
        $this->assertStringContainsString('Perubahan Distribusi Data Desil', $excelContent);
        $this->assertStringNotContainsString('PRIVATE-2026-A', $excelContent);
    }

    public function test_data_quality_flags_records_requiring_verification(): void
    {
        $village = Village::query()->create(['name' => 'Desa Uji', 'slug' => 'desa-uji', 'district' => 'Kecamatan Uji', 'city' => 'Kabupaten Uji', 'province' => 'Bali', 'address' => 'Jl. Uji']);

        VillageHouseholdWelfare::query()->create(['village_id' => $village->id, 'year' => 2026, 'reference_code' => 'QUALITY-001', 'decile' => 'D1', 'head_gender' => 'perempuan', 'requires_verification' => true, 'is_published' => true, 'published_at' => now()]);

        $report = app(DesilAnalysisService::class)->report($village, ['end_year' => 2026]);

        $this->assertSame(1, $report['quality']['missing_hamlet']);
        $this->assertSame(0, $report['quality']['invalid_gender']);
        $this->assertSame(1, $report['quality']['requires_verification']);
        $this->assertSame(1, $report['qualityTotal']);
    }

    public function test_empty_desil_data_renders_public_empty_state(): void
    {
        Village::query()->create(['name' => 'Desa Uji', 'slug' => 'desa-uji', 'district' => 'Kecamatan Uji', 'city' => 'Kabupaten Uji', 'province' => 'Bali', 'address' => 'Jl. Uji']);

        $this->get('/analisis-desil?end_year=2026')
            ->assertOk()
            ->assertSee('0 KK')
            ->assertSee('Data jenis kelamin kepala keluarga belum tersedia.');
    }

    public function test_household_reference_is_unique_per_village_and_year(): void
    {
        $village = Village::query()->create(['name' => 'Desa Uji', 'slug' => 'desa-uji', 'district' => 'Kecamatan Uji', 'city' => 'Kabupaten Uji', 'province' => 'Bali', 'address' => 'Jl. Uji']);
        VillageHouseholdWelfare::query()->create(['village_id' => $village->id, 'year' => 2026, 'reference_code' => 'KK-001']);

        $this->expectException(UniqueConstraintViolationException::class);
        VillageHouseholdWelfare::query()->create(['village_id' => $village->id, 'year' => 2026, 'reference_code' => 'KK-001']);
    }
}
