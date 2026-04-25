<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementImage;
use App\Models\Village;
use App\Services\GoogleMapsLinkResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        $type = (string) request()->query('type', 'all');
        $keyword = trim((string) request()->query('q', ''));

        $announcements = Announcement::query()
            ->with('images')
            ->when($type !== 'all', fn ($query) => $query->where('type', $type))
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->where('title', 'like', "%{$keyword}%")
                        ->orWhere('content', 'like', "%{$keyword}%");
                });
            })
            ->latest()
            ->paginate(10);

        return view('admin.announcements.index', [
            'announcements' => $announcements,
            'typeOptions' => Announcement::typeOptions(),
            'selectedType' => $type,
            'keyword' => $keyword,
        ]);
    }

    public function create(): View
    {
        return view('admin.announcements.create', [
            'typeOptions' => Announcement::typeOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(array_keys(Announcement::typeOptions()))],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'reference_url' => ['nullable', 'url', 'max:255'],
            'location_name' => ['nullable', 'string', 'max:255'],
            'map_url' => ['nullable', 'url', 'max:2000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,csv', 'max:10240'],
            'images' => ['nullable', 'array', 'max:3'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $village = Village::query()->first();

        if (!$village) {
            return back()->withErrors(['title' => 'Data desa belum tersedia.'])->withInput();
        }

        $this->resolveAnnouncementMapCoordinates($validated);
        $validated['village_id'] = $village->id;
        $validated['is_published'] = (bool) ($validated['is_published'] ?? false);
        $validated['published_at'] = $validated['is_published'] ? now() : null;
        $validated['attachment_path'] = $request->hasFile('attachment')
            ? $request->file('attachment')->store('announcements/files', 'public')
            : null;
        $validated['attachment_name'] = $request->hasFile('attachment')
            ? $request->file('attachment')->getClientOriginalName()
            : null;

        unset($validated['attachment'], $validated['images']);

        $announcement = Announcement::query()->create($validated);
        $this->storeAnnouncementImages($announcement, $request->file('images', []));

        return redirect()->route('admin.announcements.index')->with('status', 'Pengumuman berhasil ditambahkan.');
    }

    public function edit(Announcement $announcement): View
    {
        return view('admin.announcements.edit', [
            'announcement' => $announcement,
            'typeOptions' => Announcement::typeOptions(),
        ]);
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(array_keys(Announcement::typeOptions()))],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'reference_url' => ['nullable', 'url', 'max:255'],
            'location_name' => ['nullable', 'string', 'max:255'],
            'map_url' => ['nullable', 'url', 'max:2000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,csv', 'max:10240'],
            'remove_attachment' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array', 'max:3'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_image_ids' => ['nullable', 'array'],
            'remove_image_ids.*' => ['integer', Rule::exists('announcement_images', 'id')],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $this->resolveAnnouncementMapCoordinates($validated);
        $announcement->fill($validated);
        $announcement->is_published = (bool) ($validated['is_published'] ?? false);
        $announcement->published_at = $announcement->is_published ? ($announcement->published_at ?? now()) : null;

        if ((bool) $request->boolean('remove_attachment') && $announcement->hasLocalAttachment() && Storage::disk('public')->exists($announcement->attachment_path)) {
            Storage::disk('public')->delete($announcement->attachment_path);
            $announcement->attachment_path = null;
            $announcement->attachment_name = null;
        }

        if ($request->hasFile('attachment')) {
            if ($announcement->hasLocalAttachment() && Storage::disk('public')->exists($announcement->attachment_path)) {
                Storage::disk('public')->delete($announcement->attachment_path);
            }
            $announcement->attachment_path = $request->file('attachment')->store('announcements/files', 'public');
            $announcement->attachment_name = $request->file('attachment')->getClientOriginalName();
        }

        $announcement->save();
        $announcement->load('images');

        $this->removeSelectedImages($announcement, $request->input('remove_image_ids', []));
        $existingCount = $announcement->images()->count();
        $slots = max(0, 3 - $existingCount);
        $newImages = array_slice((array) $request->file('images', []), 0, $slots);
        $this->storeAnnouncementImages($announcement, $newImages);

        return redirect()->route('admin.announcements.index')->with('status', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $announcement->load('images');
        foreach ($announcement->images as $image) {
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
        }

        if ($announcement->hasLocalAttachment() && Storage::disk('public')->exists($announcement->attachment_path)) {
            Storage::disk('public')->delete($announcement->attachment_path);
        }

        $announcement->delete();

        return redirect()->route('admin.announcements.index')->with('status', 'Pengumuman berhasil dihapus.');
    }

    public function resolveMapLink(Request $request, GoogleMapsLinkResolver $resolver): JsonResponse
    {
        $validated = $request->validate([
            'map_url' => ['required', 'url', 'max:2000'],
        ]);

        $resolved = $resolver->resolve($validated['map_url']);
        if (!$resolved) {
            return response()->json([
                'ok' => false,
                'message' => 'Link Google Maps tidak bisa diproses. Pastikan link valid dan dapat diakses.',
            ], 422);
        }

        return response()->json([
            'ok' => true,
            'latitude' => $resolved['latitude'],
            'longitude' => $resolved['longitude'],
            'final_url' => $resolved['final_url'],
        ]);
    }

    private function resolveAnnouncementMapCoordinates(array &$validated): void
    {
        $mapUrl = trim((string) ($validated['map_url'] ?? ''));
        $hasLat = isset($validated['latitude']) && $validated['latitude'] !== null && $validated['latitude'] !== '';
        $hasLng = isset($validated['longitude']) && $validated['longitude'] !== null && $validated['longitude'] !== '';

        if ($mapUrl === '' || ($hasLat && $hasLng)) {
            return;
        }

        $resolver = app(GoogleMapsLinkResolver::class);
        $resolved = $resolver->resolve($mapUrl);

        if (!empty($resolved['latitude']) && !empty($resolved['longitude'])) {
            $validated['latitude'] = $resolved['latitude'];
            $validated['longitude'] = $resolved['longitude'];
            $validated['map_url'] = $resolved['final_url'] ?? $mapUrl;
        }
    }

    private function storeAnnouncementImages(Announcement $announcement, array $images): void
    {
        $count = $announcement->images()->count();
        foreach ($images as $image) {
            if (!$image instanceof UploadedFile || $count >= 3) {
                continue;
            }

            AnnouncementImage::query()->create([
                'announcement_id' => $announcement->id,
                'image_path' => $image->store('announcements/images', 'public'),
                'sort_order' => $count,
            ]);
            $count++;
        }
    }

    private function removeSelectedImages(Announcement $announcement, array $ids): void
    {
        if ($ids === []) {
            return;
        }

        $images = $announcement->images()->whereIn('id', $ids)->get();
        foreach ($images as $image) {
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            $image->delete();
        }

        $remaining = $announcement->images()->orderBy('sort_order')->orderBy('id')->get();
        foreach ($remaining as $index => $image) {
            if ($image->sort_order !== $index) {
                $image->sort_order = $index;
                $image->save();
            }
        }
    }
}
