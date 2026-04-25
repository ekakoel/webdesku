<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Models\VillageInfographicItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VillageInfographicItemController extends Controller
{
    public function index(): View
    {
        $category = (string) request()->query('category', 'all');
        $q = trim((string) request()->query('q', ''));

        $items = VillageInfographicItem::query()
            ->when($category !== 'all', fn ($query) => $query->where('category', $category))
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($subQuery) use ($q) {
                    $subQuery->where('title', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%")
                        ->orWhere('value', 'like', "%{$q}%");
                });
            })
            ->orderBy('category')
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(12);

        return view('admin.village-infographic-items.index', [
            'items' => $items,
            'category' => $category,
            'q' => $q,
            'categoryOptions' => VillageInfographicItem::categoryOptions(),
        ]);
    }

    public function create(): View
    {
        return view('admin.village-infographic-items.create', [
            'item' => new VillageInfographicItem(),
            'categoryOptions' => VillageInfographicItem::categoryOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $village = Village::query()->first();

        if (!$village) {
            return back()->withErrors(['title' => 'Data desa belum tersedia.'])->withInput();
        }

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
        $validated = $request->validate($this->rules());
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

    private function rules(): array
    {
        return [
            'category' => ['required', 'string', 'in:'.implode(',', array_keys(VillageInfographicItem::categoryOptions()))],
            'title' => ['required', 'string', 'max:255'],
            'value' => ['nullable', 'string', 'max:120'],
            'unit' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:40'],
            'color' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }
}
