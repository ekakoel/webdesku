<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Village;
use App\Models\VillageInfographicItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VillageInfographicItemCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_aparat_can_manage_yearly_aggregate_indicators(): void
    {
        $village = Village::query()->create([
            'name' => 'Desa Uji',
            'slug' => 'desa-uji',
            'district' => 'Kecamatan Uji',
            'city' => 'Kabupaten Uji',
            'province' => 'Bali',
            'address' => 'Jl. Uji No. 1',
        ]);
        $user = User::factory()->create(['role' => 'aparat']);

        $this->actingAs($user)
            ->post(route('admin.village-infographic-items.store'), [
                'category' => 'geografi_iklim',
                'year' => 2026,
                'title' => 'Curah Hujan',
                'value' => '2000',
                'unit' => 'mm/tahun',
                'source' => 'Pemerintah Desa',
                'notes' => 'Agregat tahunan.',
                'description' => 'Rata-rata tahunan.',
                'sort_order' => 1,
                'is_published' => '1',
            ])
            ->assertRedirect(route('admin.village-infographic-items.index'));

        $item = VillageInfographicItem::query()->firstOrFail();

        $this->assertSame($village->id, $item->village_id);
        $this->assertSame('geografi_iklim', $item->category);
        $this->assertSame(2026, $item->year);
        $this->assertTrue($item->is_published);
        $this->assertNotNull($item->published_at);

        $this->actingAs($user)
            ->get(route('admin.village-infographic-items.index', ['year' => 2026, 'category' => 'geografi_iklim']))
            ->assertOk()
            ->assertSee('Curah Hujan')
            ->assertSee('2026')
            ->assertSee('Pemerintah Desa');

        $this->actingAs($user)
            ->put(route('admin.village-infographic-items.update', $item), [
                'category' => 'ekonomi',
                'year' => 2025,
                'title' => 'BUMDes',
                'value' => '1',
                'unit' => 'unit',
                'source' => 'Pemerintah Desa',
                'notes' => 'Data agregat.',
                'description' => 'Lembaga ekonomi desa.',
                'sort_order' => 2,
                'is_published' => '0',
            ])
            ->assertRedirect(route('admin.village-infographic-items.index'));

        $item->refresh();
        $this->assertSame('ekonomi', $item->category);
        $this->assertSame(2025, $item->year);
        $this->assertFalse($item->is_published);
        $this->assertNull($item->published_at);

        $this->actingAs($user)
            ->delete(route('admin.village-infographic-items.destroy', $item))
            ->assertRedirect(route('admin.village-infographic-items.index'));

        $this->assertDatabaseMissing('village_infographic_items', ['id' => $item->id]);
    }

    public function test_public_user_cannot_access_admin_indicator_crud(): void
    {
        $user = User::factory()->create(['role' => 'public']);

        $this->actingAs($user)
            ->get(route('admin.village-infographic-items.index'))
            ->assertForbidden();
    }

    public function test_admin_indicator_crud_rejects_duplicate_category_year_and_title(): void
    {
        $village = Village::query()->create([
            'name' => 'Desa Uji',
            'slug' => 'desa-uji',
            'district' => 'Kecamatan Uji',
            'city' => 'Kabupaten Uji',
            'province' => 'Bali',
            'address' => 'Jl. Uji No. 1',
        ]);
        $user = User::factory()->create(['role' => 'aparat']);

        VillageInfographicItem::query()->create([
            'village_id' => $village->id,
            'category' => 'geografi_iklim',
            'year' => 2026,
            'title' => 'Curah Hujan',
            'value' => '2000',
            'unit' => 'mm/tahun',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->actingAs($user)
            ->from(route('admin.village-infographic-items.create'))
            ->post(route('admin.village-infographic-items.store'), [
                'category' => 'geografi_iklim',
                'year' => 2026,
                'title' => 'Curah Hujan',
                'value' => '2100',
                'unit' => 'mm/tahun',
                'is_published' => '1',
            ])
            ->assertRedirect(route('admin.village-infographic-items.create'))
            ->assertSessionHasErrors('title');

        $this->assertSame(
            1,
            VillageInfographicItem::query()
                ->where('village_id', $village->id)
                ->where('category', 'geografi_iklim')
                ->where('year', 2026)
                ->where('title', 'Curah Hujan')
                ->count()
        );
    }
}
