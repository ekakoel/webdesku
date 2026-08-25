<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\Village;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VillageIdentityTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_use_configured_village_identity(): void
    {
        $village = $this->createVillage('Desa Contoh');

        $homeResponse = $this->get('/');
        $homeResponse->assertOk();
        $homeResponse->assertSee('<title>Desa Contoh</title>', false);
        $homeResponse->assertSee('Statistik Desa');
        $this->assertStringNotContainsString('Desa Dangin Puri', $homeResponse->getContent());
        $this->assertStringNotContainsString('Webdesku', $homeResponse->getContent());

        $newsResponse = $this->get('/berita');
        $newsResponse->assertOk();
        $newsResponse->assertSee('<title>Berita | Desa Contoh</title>', false);
        $this->assertStringNotContainsString('Webdesku', $newsResponse->getContent());

        $village->update(['name' => 'Desa Baru']);

        $updatedResponse = $this->get('/berita');
        $updatedResponse->assertOk();
        $updatedResponse->assertSee('<title>Berita | Desa Baru</title>', false);
    }

    public function test_detail_and_auth_pages_use_dynamic_village_titles(): void
    {
        $village = $this->createVillage('Desa Uji');

        News::query()->create([
            'village_id' => $village->id,
            'title' => 'Gotong Royong Desa',
            'slug' => 'gotong-royong-desa',
            'content' => 'Konten berita desa.',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $detailResponse = $this->get('/berita/gotong-royong-desa');
        $detailResponse->assertOk();
        $detailResponse->assertSee('<title>Gotong Royong Desa | Desa Uji</title>', false);
        $this->assertStringNotContainsString('Webdesku', $detailResponse->getContent());

        $loginResponse = $this->get('/login');
        $loginResponse->assertOk();
        $loginResponse->assertSee('<title>Login | Desa Uji</title>', false);
        $this->assertStringNotContainsString('Webdesku', $loginResponse->getContent());
    }

    private function createVillage(string $name): Village
    {
        return Village::query()->create([
            'name' => $name,
            'slug' => str($name)->slug(),
            'district' => 'Kecamatan Uji',
            'city' => 'Kabupaten Uji',
            'province' => 'Bali',
            'address' => 'Jl. Uji No. 1',
            'email' => 'desa@example.test',
            'phone' => '081234567890',
            'head_name' => 'Kepala Desa Uji',
        ]);
    }
}
