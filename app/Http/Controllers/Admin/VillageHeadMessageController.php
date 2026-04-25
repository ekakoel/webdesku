<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Models\VillageHeadMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VillageHeadMessageController extends Controller
{
    public function index(): View
    {
        $headMessages = VillageHeadMessage::query()
            ->latest()
            ->paginate(10);

        return view('admin.head-messages.index', compact('headMessages'));
    }

    public function create(): View
    {
        return view('admin.head-messages.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'signature' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $village = Village::query()->first();
        if (!$village) {
            return back()->withErrors(['name' => 'Data desa belum tersedia.'])->withInput();
        }

        $photoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('head-messages', 'public')
            : null;

        VillageHeadMessage::query()->create([
            'village_id' => $village->id,
            'name' => $validated['name'],
            'position' => $validated['position'],
            'message' => $validated['message'],
            'signature' => $validated['signature'] ?? null,
            'photo_path' => $photoPath,
            'is_published' => (bool) ($validated['is_published'] ?? false),
            'published_at' => ($validated['is_published'] ?? false) ? now() : null,
        ]);

        return redirect()->route('admin.head-messages.index')->with('status', 'Sambutan kepala desa berhasil ditambahkan.');
    }

    public function edit(VillageHeadMessage $headMessage): View
    {
        return view('admin.head-messages.edit', compact('headMessage'));
    }

    public function update(Request $request, VillageHeadMessage $headMessage): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'signature' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('photo')) {
            if ($headMessage->photo_path) {
                Storage::disk('public')->delete($headMessage->photo_path);
            }
            $headMessage->photo_path = $request->file('photo')->store('head-messages', 'public');
        }

        $headMessage->fill([
            'name' => $validated['name'],
            'position' => $validated['position'],
            'message' => $validated['message'],
            'signature' => $validated['signature'] ?? null,
        ]);

        $headMessage->is_published = (bool) ($validated['is_published'] ?? false);
        $headMessage->published_at = $headMessage->is_published ? ($headMessage->published_at ?? now()) : null;
        $headMessage->save();

        return redirect()->route('admin.head-messages.index')->with('status', 'Sambutan kepala desa berhasil diperbarui.');
    }

    public function destroy(VillageHeadMessage $headMessage): RedirectResponse
    {
        if ($headMessage->photo_path) {
            Storage::disk('public')->delete($headMessage->photo_path);
        }

        $headMessage->delete();

        return redirect()->route('admin.head-messages.index')->with('status', 'Sambutan kepala desa berhasil dihapus.');
    }
}

