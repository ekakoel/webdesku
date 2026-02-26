<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Models\VillagePopulationStat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VillagePopulationStatController extends Controller
{
    public function index(Request $request): View
    {
        $year = (int) $request->query('year', 0);
        $category = (string) $request->query('category', 'all');

        $query = VillagePopulationStat::query()
            ->when($year > 0, fn ($builder) => $builder->where('year', $year))
            ->when($category !== 'all', fn ($builder) => $builder->where('category', $category))
            ->orderByDesc('year')
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('label');

        $items = $query->paginate(20)->withQueryString();
        $years = VillagePopulationStat::query()->select('year')->distinct()->orderByDesc('year')->pluck('year');

        return view('admin.village-population-stats.index', [
            'items' => $items,
            'years' => $years,
            'year' => $year > 0 ? $year : null,
            'category' => $category,
            'categoryOptions' => VillagePopulationStat::categoryOptions(),
        ]);
    }

    public function create(): View
    {
        return view('admin.village-population-stats.create', [
            'item' => new VillagePopulationStat(),
            'categoryOptions' => VillagePopulationStat::categoryOptions(),
        ]);
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

        VillagePopulationStat::query()->create($validated);

        return redirect()->route('admin.village-population-stats.index')->with('status', 'Statistik kategori penduduk berhasil ditambahkan.');
    }

    public function edit(VillagePopulationStat $villagePopulationStat): View
    {
        return view('admin.village-population-stats.edit', [
            'item' => $villagePopulationStat,
            'categoryOptions' => VillagePopulationStat::categoryOptions(),
        ]);
    }

    public function update(Request $request, VillagePopulationStat $villagePopulationStat): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        $villagePopulationStat->fill($validated);
        $villagePopulationStat->is_published = (bool) ($validated['is_published'] ?? false);
        $villagePopulationStat->published_at = $villagePopulationStat->is_published
            ? ($villagePopulationStat->published_at ?? now())
            : null;
        $villagePopulationStat->save();

        return redirect()->route('admin.village-population-stats.index')->with('status', 'Statistik kategori penduduk berhasil diperbarui.');
    }

    public function destroy(VillagePopulationStat $villagePopulationStat): RedirectResponse
    {
        $villagePopulationStat->delete();

        return redirect()->route('admin.village-population-stats.index')->with('status', 'Statistik kategori penduduk berhasil dihapus.');
    }

    private function rules(): array
    {
        return [
            'year' => ['required', 'integer', 'min:1990', 'max:2100'],
            'category' => ['required', 'string', 'in:'.implode(',', array_keys(VillagePopulationStat::categoryOptions()))],
            'label' => ['required', 'string', 'max:120'],
            'value' => ['required', 'integer', 'min:0'],
            'unit' => ['nullable', 'string', 'max:30'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }
}

