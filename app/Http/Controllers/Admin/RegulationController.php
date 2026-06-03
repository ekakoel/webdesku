<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Village;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class RegulationController extends Controller
{
    public function index(): View
    {
        $keyword = trim((string) request()->query('q', ''));

        $regulations = Announcement::query()
            ->where('type', Announcement::TYPE_PERATURAN)
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->where('title', 'like', "%{$keyword}%")
                        ->orWhere('content', 'like', "%{$keyword}%")
                        ->orWhere('attachment_name', 'like', "%{$keyword}%");
                });
            })
            ->latest()
            ->paginate(10);

        return view('admin.regulations.index', [
            'regulations' => $regulations,
            'keyword' => $keyword,
        ]);
    }

    public function create(): View
    {
        return view('admin.regulations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'attachment' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            'is_published' => ['nullable', 'boolean'],
        ], [
            'attachment.required' => 'File peraturan wajib diunggah.',
            'attachment.mimes' => 'File peraturan harus berupa PDF/JPG/JPEG/PNG/WEBP.',
        ]);

        $village = Village::query()->first();
        if (!$village) {
            return back()->withErrors(['title' => 'Data desa belum tersedia.'])->withInput();
        }

        $validated['village_id'] = $village->id;
        $validated['type'] = Announcement::TYPE_PERATURAN;
        $validated['is_published'] = (bool) ($validated['is_published'] ?? false);
        $validated['published_at'] = $validated['is_published'] ? now() : null;
        $validated['attachment_path'] = $request->file('attachment')->store('regulations/files', 'public');
        $validated['attachment_name'] = $request->file('attachment')->getClientOriginalName();

        unset($validated['attachment']);
        Announcement::query()->create($validated);

        return redirect()->route('admin.regulations.index')->with('status', 'Peraturan desa berhasil ditambahkan.');
    }

    public function edit(Announcement $regulation): View
    {
        $this->ensureRegulation($regulation);

        return view('admin.regulations.edit', [
            'regulation' => $regulation,
        ]);
    }

    public function update(Request $request, Announcement $regulation): RedirectResponse
    {
        $this->ensureRegulation($regulation);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            'remove_attachment' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
        ], [
            'attachment.mimes' => 'File peraturan harus berupa PDF/JPG/JPEG/PNG/WEBP.',
        ]);

        $regulation->fill($validated);
        $regulation->type = Announcement::TYPE_PERATURAN;
        $regulation->is_published = (bool) ($validated['is_published'] ?? false);
        $regulation->published_at = $regulation->is_published ? ($regulation->published_at ?? now()) : null;

        if ((bool) $request->boolean('remove_attachment') && $regulation->hasLocalAttachment() && Storage::disk('public')->exists($regulation->attachment_path)) {
            Storage::disk('public')->delete($regulation->attachment_path);
            $regulation->attachment_path = null;
            $regulation->attachment_name = null;
        }

        if ($request->hasFile('attachment')) {
            if ($regulation->hasLocalAttachment() && Storage::disk('public')->exists($regulation->attachment_path)) {
                Storage::disk('public')->delete($regulation->attachment_path);
            }
            $regulation->attachment_path = $request->file('attachment')->store('regulations/files', 'public');
            $regulation->attachment_name = $request->file('attachment')->getClientOriginalName();
        }

        $regulation->save();

        return redirect()->route('admin.regulations.index')->with('status', 'Peraturan desa berhasil diperbarui.');
    }

    public function destroy(Announcement $regulation): RedirectResponse
    {
        $this->ensureRegulation($regulation);

        if ($regulation->hasLocalAttachment() && Storage::disk('public')->exists($regulation->attachment_path)) {
            Storage::disk('public')->delete($regulation->attachment_path);
        }

        $regulation->delete();

        return redirect()->route('admin.regulations.index')->with('status', 'Peraturan desa berhasil dihapus.');
    }

    private function ensureRegulation(Announcement $announcement): void
    {
        abort_unless($announcement->type === Announcement::TYPE_PERATURAN, 404);
    }
}

