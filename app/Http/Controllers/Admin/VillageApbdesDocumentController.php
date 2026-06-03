<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Models\VillageApbdesDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VillageApbdesDocumentController extends Controller
{
    public function index(Request $request): View
    {
        $year = (int) $request->query('year', 0);
        $q = trim((string) $request->query('q', ''));

        $items = VillageApbdesDocument::query()
            ->when($year > 0, fn ($query) => $query->where('fiscal_year', $year))
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($subQuery) use ($q) {
                    $subQuery->where('title', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('fiscal_year')
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $years = VillageApbdesDocument::query()
            ->select('fiscal_year')
            ->whereNotNull('fiscal_year')
            ->distinct()
            ->orderByDesc('fiscal_year')
            ->pluck('fiscal_year');

        return view('admin.village-apbdes-documents.index', [
            'items' => $items,
            'years' => $years,
            'year' => $year > 0 ? $year : null,
            'q' => $q,
        ]);
    }

    public function create(): View
    {
        return view('admin.village-apbdes-documents.create', [
            'item' => new VillageApbdesDocument(),
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
            $validated['document_path'] = $request->file('document_file')->store('apbdes-documents', 'public');
        }

        VillageApbdesDocument::query()->create($validated);

        return redirect()->route('admin.village-apbdes-documents.index')->with('status', 'Dokumen/Laporan APBDes berhasil ditambahkan.');
    }

    public function edit(VillageApbdesDocument $villageApbdesDocument): View
    {
        return view('admin.village-apbdes-documents.edit', [
            'item' => $villageApbdesDocument,
        ]);
    }

    public function update(Request $request, VillageApbdesDocument $villageApbdesDocument): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $villageApbdesDocument->fill($validated);
        $villageApbdesDocument->is_published = (bool) ($validated['is_published'] ?? false);
        $villageApbdesDocument->published_at = $villageApbdesDocument->is_published ? ($villageApbdesDocument->published_at ?? now()) : null;

        if ($request->hasFile('document_file')) {
            if ($villageApbdesDocument->document_path && Storage::disk('public')->exists($villageApbdesDocument->document_path)) {
                Storage::disk('public')->delete($villageApbdesDocument->document_path);
            }
            $villageApbdesDocument->document_path = $request->file('document_file')->store('apbdes-documents', 'public');
        }

        $villageApbdesDocument->save();

        return redirect()->route('admin.village-apbdes-documents.index')->with('status', 'Dokumen/Laporan APBDes berhasil diperbarui.');
    }

    public function destroy(VillageApbdesDocument $villageApbdesDocument): RedirectResponse
    {
        if ($villageApbdesDocument->document_path && Storage::disk('public')->exists($villageApbdesDocument->document_path)) {
            Storage::disk('public')->delete($villageApbdesDocument->document_path);
        }
        $villageApbdesDocument->delete();

        return redirect()->route('admin.village-apbdes-documents.index')->with('status', 'Dokumen/Laporan APBDes berhasil dihapus.');
    }

    private function rules(): array
    {
        return [
            'fiscal_year' => ['nullable', 'integer', 'min:1990', 'max:2100'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'document_url' => ['nullable', 'url', 'max:2000'],
            'document_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }
}
