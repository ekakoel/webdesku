<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Models\VillagePopulation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VillagePopulationController extends Controller
{
    public function index(): View
    {
        $items = VillagePopulation::query()
            ->orderByDesc('year')
            ->orderBy('sort_order')
            ->paginate(12);

        return view('admin.village-populations.index', compact('items'));
    }

    public function create(): View
    {
        return view('admin.village-populations.create', ['item' => new VillagePopulation()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $village = Village::query()->first();

        if (!$village) {
            return back()->withErrors(['year' => 'Data desa belum tersedia.'])->withInput();
        }

        $validated['village_id'] = $village->id;
        $validated['is_published'] = (bool) ($validated['is_published'] ?? false);
        $validated['published_at'] = $validated['is_published'] ? now() : null;

        VillagePopulation::query()->create($validated);

        return redirect()->route('admin.village-populations.index')->with('status', 'Data penduduk berhasil ditambahkan.');
    }

    public function edit(VillagePopulation $villagePopulation): View
    {
        return view('admin.village-populations.edit', ['item' => $villagePopulation]);
    }

    public function update(Request $request, VillagePopulation $villagePopulation): RedirectResponse
    {
        $validated = $request->validate($this->rules($villagePopulation->id));
        $villagePopulation->fill($validated);
        $villagePopulation->is_published = (bool) ($validated['is_published'] ?? false);
        $villagePopulation->published_at = $villagePopulation->is_published ? ($villagePopulation->published_at ?? now()) : null;
        $villagePopulation->save();

        return redirect()->route('admin.village-populations.index')->with('status', 'Data penduduk berhasil diperbarui.');
    }

    public function destroy(VillagePopulation $villagePopulation): RedirectResponse
    {
        $villagePopulation->delete();

        return redirect()->route('admin.village-populations.index')->with('status', 'Data penduduk berhasil dihapus.');
    }

    private function rules(?int $ignoreId = null): array
    {
        $villageId = Village::query()->first()?->id ?? 0;
        $uniqueYear = Rule::unique('village_populations', 'year')
            ->where(fn ($query) => $query->where('village_id', $villageId));
        if ($ignoreId) {
            $uniqueYear = $uniqueYear->ignore($ignoreId);
        }

        return [
            'year' => ['required', 'integer', 'min:1990', 'max:2100', $uniqueYear],
            'male' => ['required', 'integer', 'min:0'],
            'female' => ['required', 'integer', 'min:0'],
            'households' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }
}
