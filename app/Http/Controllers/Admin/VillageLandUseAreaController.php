<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Models\VillageLandUseArea;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VillageLandUseAreaController extends Controller
{
    public function index(Request $request): View
    {
        $year = (int) $request->query('year', 0);
        $q = trim((string) $request->query('q', ''));

        $items = VillageLandUseArea::query()
            ->when($year > 0, fn ($query) => $query->where('fiscal_year', $year))
            ->when($q !== '', fn ($query) => $query->where('label', 'like', "%{$q}%"))
            ->orderByDesc('fiscal_year')
            ->orderBy('sort_order')
            ->orderBy('label')
            ->paginate(20)
            ->withQueryString();

        $years = VillageLandUseArea::query()
            ->select('fiscal_year')
            ->whereNotNull('fiscal_year')
            ->distinct()
            ->orderByDesc('fiscal_year')
            ->pluck('fiscal_year');

        return view('admin.village-land-use-areas.index', [
            'items' => $items,
            'years' => $years,
            'year' => $year > 0 ? $year : null,
            'q' => $q,
        ]);
    }

    public function create(): View
    {
        return view('admin.village-land-use-areas.create', [
            'item' => new VillageLandUseArea(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $village = Village::query()->first();

        if (!$village) {
            return back()->withErrors(['label' => 'Data desa belum tersedia.'])->withInput();
        }

        $validated['village_id'] = $village->id;
        $validated['is_published'] = (bool) ($validated['is_published'] ?? false);
        $validated['published_at'] = $validated['is_published'] ? now() : null;

        VillageLandUseArea::query()->create($validated);

        return redirect()->route('admin.village-land-use-areas.index')->with('status', 'Data luas wilayah berhasil ditambahkan.');
    }

    public function edit(VillageLandUseArea $villageLandUseArea): View
    {
        return view('admin.village-land-use-areas.edit', [
            'item' => $villageLandUseArea,
        ]);
    }

    public function update(Request $request, VillageLandUseArea $villageLandUseArea): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $villageLandUseArea->fill($validated);
        $villageLandUseArea->is_published = (bool) ($validated['is_published'] ?? false);
        $villageLandUseArea->published_at = $villageLandUseArea->is_published
            ? ($villageLandUseArea->published_at ?? now())
            : null;
        $villageLandUseArea->save();

        return redirect()->route('admin.village-land-use-areas.index')->with('status', 'Data luas wilayah berhasil diperbarui.');
    }

    public function destroy(VillageLandUseArea $villageLandUseArea): RedirectResponse
    {
        $villageLandUseArea->delete();

        return redirect()->route('admin.village-land-use-areas.index')->with('status', 'Data luas wilayah berhasil dihapus.');
    }

    private function rules(): array
    {
        return [
            'fiscal_year' => ['nullable', 'integer', 'min:1990', 'max:2100'],
            'label' => ['required', 'string', 'max:120'],
            'area_value' => ['required', 'numeric', 'min:0'],
            'unit' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }
}
