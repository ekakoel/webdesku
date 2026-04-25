<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Models\VillageApbdesItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VillageApbdesItemController extends Controller
{
    public function index(Request $request): View
    {
        $year = (int) $request->query('year', 0);

        $items = VillageApbdesItem::query()
            ->when($year > 0, fn ($query) => $query->where('fiscal_year', $year))
            ->orderByDesc('fiscal_year')
            ->orderBy('type')
            ->orderBy('sort_order')
            ->paginate(15)
            ->withQueryString();

        $years = VillageApbdesItem::query()
            ->select('fiscal_year')
            ->distinct()
            ->orderByDesc('fiscal_year')
            ->pluck('fiscal_year');

        return view('admin.village-apbdes-items.index', [
            'items' => $items,
            'year' => $year > 0 ? $year : null,
            'years' => $years,
            'types' => VillageApbdesItem::TYPES,
        ]);
    }

    public function create(): View
    {
        return view('admin.village-apbdes-items.create', [
            'item' => new VillageApbdesItem(),
            'types' => VillageApbdesItem::TYPES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $village = Village::query()->first();

        if (!$village) {
            return back()->withErrors(['fiscal_year' => 'Data desa belum tersedia.'])->withInput();
        }

        $validated['village_id'] = $village->id;
        $validated['is_published'] = (bool) ($validated['is_published'] ?? false);
        $validated['published_at'] = $validated['is_published'] ? now() : null;

        VillageApbdesItem::query()->create($validated);

        return redirect()->route('admin.village-apbdes-items.index')->with('status', 'Data APBDes berhasil ditambahkan.');
    }

    public function edit(VillageApbdesItem $villageApbdesItem): View
    {
        return view('admin.village-apbdes-items.edit', [
            'item' => $villageApbdesItem,
            'types' => VillageApbdesItem::TYPES,
        ]);
    }

    public function update(Request $request, VillageApbdesItem $villageApbdesItem): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $villageApbdesItem->fill($validated);
        $villageApbdesItem->is_published = (bool) ($validated['is_published'] ?? false);
        $villageApbdesItem->published_at = $villageApbdesItem->is_published ? ($villageApbdesItem->published_at ?? now()) : null;
        $villageApbdesItem->save();

        return redirect()->route('admin.village-apbdes-items.index')->with('status', 'Data APBDes berhasil diperbarui.');
    }

    public function destroy(VillageApbdesItem $villageApbdesItem): RedirectResponse
    {
        $villageApbdesItem->delete();

        return redirect()->route('admin.village-apbdes-items.index')->with('status', 'Data APBDes berhasil dihapus.');
    }

    private function rules(): array
    {
        return [
            'fiscal_year' => ['required', 'integer', 'min:1990', 'max:2100'],
            'type' => ['required', 'in:pendapatan,belanja,pembiayaan'],
            'category' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }
}

