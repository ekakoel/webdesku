<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Village;
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

        return view('admin.village-settings.edit', [
            'village' => $village,
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
            'population' => ['nullable', 'integer', 'min:0'],
            'population_male' => ['nullable', 'integer', 'min:0'],
            'population_female' => ['nullable', 'integer', 'min:0'],
            'households' => ['nullable', 'integer', 'min:0'],
            'rt_count' => ['nullable', 'integer', 'min:0'],
            'rw_count' => ['nullable', 'integer', 'min:0'],
            'history' => ['nullable', 'string'],
            'vision' => ['nullable', 'string'],
            'mission' => ['nullable', 'string'],
            'head_greeting' => ['nullable', 'string'],
            'remove_logo' => ['nullable', 'boolean'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        if (!$village) {
            $village = new Village();
        }

        $male = (int) ($validated['population_male'] ?? 0);
        $female = (int) ($validated['population_female'] ?? 0);
        if ($male > 0 || $female > 0) {
            $validated['population'] = $male + $female;
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

        $village->fill($validated);
        $village->slug = Str::slug($validated['slug']) ?: Str::slug($validated['name']);
        $village->save();

        return redirect()
            ->route('admin.village-settings.edit')
            ->with('status', 'Pengaturan desa berhasil disimpan ke database.');
    }
}
