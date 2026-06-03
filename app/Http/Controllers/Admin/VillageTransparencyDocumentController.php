<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Models\VillageTransparencyDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VillageTransparencyDocumentController extends Controller
{
    public function index(Request $request): View
    {
        $year = (int) $request->query('year', 0);
        $category = (string) $request->query('category', 'all');
        $q = trim((string) $request->query('q', ''));

        $items = VillageTransparencyDocument::query()
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

        $years = VillageTransparencyDocument::query()
            ->select('fiscal_year')
            ->whereNotNull('fiscal_year')
            ->distinct()
            ->orderByDesc('fiscal_year')
            ->pluck('fiscal_year');

        return view('admin.village-transparency-documents.index', [
            'items' => $items,
            'years' => $years,
            'year' => $year > 0 ? $year : null,
            'category' => $category,
            'q' => $q,
            'categories' => VillageTransparencyDocument::categoryOptions(),
        ]);
    }

    public function create(): View
    {
        return view('admin.village-transparency-documents.create', [
            'item' => new VillageTransparencyDocument(),
            'categories' => VillageTransparencyDocument::categoryOptions(),
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

        if ($request->hasFile('document_file')) {
            $validated['document_path'] = $request->file('document_file')->store('transparency-documents', 'public');
        }

        VillageTransparencyDocument::query()->create($validated);

        return redirect()->route('admin.village-transparency-documents.index')->with('status', 'Dokumen transparansi berhasil ditambahkan.');
    }

    public function edit(VillageTransparencyDocument $villageTransparencyDocument): View
    {
        return view('admin.village-transparency-documents.edit', [
            'item' => $villageTransparencyDocument,
            'categories' => VillageTransparencyDocument::categoryOptions(),
        ]);
    }

    public function update(Request $request, VillageTransparencyDocument $villageTransparencyDocument): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $villageTransparencyDocument->fill($validated);
        $villageTransparencyDocument->is_published = (bool) ($validated['is_published'] ?? false);
        $villageTransparencyDocument->published_at = $villageTransparencyDocument->is_published ? ($villageTransparencyDocument->published_at ?? now()) : null;

        if ($request->hasFile('document_file')) {
            if ($villageTransparencyDocument->document_path && Storage::disk('public')->exists($villageTransparencyDocument->document_path)) {
                Storage::disk('public')->delete($villageTransparencyDocument->document_path);
            }
            $villageTransparencyDocument->document_path = $request->file('document_file')->store('transparency-documents', 'public');
        }

        $villageTransparencyDocument->save();

        return redirect()->route('admin.village-transparency-documents.index')->with('status', 'Dokumen transparansi berhasil diperbarui.');
    }

    public function destroy(VillageTransparencyDocument $villageTransparencyDocument): RedirectResponse
    {
        if ($villageTransparencyDocument->document_path && Storage::disk('public')->exists($villageTransparencyDocument->document_path)) {
            Storage::disk('public')->delete($villageTransparencyDocument->document_path);
        }
        $villageTransparencyDocument->delete();

        return redirect()->route('admin.village-transparency-documents.index')->with('status', 'Dokumen transparansi berhasil dihapus.');
    }

    private function rules(): array
    {
        return [
            'fiscal_year' => ['nullable', 'integer', 'min:1990', 'max:2100'],
            'category' => ['required', 'string', 'in:'.implode(',', array_keys(VillageTransparencyDocument::categoryOptions()))],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'document_url' => ['nullable', 'url', 'max:2000'],
            'document_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }
}
