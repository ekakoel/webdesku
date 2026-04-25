<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\Village;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(): View
    {
        $galleries = Gallery::query()
            ->latest()
            ->paginate(12);

        return view('admin.galleries.index', compact('galleries'));
    }

    public function create(): View
    {
        return view('admin.galleries.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'caption' => ['nullable', 'string'],
            'image' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'category' => ['nullable', 'string', 'max:100'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $village = Village::query()->first();

        if (!$village) {
            return back()->withErrors(['title' => 'Data desa belum tersedia.'])->withInput();
        }

        $media = $this->storeOptimizedGalleryImage($request->file('image'));

        Gallery::query()->create([
            'village_id' => $village->id,
            'title' => $validated['title'],
            'caption' => $validated['caption'] ?? null,
            'image_url' => $media['image_path'],
            'thumbnail_path' => $media['thumbnail_path'],
            'category' => $validated['category'] ?? null,
            'is_published' => (bool) ($validated['is_published'] ?? false),
            'published_at' => (bool) ($validated['is_published'] ?? false) ? now() : null,
        ]);

        return redirect()->route('admin.galleries.index')->with('status', 'Galeri berhasil ditambahkan.');
    }

    public function edit(Gallery $gallery): View
    {
        return view('admin.galleries.edit', compact('gallery'));
    }

    public function update(Request $request, Gallery $gallery): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'caption' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],
            'category' => ['nullable', 'string', 'max:100'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $shouldRemoveImage = (bool) ($validated['remove_image'] ?? false);
        if ($shouldRemoveImage && $gallery->hasLocalImage()) {
            $this->deleteGalleryMedia($gallery);
            $gallery->image_url = null;
            $gallery->thumbnail_path = null;
        }

        if ($request->hasFile('image')) {
            $this->deleteGalleryMedia($gallery);

            $media = $this->storeOptimizedGalleryImage($request->file('image'));
            $gallery->image_url = $media['image_path'];
            $gallery->thumbnail_path = $media['thumbnail_path'];
        }

        $gallery->title = $validated['title'];
        $gallery->caption = $validated['caption'] ?? null;
        $gallery->category = $validated['category'] ?? null;
        $gallery->is_published = (bool) ($validated['is_published'] ?? false);
        $gallery->published_at = $gallery->is_published ? ($gallery->published_at ?? now()) : null;
        $gallery->save();

        return redirect()->route('admin.galleries.index')->with('status', 'Galeri berhasil diperbarui.');
    }

    public function destroy(Gallery $gallery): RedirectResponse
    {
        $this->deleteGalleryMedia($gallery);

        $gallery->delete();

        return redirect()->route('admin.galleries.index')->with('status', 'Galeri berhasil dihapus.');
    }

    private function deleteGalleryMedia(Gallery $gallery): void
    {
        $rawImage = (string) ($gallery->getRawOriginal('image_url') ?? '');
        $rawThumb = (string) ($gallery->getRawOriginal('thumbnail_path') ?? '');

        if ($rawImage !== '' && !Str::startsWith($rawImage, ['http://', 'https://', '//'])) {
            Storage::disk('public')->delete($rawImage);
        }

        if ($rawThumb !== '' && !Str::startsWith($rawThumb, ['http://', 'https://', '//'])) {
            Storage::disk('public')->delete($rawThumb);
        }
    }

    private function storeOptimizedGalleryImage(UploadedFile $image): array
    {
        $fullBinary = $this->convertImageToWebpBinary($image, 1600, 80, 'image');
        $thumbBinary = $this->convertImageToWebpBinary($image, 560, 74, 'image');

        $baseName = Str::uuid()->toString();
        $imagePath = 'galleries/'.$baseName.'.webp';
        $thumbnailPath = 'galleries/thumbs/'.$baseName.'.webp';

        Storage::disk('public')->put($imagePath, $fullBinary);
        Storage::disk('public')->put($thumbnailPath, $thumbBinary);

        return [
            'image_path' => $imagePath,
            'thumbnail_path' => $thumbnailPath,
        ];
    }

    private function convertImageToWebpBinary(
        UploadedFile $image,
        int $maxWidth,
        int $quality,
        string $errorField = 'image'
    ): string {
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

            $scale = $sourceWidth > $maxWidth ? ($maxWidth / $sourceWidth) : 1;
            $targetWidth = (int) max(1, round($sourceWidth * $scale));
            $targetHeight = (int) max(1, round($sourceHeight * $scale));

            $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
            imagefill($canvas, 0, 0, $transparent);

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
            $success = imagewebp($canvas, null, $quality);
            $binary = (string) ob_get_clean();

            imagedestroy($source);
            imagedestroy($canvas);

            if (!$success || $binary === '') {
                throw new \RuntimeException('Gagal membuat output gambar WEBP.');
            }

            return $binary;
        } catch (\Throwable $e) {
            Log::warning('Optimasi gambar galeri gagal.', [
                'error' => $e->getMessage(),
                'name' => $image->getClientOriginalName(),
            ]);

            throw ValidationException::withMessages([
                $errorField => 'Upload gambar gagal. Pastikan file valid dan server mendukung konversi WEBP.',
            ]);
        }
    }
}
