<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVillageHamletRequest;
use App\Http\Requests\UpdateVillageHamletRequest;
use App\Models\Village;
use App\Models\VillageHamlet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class VillageHamletController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.village-hamlets.index', ['items' => VillageHamlet::query()->orderBy('name')->paginate(20)->withQueryString()]);
    }

    public function create(): View
    {
        return view('admin.village-hamlets.create', ['item' => new VillageHamlet]);
    }

    public function store(StoreVillageHamletRequest $request): RedirectResponse
    {
        $village = Village::query()->first();
        if (! $village) {
            return back()->withErrors(['name' => 'Data desa belum tersedia.'])->withInput();
        }
        VillageHamlet::query()->create([...$request->validated(), 'village_id' => $village->id, 'normalized_name' => Str::upper($request->validated('name')), 'is_active' => $request->boolean('is_active')]);

        return redirect()->route('admin.village-hamlets.index')->with('status', 'Banjar berhasil ditambahkan.');
    }

    public function edit(VillageHamlet $villageHamlet): View
    {
        return view('admin.village-hamlets.edit', ['item' => $villageHamlet]);
    }

    public function update(UpdateVillageHamletRequest $request, VillageHamlet $villageHamlet): RedirectResponse
    {
        $villageHamlet->update([...$request->validated(), 'normalized_name' => Str::upper($request->validated('name')), 'is_active' => $request->boolean('is_active')]);

        return redirect()->route('admin.village-hamlets.index')->with('status', 'Banjar berhasil diperbarui.');
    }

    public function destroy(VillageHamlet $villageHamlet): RedirectResponse
    {
        if ($villageHamlet->householdWelfares()->exists()) {
            return back()->withErrors(['name' => 'Banjar masih digunakan oleh data kesejahteraan KK dan tidak dapat dihapus.']);
        }
        $villageHamlet->delete();

        return redirect()->route('admin.village-hamlets.index')->with('status', 'Banjar berhasil dihapus.');
    }
}
