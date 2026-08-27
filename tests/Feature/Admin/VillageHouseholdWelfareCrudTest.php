<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Village;
use App\Models\VillageHamlet;
use App\Models\VillageHouseholdWelfare;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VillageHouseholdWelfareCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_aparat_can_create_update_and_delete_household_welfare_data(): void
    {
        $village = Village::query()->create(['name' => 'Desa Uji', 'slug' => 'desa-uji', 'district' => 'Kecamatan Uji', 'city' => 'Kabupaten Uji', 'province' => 'Bali', 'address' => 'Jl. Uji']);
        $hamlet = VillageHamlet::query()->create(['village_id' => $village->id, 'name' => 'Banjar Uji', 'normalized_name' => 'BANJAR UJI']);
        $user = User::factory()->create(['role' => 'aparat']);

        $this->actingAs($user)->post(route('admin.village-household-welfares.store'), ['year' => 2026, 'reference_code' => 'KK-001', 'village_hamlet_id' => $hamlet->id, 'decile' => 'D1', 'head_gender' => 'perempuan', 'requires_verification' => true, 'is_published' => true])->assertRedirect(route('admin.village-household-welfares.index'));
        $item = VillageHouseholdWelfare::query()->firstOrFail();
        $this->assertTrue($item->is_published);
        $this->assertTrue($item->requires_verification);

        $this->actingAs($user)->put(route('admin.village-household-welfares.update', $item), ['year' => 2026, 'reference_code' => 'KK-001', 'village_hamlet_id' => $hamlet->id, 'decile' => 'D2', 'head_gender' => 'laki_laki', 'is_published' => false])->assertRedirect(route('admin.village-household-welfares.index'));
        $this->assertDatabaseHas('village_household_welfares', ['id' => $item->id, 'decile' => 'D2', 'is_published' => false]);

        $this->actingAs($user)->delete(route('admin.village-household-welfares.destroy', $item))->assertRedirect(route('admin.village-household-welfares.index'));
        $this->assertDatabaseMissing('village_household_welfares', ['id' => $item->id]);
    }

    public function test_public_user_cannot_manage_household_welfare_data_and_duplicate_is_rejected(): void
    {
        $village = Village::query()->create(['name' => 'Desa Uji', 'slug' => 'desa-uji', 'district' => 'Kecamatan Uji', 'city' => 'Kabupaten Uji', 'province' => 'Bali', 'address' => 'Jl. Uji']);
        VillageHouseholdWelfare::query()->create(['village_id' => $village->id, 'year' => 2026, 'reference_code' => 'KK-001']);
        $public = User::factory()->create(['role' => 'public']);
        $aparat = User::factory()->create(['role' => 'aparat']);

        $this->actingAs($public)->get(route('admin.village-household-welfares.index'))->assertForbidden();
        $this->actingAs($aparat)->from(route('admin.village-household-welfares.create'))->post(route('admin.village-household-welfares.store'), ['year' => 2026, 'reference_code' => 'KK-001', 'decile' => 'D1'])->assertRedirect(route('admin.village-household-welfares.create'))->assertSessionHasErrors('reference_code');
    }

    public function test_validation_rejects_invalid_desil_and_gender(): void
    {
        Village::query()->create(['name' => 'Desa Uji', 'slug' => 'desa-uji', 'district' => 'Kecamatan Uji', 'city' => 'Kabupaten Uji', 'province' => 'Bali', 'address' => 'Jl. Uji']);
        $user = User::factory()->create(['role' => 'aparat']);

        $this->actingAs($user)->from(route('admin.village-household-welfares.create'))->post(route('admin.village-household-welfares.store'), ['year' => 2026, 'reference_code' => 'KK-002', 'decile' => 'D9', 'head_gender' => 'lainnya'])->assertRedirect(route('admin.village-household-welfares.create'))->assertSessionHasErrors(['decile', 'head_gender']);
    }
}
