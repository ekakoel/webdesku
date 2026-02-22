<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Models\Village;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SliderController extends Controller
{
    public function index(): View
    {
        $sliders = Slider::query()
            ->orderBy('sort_order')
            ->latest()
            ->paginate(10);

        return view('admin.sliders.index', compact('sliders'));
    }

    public function create(): View
    {
        return view('admin.sliders.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string'],
            'cta_text' => ['nullable', 'string', 'max:100'],
            'cta_url' => ['nullable', 'url', 'max:255'],
            'image' => ['required', 'image', 'max:4096'],
            'image_alt' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $village = Village::query()->first();
        if (!$village) {
            return back()->withErrors(['image' => 'Data desa belum tersedia.'])->withInput();
        }

        $imagePath = $request->file('image')->store('sliders', 'public');

        Slider::query()->create([
            'village_id' => $village->id,
            'title' => $validated['title'] ?? null,
            'caption' => $validated['caption'] ?? null,
            'cta_text' => $validated['cta_text'] ?? null,
            'cta_url' => $validated['cta_url'] ?? null,
            'image_path' => $imagePath,
            'image_alt' => $validated['image_alt'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => (bool) ($validated['is_active'] ?? false),
            'is_published' => (bool) ($validated['is_published'] ?? false),
            'published_at' => ($validated['is_published'] ?? false) ? now() : null,
        ]);

        return redirect()->route('admin.sliders.index')->with('status', 'Slider berhasil ditambahkan.');
    }

    public function edit(Slider $slider): View
    {
        return view('admin.sliders.edit', compact('slider'));
    }

    public function update(Request $request, Slider $slider): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string'],
            'cta_text' => ['nullable', 'string', 'max:100'],
            'cta_url' => ['nullable', 'url', 'max:255'],
            'image' => ['nullable', 'image', 'max:4096'],
            'image_alt' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            if ($slider->image_path) {
                Storage::disk('public')->delete($slider->image_path);
            }
            $slider->image_path = $request->file('image')->store('sliders', 'public');
        }

        $slider->fill([
            'title' => $validated['title'] ?? null,
            'caption' => $validated['caption'] ?? null,
            'cta_text' => $validated['cta_text'] ?? null,
            'cta_url' => $validated['cta_url'] ?? null,
            'image_alt' => $validated['image_alt'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        $slider->is_active = (bool) ($validated['is_active'] ?? false);
        $slider->is_published = (bool) ($validated['is_published'] ?? false);
        $slider->published_at = $slider->is_published ? ($slider->published_at ?? now()) : null;
        $slider->save();

        return redirect()->route('admin.sliders.index')->with('status', 'Slider berhasil diperbarui.');
    }

    public function destroy(Slider $slider): RedirectResponse
    {
        if ($slider->image_path) {
            Storage::disk('public')->delete($slider->image_path);
        }

        $slider->delete();

        return redirect()->route('admin.sliders.index')->with('status', 'Slider berhasil dihapus.');
    }
}
