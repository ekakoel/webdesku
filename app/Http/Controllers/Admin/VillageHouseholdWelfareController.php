<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVillageHouseholdWelfareRequest;
use App\Http\Requests\UpdateVillageHouseholdWelfareRequest;
use App\Models\Village;
use App\Models\VillageHamlet;
use App\Models\VillageHouseholdWelfare;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VillageHouseholdWelfareController extends Controller
{
    public function index(Request $request): View
    {
        $year = $request->integer('year') ?: null;
        return view('admin.village-household-welfares.index', ['items' => VillageHouseholdWelfare::query()->with('hamlet')->when($year, fn($query) => $query->where('year', $year))->orderByDesc('year')->latest()->paginate(20)->withQueryString(), 'years' => VillageHouseholdWelfare::query()->distinct()->orderByDesc('year')->pluck('year'), 'year' => $year]);
    }
    public function create(): View
    {
        return view('admin.village-household-welfares.create', $this->formData(new VillageHouseholdWelfare));
    }
    public function store(StoreVillageHouseholdWelfareRequest $request): RedirectResponse
    {
        $village = Village::query()->first();
        if (! $village) return back()->withErrors(['year' => 'Data desa belum tersedia.'])->withInput();
        VillageHouseholdWelfare::query()->create($this->payload($request, $village->id));
        return redirect()->route('admin.village-household-welfares.index')->with('status', 'Data kesejahteraan KK berhasil ditambahkan.');
    }
    public function edit(VillageHouseholdWelfare $villageHouseholdWelfare): View
    {
        return view('admin.village-household-welfares.edit', $this->formData($villageHouseholdWelfare));
    }
    public function update(UpdateVillageHouseholdWelfareRequest $request, VillageHouseholdWelfare $villageHouseholdWelfare): RedirectResponse
    {
        $villageHouseholdWelfare->update($this->payload($request, $villageHouseholdWelfare->village_id));
        return redirect()->route('admin.village-household-welfares.index')->with('status', 'Data kesejahteraan KK berhasil diperbarui.');
    }
    public function destroy(VillageHouseholdWelfare $villageHouseholdWelfare): RedirectResponse
    {
        $villageHouseholdWelfare->delete();
        return redirect()->route('admin.village-household-welfares.index')->with('status', 'Data kesejahteraan KK berhasil dihapus.');
    }
    private function formData(VillageHouseholdWelfare $item): array
    {
        return ['item' => $item, 'hamlets' => VillageHamlet::query()->where('is_active', true)->orderBy('name')->get(), 'deciles' => VillageHouseholdWelfare::DECILES, 'genders' => VillageHouseholdWelfare::GENDERS];
    }
    private function payload(Request $request, int $villageId): array
    {
        return [...$request->validated(), 'village_id' => $villageId, 'is_outside_village' => $request->boolean('is_outside_village'), 'requires_verification' => $request->boolean('requires_verification'), 'is_published' => $request->boolean('is_published'), 'published_at' => $request->boolean('is_published') ? now() : null];
    }
}
