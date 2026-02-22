<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Models\VillageAsset;
use App\Services\GoogleMapsLinkResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VillageAssetController extends Controller
{
    public function index(Request $request): View
    {
        $type = (string) $request->query('type', 'all');
        $q = trim((string) $request->query('q', ''));

        $assets = VillageAsset::query()
            ->when($type !== 'all', fn ($query) => $query->where('type', $type))
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($subQuery) use ($q) {
                    $subQuery->where('name', 'like', "%{$q}%")
                        ->orWhere('subcategory', 'like', "%{$q}%")
                        ->orWhere('address', 'like', "%{$q}%");
                });
            })
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.village-assets.index', [
            'assets' => $assets,
            'type' => $type,
            'q' => $q,
            'typeOptions' => VillageAsset::typeOptions(),
        ]);
    }

    public function create(): View
    {
        return view('admin.village-assets.create', [
            'asset' => new VillageAsset(),
            'typeOptions' => VillageAsset::typeOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $this->fillCoordinatesFromMapLink($validated, app(GoogleMapsLinkResolver::class));

        $village = Village::query()->first();
        if (!$village) {
            return back()->withErrors(['name' => 'Data desa belum tersedia.'])->withInput();
        }

        $validated['village_id'] = $village->id;
        $validated['is_published'] = (bool) ($validated['is_published'] ?? false);
        $validated['published_at'] = $validated['is_published'] ? now() : null;
        $validated['icon_path'] = $request->hasFile('icon')
            ? $request->file('icon')->store('village-assets/icons', 'public')
            : null;

        unset($validated['icon'], $validated['remove_icon']);

        VillageAsset::query()->create($validated);

        return redirect()->route('admin.village-assets.index')->with('status', 'Data aset desa berhasil ditambahkan.');
    }

    public function edit(VillageAsset $villageAsset): View
    {
        return view('admin.village-assets.edit', [
            'asset' => $villageAsset,
            'typeOptions' => VillageAsset::typeOptions(),
        ]);
    }

    public function update(Request $request, VillageAsset $villageAsset): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $this->fillCoordinatesFromMapLink($validated, app(GoogleMapsLinkResolver::class));

        if ((bool) ($validated['remove_icon'] ?? false)) {
            if ($villageAsset->hasLocalIcon()) {
                Storage::disk('public')->delete($villageAsset->icon_path);
            }
            $villageAsset->icon_path = null;
        }

        if ($request->hasFile('icon')) {
            if ($villageAsset->hasLocalIcon()) {
                Storage::disk('public')->delete($villageAsset->icon_path);
            }
            $villageAsset->icon_path = $request->file('icon')->store('village-assets/icons', 'public');
        }

        unset($validated['icon'], $validated['remove_icon']);
        $villageAsset->fill($validated);
        $villageAsset->is_published = (bool) ($validated['is_published'] ?? false);
        $villageAsset->published_at = $villageAsset->is_published ? ($villageAsset->published_at ?? now()) : null;
        $villageAsset->save();

        return redirect()->route('admin.village-assets.index')->with('status', 'Data aset desa berhasil diperbarui.');
    }

    public function destroy(VillageAsset $villageAsset): RedirectResponse
    {
        if ($villageAsset->hasLocalIcon()) {
            Storage::disk('public')->delete($villageAsset->icon_path);
        }

        $villageAsset->delete();

        return redirect()->route('admin.village-assets.index')->with('status', 'Data aset desa berhasil dihapus.');
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

        $details = $this->reverseGeocodeDetails(
            (float) $resolved['latitude'],
            (float) $resolved['longitude']
        );

        return response()->json([
            'ok' => true,
            'latitude' => $resolved['latitude'],
            'longitude' => $resolved['longitude'],
            'final_url' => $resolved['final_url'],
            'name' => $details['name'] ?? null,
            'address' => $details['address'] ?? null,
            'contact_phone' => $details['contact_phone'] ?? null,
            'contact_person' => $details['contact_person'] ?? null,
            'source' => $details['source'] ?? 'google+nominatim',
        ]);
    }

    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:40'],
            'subcategory' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'map_url' => ['nullable', 'url', 'max:2000'],
            'icon' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'remove_icon' => ['nullable', 'boolean'],
            'contact_person' => ['nullable', 'string', 'max:120'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }

    private function fillCoordinatesFromMapLink(array &$validated, GoogleMapsLinkResolver $resolver): void
    {
        $mapUrl = trim((string) ($validated['map_url'] ?? ''));
        if ($mapUrl === '') {
            return;
        }

        $resolved = $resolver->resolve($mapUrl);
        if (!$resolved) {
            return;
        }

        $validated['latitude'] = $resolved['latitude'];
        $validated['longitude'] = $resolved['longitude'];
        $validated['map_url'] = $resolved['final_url'];

        $details = $this->reverseGeocodeDetails((float) $resolved['latitude'], (float) $resolved['longitude']);
        if (($validated['address'] ?? null) === null || trim((string) ($validated['address'] ?? '')) === '') {
            $validated['address'] = $details['address'] ?? null;
        }
        if (($validated['name'] ?? null) === null || trim((string) ($validated['name'] ?? '')) === '') {
            $validated['name'] = $details['name'] ?? ($validated['name'] ?? '');
        }
        if (($validated['contact_phone'] ?? null) === null || trim((string) ($validated['contact_phone'] ?? '')) === '') {
            $validated['contact_phone'] = $details['contact_phone'] ?? null;
        }
        if (($validated['contact_person'] ?? null) === null || trim((string) ($validated['contact_person'] ?? '')) === '') {
            $validated['contact_person'] = $details['contact_person'] ?? null;
        }
    }

    private function reverseGeocodeDetails(float $lat, float $lng): array
    {
        try {
            $response = Http::timeout(12)
                ->retry(1, 200)
                ->withHeaders([
                    'User-Agent' => 'Webdesku/1.0 (infografis-asset-resolver)',
                    'Accept-Language' => 'id-ID,id;q=0.9,en;q=0.8',
                ])
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'format' => 'jsonv2',
                    'lat' => $lat,
                    'lon' => $lng,
                    'addressdetails' => 1,
                    'namedetails' => 1,
                    'extratags' => 1,
                ]);

            if (!$response->ok()) {
                return [];
            }

            $json = $response->json();
            if (!is_array($json)) {
                return [];
            }

            $address = trim((string) ($json['display_name'] ?? ''));
            $namedetails = is_array($json['namedetails'] ?? null) ? $json['namedetails'] : [];
            $extratags = is_array($json['extratags'] ?? null) ? $json['extratags'] : [];
            $addr = is_array($json['address'] ?? null) ? $json['address'] : [];

            $name = trim((string) (
                $namedetails['name']
                ?? $addr['amenity']
                ?? $addr['building']
                ?? $addr['shop']
                ?? $addr['tourism']
                ?? ''
            ));

            $phone = trim((string) (
                $extratags['contact:phone']
                ?? $extratags['phone']
                ?? ''
            ));

            $contactPerson = trim((string) (
                $extratags['operator']
                ?? $extratags['brand']
                ?? ''
            ));

            return [
                'source' => 'nominatim',
                'name' => $name !== '' ? $name : null,
                'address' => $address !== '' ? $address : null,
                'contact_phone' => $phone !== '' ? $phone : null,
                'contact_person' => $contactPerson !== '' ? $contactPerson : null,
            ];
        } catch (\Throwable) {
            return [];
        }
    }
}
