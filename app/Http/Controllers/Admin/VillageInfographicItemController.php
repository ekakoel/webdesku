<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Models\VillageInfographicItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VillageInfographicItemController extends Controller
{
    public function index(): View
    {
        $category = (string) request()->query('category', 'all');
        $year = (int) request()->query('year', 0);
        $q = trim((string) request()->query('q', ''));

        $items = VillageInfographicItem::query()
            ->when($category !== 'all', fn ($query) => $query->where('category', $category))
            ->when($year > 0, fn ($query) => $query->where('year', $year))
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($subQuery) use ($q) {
                    $subQuery->where('title', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%")
                        ->orWhere('source', 'like', "%{$q}%")
                        ->orWhere('value', 'like', "%{$q}%");
                });
            })
            ->orderBy('category')
            ->orderByDesc('year')
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(12);
        $years = VillageInfographicItem::query()
            ->whereNotNull('year')
            ->select('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return view('admin.village-infographic-items.index', [
            'items' => $items,
            'category' => $category,
            'year' => $year > 0 ? $year : null,
            'years' => $years,
            'q' => $q,
            'categoryOptions' => VillageInfographicItem::categoryOptions(),
        ]);
    }

    public function create(): View
    {
        return view('admin.village-infographic-items.create', [
            'item' => new VillageInfographicItem,
            'categoryOptions' => VillageInfographicItem::categoryOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $village = Village::query()->first();

        if (! $village) {
            return back()->withErrors(['title' => 'Data desa belum tersedia.'])->withInput();
        }

        $validated = $request->validate(
            $this->rules((int) $village->id),
            $this->messages()
        );
        $validated['village_id'] = $village->id;
        $validated['is_published'] = (bool) ($validated['is_published'] ?? false);
        $validated['published_at'] = $validated['is_published'] ? now() : null;

        VillageInfographicItem::query()->create($validated);

        return redirect()->route('admin.village-infographic-items.index')->with('status', 'Data infografis berhasil ditambahkan.');
    }

    public function edit(VillageInfographicItem $villageInfographicItem): View
    {
        return view('admin.village-infographic-items.edit', [
            'item' => $villageInfographicItem,
            'categoryOptions' => VillageInfographicItem::categoryOptions(),
        ]);
    }

    public function update(Request $request, VillageInfographicItem $villageInfographicItem): RedirectResponse
    {
        $validated = $request->validate(
            $this->rules((int) $villageInfographicItem->village_id, (int) $villageInfographicItem->id),
            $this->messages()
        );
        $villageInfographicItem->fill($validated);
        $villageInfographicItem->is_published = (bool) ($validated['is_published'] ?? false);
        $villageInfographicItem->published_at = $villageInfographicItem->is_published ? ($villageInfographicItem->published_at ?? now()) : null;
        $villageInfographicItem->save();

        return redirect()->route('admin.village-infographic-items.index')->with('status', 'Data infografis berhasil diperbarui.');
    }

    public function destroy(VillageInfographicItem $villageInfographicItem): RedirectResponse
    {
        $villageInfographicItem->delete();

        return redirect()->route('admin.village-infographic-items.index')->with('status', 'Data infografis berhasil dihapus.');
    }

    private function rules(int $villageId, ?int $ignoreId = null): array
    {
        $category = (string) request()->input('category', '');
        $year = request()->input('year');
        $titleRule = Rule::unique('village_infographic_items', 'title')
            ->where(function ($query) use ($villageId, $category, $year) {
                $query->where('village_id', $villageId)
                    ->where('category', $category);

                if ($year === null || $year === '') {
                    $query->whereNull('year');

                    return;
                }

                $query->where('year', (int) $year);
            });

        if ($ignoreId) {
            $titleRule->ignore($ignoreId);
        }

        return [
            'category' => ['required', 'string', 'in:'.implode(',', array_keys(VillageInfographicItem::categoryOptions()))],
            'year' => ['nullable', 'integer', 'min:1990', 'max:2100'],
            'title' => ['required', 'string', 'max:255', $titleRule],
            'value' => ['nullable', 'string', 'max:120'],
            'unit' => ['nullable', 'string', 'max:50'],
            'source' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:40'],
            'color' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }

    private function messages(): array
    {
        return [
            'title.unique' => 'Indikator dengan kategori dan tahun yang sama sudah ada.',
        ];
    }
}
