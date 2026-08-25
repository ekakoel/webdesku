<?php

namespace Tests\Feature;

use App\Models\Agenda;
use App\Models\Complaint;
use App\Models\News;
use App\Models\ServiceRequest;
use App\Models\Village;
use App\Models\VillageAsset;
use App\Models\VillageInfographicItem;
use App\Models\VillagePopulation;
use App\Models\VillageService;
use App\Services\StatisticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatisticsReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_statistics_filter_combines_data_from_selected_year_range(): void
    {
        $village = $this->seedStatisticsData();

        $response = $this->get('/statistik?start_year=2025&end_year=2026');

        $response->assertOk();
        $response->assertSee('Berita Periode Ini');
        $response->assertSee('2');
        $response->assertSee('Pengajuan Layanan');
        $response->assertSee('2');

        $report = app(StatisticsService::class)->report($village, 2025, 2026);

        $this->assertSame(2, $report['periodicCounts']['news']);
        $this->assertSame(2, $report['periodicCounts']['service_requests']);
        $this->assertSame(1, $report['periodicCounts']['agendas']);
    }

    public function test_statistics_filter_single_year_only_counts_that_year(): void
    {
        $village = $this->seedStatisticsData();

        $response = $this->get('/statistik?start_year=2026&end_year=2026');

        $response->assertOk();

        $report = app(StatisticsService::class)->report($village, 2026, 2026);

        $this->assertSame(1, $report['periodicCounts']['news']);
        $this->assertSame(1, $report['periodicCounts']['service_requests']);
        $this->assertSame(0, $report['periodicCounts']['agendas']);
    }

    public function test_statistics_filter_rejects_invalid_year_range(): void
    {
        $this->seedStatisticsData();

        $response = $this->from('/statistik')->get('/statistik?start_year=2026&end_year=2025');

        $response->assertRedirect('/statistik');
        $response->assertSessionHasErrors('end_year');
    }

    public function test_statistics_page_displays_empty_period_state(): void
    {
        $village = $this->seedStatisticsData();

        $response = $this->get('/statistik?start_year=2024&end_year=2024');

        $response->assertOk();
        $response->assertSee('Tidak terdapat data periodik pada periode yang dipilih.');
        $response->assertSee('Data penduduk belum tersedia untuk periode ini.');

        $report = app(StatisticsService::class)->report($village, 2024, 2024);

        $this->assertFalse($report['hasPeriodData']);
        $this->assertFalse($report['population']['available']);
        $this->assertSame(0, collect($report['periodicCounts'])->sum());
    }

    public function test_statistics_pdf_uses_selected_filter_and_same_service_counts(): void
    {
        $village = $this->seedStatisticsData();
        $report = app(StatisticsService::class)->report($village, 2025, 2026);

        $response = $this->get('/statistik/pdf?start_year=2025&end_year=2026');

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $response->assertHeader('content-disposition', 'attachment; filename=laporan-statistik-desa-2025-2026.pdf');
        $this->assertSame(2, $report['periodicCounts']['news']);
        $this->assertSame(2, $report['periodicCounts']['service_requests']);
    }

    public function test_statistics_report_includes_published_aggregate_indicators(): void
    {
        $village = $this->seedStatisticsData();

        VillageInfographicItem::query()->create([
            'village_id' => $village->id,
            'category' => 'geografi_iklim',
            'year' => 2026,
            'title' => 'Curah Hujan',
            'value' => '2000',
            'unit' => 'mm/tahun',
            'source' => 'Pemerintah Desa',
            'is_published' => true,
            'published_at' => now(),
        ]);
        VillageInfographicItem::query()->create([
            'village_id' => $village->id,
            'category' => 'kesehatan_sosial',
            'year' => 2024,
            'title' => 'Hipertensi',
            'value' => '360',
            'unit' => 'orang',
            'source' => 'Pemerintah Desa',
            'is_published' => true,
            'published_at' => now(),
        ]);
        VillageInfographicItem::query()->create([
            'village_id' => $village->id,
            'category' => 'ekonomi',
            'year' => 2026,
            'title' => 'Indikator Draft',
            'value' => '99',
            'unit' => 'unit',
            'source' => 'Internal',
            'is_published' => false,
            'published_at' => null,
        ]);

        $response = $this->get('/statistik?start_year=2026&end_year=2026');

        $response->assertOk();
        $response->assertSee('Geografi &amp; Iklim', false);
        $response->assertSee('Curah Hujan');
        $response->assertSee('2000');
        $response->assertDontSee('Hipertensi');
        $response->assertDontSee('Indikator Draft');
    }

    public function test_statistics_excel_uses_selected_filter_and_public_identity(): void
    {
        $village = $this->seedStatisticsData();

        VillageInfographicItem::query()->create([
            'village_id' => $village->id,
            'category' => 'ekonomi',
            'year' => 2026,
            'title' => 'BUMDes',
            'value' => '1',
            'unit' => 'unit',
            'source' => 'Pemerintah Desa',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get('/statistik/excel?start_year=2026&end_year=2026');

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.ms-excel; charset=UTF-8');
        $response->assertHeader('content-disposition', 'attachment; filename=laporan-statistik-desa-2026.xls');
        $content = $response->streamedContent();

        $this->assertStringContainsString('Desa Uji', $content);
        $this->assertStringContainsString('BUMDes', $content);
        $this->assertStringContainsString('Pemerintah Desa', $content);
        $this->assertStringNotContainsString('Webdesku', $content);
        $this->assertStringNotContainsString('1234567890123457', $content);
    }

    public function test_statistics_data_lineage_matches_database_service_frontend_and_excel(): void
    {
        $village = $this->seedStatisticsData();

        Complaint::query()->create([
            'village_id' => $village->id,
            'ticket_code' => 'CMP-2026-001',
            'public_token' => 'complaint-token-2026-001',
            'name' => 'Warga Pengadu',
            'category' => 'jalan',
            'title' => 'Jalan Rusak',
            'description' => 'Permukaan jalan rusak.',
            'status' => 'baru',
            'submitted_at' => '2026-02-01 08:00:00',
        ]);
        Complaint::query()->create([
            'village_id' => $village->id,
            'ticket_code' => 'CMP-2026-002',
            'public_token' => 'complaint-token-2026-002',
            'name' => 'Warga Pengadu',
            'category' => 'jalan',
            'title' => 'Lampu Jalan',
            'description' => 'Lampu jalan padam.',
            'status' => 'selesai',
            'submitted_at' => '2026-03-01 08:00:00',
        ]);
        VillageAsset::query()->create([
            'village_id' => $village->id,
            'name' => 'Puskesmas Pembantu',
            'type' => 'kesehatan',
            'is_published' => true,
            'published_at' => '2026-01-01 00:00:00',
        ]);
        VillageAsset::query()->create([
            'village_id' => $village->id,
            'name' => 'SD Negeri Uji',
            'type' => 'pendidikan',
            'is_published' => true,
            'published_at' => '2026-01-01 00:00:00',
        ]);
        VillageInfographicItem::query()->create([
            'village_id' => $village->id,
            'category' => 'infrastruktur',
            'year' => 2026,
            'title' => 'Panjang Jalan',
            'value' => '12',
            'unit' => 'km',
            'source' => 'Pemerintah Desa',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $report = app(StatisticsService::class)->report($village, 2026, 2026);

        $this->assertSame(1, $report['periodicCounts']['news']);
        $this->assertSame(1, $report['periodicCounts']['service_requests']);
        $this->assertSame(2, $report['periodicCounts']['complaints']);
        $this->assertSame(2, $report['masterCounts']['assets']);
        $this->assertSame(220, $report['population']['total']);
        $this->assertSame(100, $report['population']['male']);
        $this->assertSame(120, $report['population']['female']);
        $this->assertSame(2, Complaint::query()->where('village_id', $village->id)->whereYear('submitted_at', 2026)->count());
        $this->assertSame(2, VillageAsset::query()->where('village_id', $village->id)->where('is_published', true)->count());
        $this->assertSame(1, $report['complaintByStatus']['baru']);
        $this->assertSame(1, $report['complaintByStatus']['selesai']);
        $this->assertSame('jalan', $report['complaintByCategory']->first()->category);
        $this->assertSame(2, (int) $report['complaintByCategory']->first()->total);
        $this->assertSame(['pendidikan', 'kesehatan'], $report['assetTypeStats']->pluck('type')->all());
        $this->assertSame('Panjang Jalan', $report['infographicIndicators']->first()['items']->first()['title']);

        $this->get('/statistik?start_year=2026&end_year=2026')
            ->assertOk()
            ->assertSee('220 Jiwa')
            ->assertSee('Panjang Jalan')
            ->assertSee('Pemerintah Desa')
            ->assertSee('Pendidikan')
            ->assertSee('Kesehatan');

        $excel = $this->get('/statistik/excel?start_year=2026&end_year=2026');
        $excel->assertOk();
        $content = $excel->streamedContent();

        $this->assertStringContainsString('Total Penduduk', $content);
        $this->assertStringContainsString('220 Jiwa', $content);
        $this->assertStringContainsString('Panjang Jalan', $content);
        $this->assertStringContainsString('Pemerintah Desa', $content);
    }

    private function seedStatisticsData(): Village
    {
        $village = Village::query()->create([
            'name' => 'Desa Uji',
            'slug' => 'desa-uji',
            'district' => 'Kecamatan Uji',
            'city' => 'Kabupaten Uji',
            'province' => 'Bali',
            'address' => 'Jl. Uji No. 1',
            'head_name' => 'Kepala Desa Uji',
        ]);

        News::query()->create([
            'village_id' => $village->id,
            'title' => 'Berita 2025',
            'slug' => 'berita-2025',
            'content' => 'Konten',
            'is_published' => true,
            'published_at' => '2025-05-10 08:00:00',
        ]);
        News::query()->create([
            'village_id' => $village->id,
            'title' => 'Berita 2026',
            'slug' => 'berita-2026',
            'content' => 'Konten',
            'is_published' => true,
            'published_at' => '2026-06-10 08:00:00',
        ]);
        News::query()->create([
            'village_id' => $village->id,
            'title' => 'Berita 2023',
            'slug' => 'berita-2023',
            'content' => 'Konten',
            'is_published' => true,
            'published_at' => '2023-06-10 08:00:00',
        ]);

        Agenda::query()->create([
            'village_id' => $village->id,
            'title' => 'Agenda 2025',
            'description' => 'Agenda',
            'start_at' => '2025-03-01 09:00:00',
            'is_published' => true,
            'published_at' => '2025-02-01 09:00:00',
        ]);

        $service = VillageService::query()->create([
            'village_id' => $village->id,
            'name' => 'Surat Keterangan',
            'slug' => 'surat-keterangan',
            'is_published' => true,
            'published_at' => '2025-01-01 09:00:00',
        ]);

        ServiceRequest::query()->create([
            'village_id' => $village->id,
            'service_id' => $service->id,
            'ticket_code' => 'SRV-2025',
            'public_token' => 'token-2025',
            'applicant_name' => 'Warga 2025',
            'nik' => '1234567890123456',
            'phone' => '081234567890',
            'address' => 'Alamat',
            'status' => 'diajukan',
            'submitted_at' => '2025-01-15 10:00:00',
        ]);
        ServiceRequest::query()->create([
            'village_id' => $village->id,
            'service_id' => $service->id,
            'ticket_code' => 'SRV-2026',
            'public_token' => 'token-2026',
            'applicant_name' => 'Warga 2026',
            'nik' => '1234567890123457',
            'phone' => '081234567891',
            'address' => 'Alamat',
            'status' => 'diajukan',
            'submitted_at' => '2026-01-15 10:00:00',
        ]);

        VillagePopulation::query()->create([
            'village_id' => $village->id,
            'year' => 2026,
            'male' => 100,
            'female' => 120,
            'households' => 80,
            'is_published' => true,
            'published_at' => '2026-01-01 00:00:00',
        ]);

        return $village;
    }
}
