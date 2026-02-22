<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Village;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        $announcements = Announcement::query()
            ->latest()
            ->paginate(10);

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create(): View
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'reference_url' => ['nullable', 'url', 'max:255'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $village = Village::query()->first();

        if (!$village) {
            return back()->withErrors(['title' => 'Data desa belum tersedia.'])->withInput();
        }

        $validated['village_id'] = $village->id;
        $validated['is_published'] = (bool) ($validated['is_published'] ?? false);
        $validated['published_at'] = $validated['is_published'] ? now() : null;

        Announcement::query()->create($validated);

        return redirect()->route('admin.announcements.index')->with('status', 'Pengumuman berhasil ditambahkan.');
    }

    public function edit(Announcement $announcement): View
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'reference_url' => ['nullable', 'url', 'max:255'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $announcement->fill($validated);
        $announcement->is_published = (bool) ($validated['is_published'] ?? false);
        $announcement->published_at = $announcement->is_published ? ($announcement->published_at ?? now()) : null;
        $announcement->save();

        return redirect()->route('admin.announcements.index')->with('status', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $announcement->delete();

        return redirect()->route('admin.announcements.index')->with('status', 'Pengumuman berhasil dihapus.');
    }
}
