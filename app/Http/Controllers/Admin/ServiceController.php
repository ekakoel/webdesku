<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\Village;
use App\Models\VillageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        $services = VillageService::query()
            ->withCount('requests')
            ->orderByDesc('is_featured')
            ->latest()
            ->paginate(10);

        return view('admin.services.index', compact('services'));
    }

    public function create(): View
    {
        return view('admin.services.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'process' => ['nullable', 'string'],
            'sla_target_hours' => ['required', 'integer', 'min:1', 'max:720'],
            'icon' => ['nullable', 'string', 'max:10'],
            'is_featured' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $village = Village::query()->first();

        if (!$village) {
            return back()->withErrors(['name' => 'Data desa belum tersedia.'])->withInput();
        }

        $validated['village_id'] = $village->id;
        $validated['slug'] = $this->makeUniqueSlug($validated['name']);
        $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);
        $validated['is_published'] = (bool) ($validated['is_published'] ?? false);
        $validated['published_at'] = $validated['is_published'] ? now() : null;

        VillageService::query()->create($validated);

        return redirect()->route('admin.services.index')->with('status', 'Layanan berhasil ditambahkan.');
    }

    public function edit(VillageService $service): View
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, VillageService $service): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'process' => ['nullable', 'string'],
            'sla_target_hours' => ['required', 'integer', 'min:1', 'max:720'],
            'icon' => ['nullable', 'string', 'max:10'],
            'is_featured' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $service->fill($validated);

        if ($service->isDirty('name')) {
            $service->slug = $this->makeUniqueSlug($validated['name'], $service->id);
        }

        $service->is_featured = (bool) ($validated['is_featured'] ?? false);
        $service->is_published = (bool) ($validated['is_published'] ?? false);
        $service->published_at = $service->is_published ? ($service->published_at ?? now()) : null;
        $service->save();

        return redirect()->route('admin.services.index')->with('status', 'Layanan berhasil diperbarui.');
    }

    public function destroy(VillageService $service): RedirectResponse
    {
        $attachments = ServiceRequest::query()
            ->where('service_id', $service->id)
            ->whereNotNull('attachment_path')
            ->pluck('attachment_path');

        foreach ($attachments as $path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        $service->delete();

        return redirect()->route('admin.services.index')->with('status', 'Layanan berhasil dihapus.');
    }

    private function makeUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $base = $base !== '' ? $base : 'layanan';
        $slug = $base;
        $counter = 1;

        while (
            VillageService::query()
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
