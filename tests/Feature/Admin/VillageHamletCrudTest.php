<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Village;
use App\Models\VillageHamlet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VillageHamletCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_aparat_can_manage_normalized_hamlets(): void
    {
        Village::query()->create(['name' => 'Desa Uji', 'slug' => 'desa-uji', 'district' => 'Kecamatan Uji', 'city' => 'Kabupaten Uji', 'province' => 'Bali', 'address' => 'Jl. Uji']);
        $user = User::factory()->create(['role' => 'aparat']);
        $this->actingAs($user)->post(route('admin.village-hamlets.store'), ['name' => ' Banjar   Uji ', 'is_active' => true])->assertRedirect(route('admin.village-hamlets.index'));
        $hamlet = VillageHamlet::query()->firstOrFail();
        $this->assertSame('BANJAR UJI', $hamlet->normalized_name);

        $this->actingAs($user)->from(route('admin.village-hamlets.create'))->post(route('admin.village-hamlets.store'), ['name' => 'Banjar Uji'])->assertSessionHasErrors('name');
        $this->actingAs($user)->put(route('admin.village-hamlets.update', $hamlet), ['name' => 'Banjar Baru', 'is_active' => false])->assertRedirect(route('admin.village-hamlets.index'));
        $this->actingAs($user)->delete(route('admin.village-hamlets.destroy', $hamlet))->assertRedirect(route('admin.village-hamlets.index'));

        $this->assertDatabaseMissing('village_hamlets', ['id' => $hamlet->id]);
    }
}
