<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Models\VillageOfficial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VillageOfficialController extends Controller
{
    public function index(): View
    {
        $officials = VillageOfficial::query()
            ->orderByDesc('is_highlighted')
            ->orderBy('sort_order')
            ->latest()
            ->paginate(12);

        return view('admin.officials.index', compact('officials'));
    }

    public function create(): View
    {
        return view('admin.officials.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'unit' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_highlighted' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $village = Village::query()->first();
        if (!$village) {
            return back()->withErrors(['name' => 'Data desa belum tersedia.'])->withInput();
        }

        $photoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('officials', 'public')
            : null;

        VillageOfficial::query()->create([
            'village_id' => $village->id,
            'name' => $validated['name'],
            'position' => $validated['position'],
            'unit' => $validated['unit'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'photo_path' => $photoPath,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_highlighted' => (bool) ($validated['is_highlighted'] ?? false),
            'is_published' => (bool) ($validated['is_published'] ?? false),
            'published_at' => ($validated['is_published'] ?? false) ? now() : null,
        ]);

        return redirect()->route('admin.officials.index')->with('status', 'Data aparatur berhasil ditambahkan.');
    }

    public function edit(VillageOfficial $official): View
    {
        return view('admin.officials.edit', compact('official'));
    }

    public function update(Request $request, VillageOfficial $official): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'unit' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_highlighted' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('photo')) {
            if ($official->photo_path) {
                Storage::disk('public')->delete($official->photo_path);
            }
            $official->photo_path = $request->file('photo')->store('officials', 'public');
        }

        $official->fill([
            'name' => $validated['name'],
            'position' => $validated['position'],
            'unit' => $validated['unit'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        $official->is_highlighted = (bool) ($validated['is_highlighted'] ?? false);
        $official->is_published = (bool) ($validated['is_published'] ?? false);
        $official->published_at = $official->is_published ? ($official->published_at ?? now()) : null;
        $official->save();

        return redirect()->route('admin.officials.index')->with('status', 'Data aparatur berhasil diperbarui.');
    }

    public function destroy(VillageOfficial $official): RedirectResponse
    {
        if ($official->photo_path) {
            Storage::disk('public')->delete($official->photo_path);
        }

        $official->delete();

        return redirect()->route('admin.officials.index')->with('status', 'Data aparatur berhasil dihapus.');
    }
}

