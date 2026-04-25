<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Models\VillageTransparencyItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VillageTransparencyItemController extends Controller
{
    public function index(Request $request): View
    {
        $year = (int) $request->query('year', 0);
        $category = (string) $request->query('category', 'all');
        $q = trim((string) $request->query('q', ''));

        $items = VillageTransparencyItem::query()
            ->when($year > 0, fn ($query) => $query->where('fiscal_year', $year))
            ->when($category !== 'all', fn ($query) => $query->where('category', $category))
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($subQuery) use ($q) {
                    $subQuery->where('title', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('fiscal_year')
            ->orderBy('category')
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $years = VillageTransparencyItem::query()
            ->select('fiscal_year')
            ->whereNotNull('fiscal_year')
            ->distinct()
            ->orderByDesc('fiscal_year')
            ->pluck('fiscal_year');

        return view('admin.village-transparency-items.index', [
            'items' => $items,
            'years' => $years,
            'year' => $year > 0 ? $year : null,
            'category' => $category,
            'q' => $q,
            'categories' => VillageTransparencyItem::categoryOptions(),
        ]);
    }

    public function create(): View
    {
        return view('admin.village-transparency-items.create', [
            'item' => new VillageTransparencyItem(),
            'categories' => VillageTransparencyItem::categoryOptions(),
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

        VillageTransparencyItem::query()->create($validated);

        return redirect()->route('admin.village-transparency-items.index')->with('status', 'Data transparansi berhasil ditambahkan.');
    }

    public function edit(VillageTransparencyItem $villageTransparencyItem): View
    {
        return view('admin.village-transparency-items.edit', [
            'item' => $villageTransparencyItem,
            'categories' => VillageTransparencyItem::categoryOptions(),
        ]);
    }

    public function update(Request $request, VillageTransparencyItem $villageTransparencyItem): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $villageTransparencyItem->fill($validated);
        $villageTransparencyItem->is_published = (bool) ($validated['is_published'] ?? false);
        $villageTransparencyItem->published_at = $villageTransparencyItem->is_published ? ($villageTransparencyItem->published_at ?? now()) : null;
        $villageTransparencyItem->save();

        return redirect()->route('admin.village-transparency-items.index')->with('status', 'Data transparansi berhasil diperbarui.');
    }

    public function destroy(VillageTransparencyItem $villageTransparencyItem): RedirectResponse
    {
        $villageTransparencyItem->delete();

        return redirect()->route('admin.village-transparency-items.index')->with('status', 'Data transparansi berhasil dihapus.');
    }

    private function rules(): array
    {
        return [
            'fiscal_year' => ['nullable', 'integer', 'min:1990', 'max:2100'],
            'category' => ['required', 'string', 'in:'.implode(',', array_keys(VillageTransparencyItem::categoryOptions()))],
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'document_url' => ['nullable', 'url', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }
}

