<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Services\BigBoundaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use RuntimeException;

class VillageMapController extends Controller
{
    public function edit(): View
    {
        $village = Village::query()->first();

        return view('admin.village-map.edit', compact('village'));
    }

    public function update(Request $request): RedirectResponse
    {
        $village = Village::query()->first();

        if (!$village) {
            return back()->withErrors(['latitude' => 'Data desa belum tersedia.']);
        }

        $validated = $request->validate([
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'boundary_geojson' => ['nullable', 'string'],
            'map_url' => ['nullable', 'url'],
        ]);

        [$detectedLat, $detectedLng] = $this->parseCoordinatesFromMapUrl($validated['map_url'] ?? null);

        $boundary = null;
        if (!empty($validated['boundary_geojson'])) {
            $decoded = json_decode($validated['boundary_geojson'], true);

            if (!is_array($decoded)) {
                return back()->withErrors(['boundary_geojson' => 'Format boundary_geojson tidak valid.'])->withInput();
            }

            $boundary = $decoded;
        }

        $village->update([
            'latitude' => $validated['latitude'] ?? $detectedLat,
            'longitude' => $validated['longitude'] ?? $detectedLng,
            'boundary_geojson' => $boundary,
        ]);

        return redirect()->route('admin.village-map.edit')->with('status', 'Data map desa berhasil diperbarui.');
    }

    public function importBig(Request $request, BigBoundaryService $bigBoundaryService): RedirectResponse
    {
        $village = Village::query()->first();

        if (!$village) {
            return back()->withErrors(['desa' => 'Data desa belum tersedia.']);
        }

        $validated = $request->validate([
            'desa' => ['nullable', 'string', 'max:255'],
            'kecamatan' => ['nullable', 'string', 'max:255'],
            'kota' => ['nullable', 'string', 'max:255'],
            'provinsi' => ['nullable', 'string', 'max:255'],
            'map_url' => ['nullable', 'url'],
        ]);

        $desa = $validated['desa'] ?? $this->normalizeVillageName($village->name);

        if (!$desa) {
            return back()->withErrors(['desa' => 'Nama desa wajib diisi.'])->withInput();
        }

        try {
            $result = $bigBoundaryService->importToVillage(
                $village,
                $desa,
                $validated['kecamatan'] ?? null,
                $validated['kota'] ?? null,
                $validated['provinsi'] ?? null
            );

            [$detectedLat, $detectedLng] = $this->parseCoordinatesFromMapUrl($validated['map_url'] ?? null);

            if ($detectedLat !== null && $detectedLng !== null) {
                $village->update([
                    'latitude' => $detectedLat,
                    'longitude' => $detectedLng,
                ]);
            } else {
                $village->update([
                    'latitude' => $result['latitude'],
                    'longitude' => $result['longitude'],
                ]);
            }
        } catch (RuntimeException $e) {
            return back()->withErrors(['desa' => $e->getMessage()])->withInput();
        } catch (\Throwable $e) {
            return back()->withErrors(['desa' => 'Gagal import dari BIG: '.$e->getMessage()])->withInput();
        }

        return redirect()->route('admin.village-map.edit')->with('status', 'Boundary berhasil diimport dari BIG.');
    }

    private function normalizeVillageName(?string $name): ?string
    {
        if (!$name) {
            return null;
        }

        return trim((string) preg_replace('/^(Desa|Kelurahan)\s+/i', '', $name));
    }

    private function parseCoordinatesFromMapUrl(?string $mapUrl): array
    {
        if (!$mapUrl) {
            return [null, null];
        }

        $candidate = trim($mapUrl);
        $expanded = $this->expandUrl($candidate);

        return $this->extractCoordinatesFromText($expanded)
            ?? $this->extractCoordinatesFromText($candidate)
            ?? [null, null];
    }

    private function expandUrl(string $url): string
    {
        try {
            $response = Http::timeout(12)
                ->withOptions(['allow_redirects' => ['max' => 5, 'track_redirects' => true]])
                ->get($url);

            $stats = $response->handlerStats();

            return (string) ($stats['url'] ?? $url);
        } catch (\Throwable) {
            return $url;
        }
    }

    private function extractCoordinatesFromText(string $text): ?array
    {
        if (preg_match('/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/', $text, $m)) {
            return [(float) $m[1], (float) $m[2]];
        }

        if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $text, $m)) {
            return [(float) $m[1], (float) $m[2]];
        }

        if (preg_match('/q=(-?\d+\.\d+),(-?\d+\.\d+)/', $text, $m)) {
            return [(float) $m[1], (float) $m[2]];
        }

        return null;
    }
}
