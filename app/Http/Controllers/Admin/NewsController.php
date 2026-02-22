<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsImage;
use App\Models\Village;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class NewsController extends Controller
{
    public function index(): View
    {
        $news = News::query()
            ->latest()
            ->paginate(10);

        return view('admin.news.index', compact('news'));
    }

    public function create(): View
    {
        return view('admin.news.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'gallery_images' => ['nullable', 'array', 'max:4'],
            'gallery_images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $village = Village::query()->first();

        if (!$village) {
            return back()->withErrors(['title' => 'Data desa belum tersedia.'])->withInput();
        }

        $validated['village_id'] = $village->id;
        if (Schema::hasColumn('news', 'created_by')) {
            $validated['created_by'] = auth()->id();
        }
        $validated['slug'] = $this->makeUniqueSlug($validated['title']);
        $validated['is_published'] = (bool) ($validated['is_published'] ?? false);
        $validated['published_at'] = $validated['is_published'] ? now() : null;
        $validated['thumbnail'] = $request->hasFile('thumbnail')
            ? $this->storeOptimizedImage($request->file('thumbnail'), 'news', 'thumbnail')
            : null;

        unset($validated['gallery_images']);
        $news = News::query()->create($validated);

        $this->storeGalleryImages($news, $request->file('gallery_images', []));

        return redirect()->route('admin.news.index')->with('status', 'Berita berhasil ditambahkan.');
    }

    public function edit(News $news): View
    {
        $news->load('images');

        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, News $news): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_image_ids' => ['nullable', 'array'],
            'remove_image_ids.*' => ['integer'],
            'remove_cover' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        unset($validated['thumbnail']);
        unset($validated['gallery_images']);
        unset($validated['remove_image_ids']);
        unset($validated['remove_cover']);
        $news->fill($validated);

        if ($news->isDirty('title')) {
            $news->slug = $this->makeUniqueSlug($validated['title'], $news->id);
        }

        $news->is_published = (bool) ($validated['is_published'] ?? false);
        $news->published_at = $news->is_published
            ? ($news->published_at ?? now())
            : null;

        $removeCover = (bool) $request->boolean('remove_cover');
        if ($removeCover && $news->hasLocalThumbnail() && Storage::disk('public')->exists($news->thumbnail)) {
            Storage::disk('public')->delete($news->thumbnail);
            $news->thumbnail = null;
        }

        if ($request->hasFile('thumbnail')) {
            if ($news->hasLocalThumbnail() && Storage::disk('public')->exists($news->thumbnail)) {
                Storage::disk('public')->delete($news->thumbnail);
            }

            $news->thumbnail = $this->storeOptimizedImage($request->file('thumbnail'), 'news', 'thumbnail');
        }

        $news->save();
        $news->load('images');

        $this->removeSelectedGalleryImages($news, $request->input('remove_image_ids', []));

        $existingCount = $news->images()->count();
        $newImages = $request->file('gallery_images', []);
        if (!is_array($newImages)) {
            $newImages = [$newImages];
        }
        $slots = max(0, 4 - $existingCount);
        $newImages = array_slice($newImages, 0, $slots);
        $this->storeGalleryImages($news, $newImages);

        return redirect()->route('admin.news.index')->with('status', 'Berita berhasil diperbarui.');
    }

    public function destroy(News $news): RedirectResponse
    {
        if ($news->hasLocalThumbnail() && Storage::disk('public')->exists($news->thumbnail)) {
            Storage::disk('public')->delete($news->thumbnail);
        }

        $news->load('images');
        foreach ($news->images as $image) {
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
        }

        $news->delete();

        return redirect()->route('admin.news.index')->with('status', 'Berita berhasil dihapus.');
    }

    private function storeGalleryImages(News $news, array $images): void
    {
        $count = $news->images()->count();

        foreach ($images as $image) {
            if (!$image instanceof UploadedFile || $count >= 4) {
                continue;
            }

            NewsImage::query()->create([
                'news_id' => $news->id,
                'image_path' => $this->storeOptimizedImage($image, 'news/gallery', 'gallery_images'),
                'sort_order' => $count,
            ]);
            $count++;
        }
    }

    private function removeSelectedGalleryImages(News $news, array $ids): void
    {
        if ($ids === []) {
            return;
        }

        $images = $news->images()->whereIn('id', $ids)->get();

        foreach ($images as $image) {
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            $image->delete();
        }

        $remaining = $news->images()->orderBy('sort_order')->orderBy('id')->get();
        foreach ($remaining as $index => $image) {
            if ($image->sort_order !== $index) {
                $image->sort_order = $index;
                $image->save();
            }
        }
    }

    private function storeOptimizedImage(UploadedFile $image, string $directory, string $errorField = 'thumbnail'): string
    {
        $extension = strtolower((string) $image->getClientOriginalExtension());
        $quality = 78;

        try {
            if (!function_exists('imagewebp')) {
                throw new \RuntimeException('Server belum mendukung fungsi WEBP (GD imagewebp).');
            }

            $imageInfo = @getimagesize($image->getRealPath());
            if (!$imageInfo || !isset($imageInfo[0], $imageInfo[1], $imageInfo[2])) {
                throw new \RuntimeException('Gambar tidak valid.');
            }

            [$sourceWidth, $sourceHeight, $sourceType] = $imageInfo;

            $source = match ($sourceType) {
                IMAGETYPE_JPEG => @imagecreatefromjpeg($image->getRealPath()),
                IMAGETYPE_PNG => @imagecreatefrompng($image->getRealPath()),
                IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($image->getRealPath()) : false,
                default => false,
            };

            if (!$source) {
                throw new \RuntimeException('Format gambar tidak didukung GD.');
            }

            $maxWidth = 1600;
            $scale = $sourceWidth > $maxWidth ? ($maxWidth / $sourceWidth) : 1;
            $targetWidth = (int) max(1, round($sourceWidth * $scale));
            $targetHeight = (int) max(1, round($sourceHeight * $scale));

            $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
            if (in_array($sourceType, [IMAGETYPE_PNG, IMAGETYPE_WEBP], true)) {
                imagealphablending($canvas, false);
                imagesavealpha($canvas, true);
                $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
                imagefill($canvas, 0, 0, $transparent);
            }

            imagecopyresampled(
                $canvas,
                $source,
                0,
                0,
                0,
                0,
                $targetWidth,
                $targetHeight,
                $sourceWidth,
                $sourceHeight
            );

            ob_start();
            $targetExtension = 'webp';
            $success = imagewebp($canvas, null, $quality);
            $binary = (string) ob_get_clean();

            imagedestroy($source);
            imagedestroy($canvas);

            if (!$success || $binary === '') {
                throw new \RuntimeException('Gagal membuat output gambar.');
            }

            $fileName = Str::uuid()->toString().'.'.$targetExtension;
            $path = trim($directory, '/').'/'.$fileName;
            Storage::disk('public')->put($path, $binary);

            return $path;
        } catch (\Throwable $e) {
            Log::warning('Optimasi/konversi WEBP gambar berita gagal.', [
                'error' => $e->getMessage(),
                'name' => $image->getClientOriginalName(),
                'extension' => $extension,
            ]);

            throw ValidationException::withMessages([
                $errorField => 'Upload gambar gagal. Pastikan file valid dan server mendukung konversi WEBP.',
            ]);
        }
    }

    private function makeUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $base = $base !== '' ? $base : 'berita';
        $slug = $base;
        $counter = 1;

        while (
            News::query()
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
