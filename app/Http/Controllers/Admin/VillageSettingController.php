<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Models\VillagePopulation;
use App\Services\InstagramFeedService;
use App\Services\VillageStatisticSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VillageSettingController extends Controller
{
    public function edit(): View
    {
        $village = Village::query()->first();
        $latestPopulation = null;
        $summaryPopulation = [
            'male' => 0,
            'female' => 0,
            'total' => 0,
            'households' => 0,
        ];
        if ($village) {
            $latestPopulation = VillagePopulation::query()
                ->where('village_id', $village->id)
                ->where('is_published', true)
                ->orderByDesc('year')
                ->orderBy('sort_order')
                ->first();

            $summaryPopulation['male'] = (int) ($latestPopulation?->male ?? $village->population_male ?? 0);
            $summaryPopulation['female'] = (int) ($latestPopulation?->female ?? $village->population_female ?? 0);
            $summaryPopulation['total'] = $summaryPopulation['male'] + $summaryPopulation['female'];
            if ($summaryPopulation['total'] <= 0) {
                $summaryPopulation['total'] = (int) ($village->population ?? 0);
            }
            $summaryPopulation['households'] = (int) ($latestPopulation?->households ?? $village->households ?? 0);
        }

        $summaryAssets = $village
            ? app(VillageStatisticSyncService::class)->latestAssetSummary($village)
            : ['total' => 0, 'by_type' => []];
        $summaryApbdes = $village
            ? app(VillageStatisticSyncService::class)->latestApbdesSummary($village)
            : ['year' => null, 'pendapatan' => 0, 'belanja' => 0, 'pembiayaan' => 0];

        return view('admin.village-settings.edit', [
            'village' => $village,
            'latestPopulation' => $latestPopulation,
            'summaryPopulation' => $summaryPopulation,
            'summaryAssets' => $summaryAssets,
            'summaryApbdes' => $summaryApbdes,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $village = Village::query()->first();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('villages', 'slug')->ignore($village?->id),
            ],
            'description' => ['nullable', 'string'],
            'head_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'district' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'area_km2' => ['nullable', 'numeric', 'min:0'],
            'rt_count' => ['nullable', 'integer', 'min:0'],
            'rw_count' => ['nullable', 'integer', 'min:0'],
            'history' => ['nullable', 'string'],
            'vision' => ['nullable', 'string'],
            'mission' => ['nullable', 'string'],
            'head_greeting' => ['nullable', 'string'],
            'instagram_enabled' => ['nullable', 'boolean'],
            'instagram_username' => ['nullable', 'string', 'max:100'],
            'instagram_user_id' => ['nullable', 'string', 'max:100'],
            'instagram_access_token' => ['nullable', 'string', 'max:4000'],
            'remove_logo' => ['nullable', 'boolean'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        if (!$village) {
            $village = new Village();
        }

        if ((bool) $request->boolean('remove_logo') && $village->logo && Storage::disk('public')->exists($village->logo)) {
            Storage::disk('public')->delete($village->logo);
            $village->logo = null;
        }

        if ($request->hasFile('logo')) {
            if ($village->logo && Storage::disk('public')->exists($village->logo)) {
                Storage::disk('public')->delete($village->logo);
            }

            $village->logo = $request->file('logo')->store('villages/logo', 'public');
        }

        unset($validated['logo'], $validated['remove_logo']);
        $validated['instagram_enabled'] = (bool) $request->boolean('instagram_enabled');
        $validated['instagram_username'] = trim((string) ($validated['instagram_username'] ?? '')) ?: null;
        $validated['instagram_user_id'] = trim((string) ($validated['instagram_user_id'] ?? '')) ?: null;
        $validated['instagram_access_token'] = trim((string) ($validated['instagram_access_token'] ?? '')) ?: null;

        if (!$validated['instagram_enabled']) {
            $validated['instagram_username'] = null;
            $validated['instagram_user_id'] = null;
            $validated['instagram_access_token'] = null;
            $validated['instagram_last_error'] = null;
        }

        $village->fill($validated);
        $village->slug = Str::slug($validated['slug']) ?: Str::slug($validated['name']);
        $village->save();

        return redirect()
            ->route('admin.village-settings.edit')
            ->with('status', 'Pengaturan desa berhasil disimpan ke database.');
    }

    public function syncInstagram(InstagramFeedService $instagramFeedService): RedirectResponse
    {
        $village = Village::query()->first();

        if (!$village) {
            return redirect()->route('admin.village-settings.edit')
                ->withErrors(['instagram' => 'Data desa belum tersedia.']);
        }

        try {
            $count = $instagramFeedService->syncVillage($village, 6);
        } catch (\Throwable $e) {
            $village->update([
                'instagram_last_error' => $e->getMessage(),
                'instagram_last_sync_at' => now(),
            ]);

            return redirect()->route('admin.village-settings.edit')
                ->withErrors(['instagram' => 'Sinkronisasi Instagram gagal: '.$e->getMessage()]);
        }

        return redirect()->route('admin.village-settings.edit')
            ->with('status', "Sinkronisasi Instagram berhasil. {$count} postingan terbaru diperbarui.");
    }
}
