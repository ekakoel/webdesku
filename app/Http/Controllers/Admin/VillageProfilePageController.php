<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Models\VillageProfilePage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VillageProfilePageController extends Controller
{
    public function index(): View
    {
        $village = Village::query()->first();
        $pages = collect(VillageProfilePage::SLUGS)->map(function (string $label, string $slug) use ($village) {
            $page = null;
            if ($village) {
                $page = VillageProfilePage::query()
                    ->where('village_id', $village->id)
                    ->where('slug', $slug)
                    ->first();
            }

            return [
                'slug' => $slug,
                'label' => $label,
                'page' => $page,
            ];
        })->values();

        return view('admin.profile-pages.index', compact('pages', 'village'));
    }

    public function create(Request $request): View
    {
        $slug = (string) $request->query('slug', '');
        if (!array_key_exists($slug, VillageProfilePage::SLUGS)) {
            abort(404);
        }

        return view('admin.profile-pages.create', [
            'slug' => $slug,
            'label' => VillageProfilePage::SLUGS[$slug],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $village = Village::query()->first();
        if (!$village) {
            return back()->withErrors(['title' => 'Data desa belum tersedia.'])->withInput();
        }

        if (!array_key_exists($validated['slug'], VillageProfilePage::SLUGS)) {
            return back()->withErrors(['slug' => 'Slug halaman tidak valid.'])->withInput();
        }

        VillageProfilePage::query()->updateOrCreate(
            [
                'village_id' => $village->id,
                'slug' => $validated['slug'],
            ],
            [
                'title' => $validated['title'],
                'subtitle' => $validated['subtitle'] ?? null,
                'content' => $validated['content'] ?? null,
                'source_url' => $validated['source_url'] ?? null,
                'payload' => $this->parsePayload($validated['payload_json'] ?? null),
                'is_published' => (bool) ($validated['is_published'] ?? false),
                'published_at' => (bool) ($validated['is_published'] ?? false) ? now() : null,
            ]
        );

        return redirect()->route('admin.profile-pages.index')->with('status', 'Konten halaman profil berhasil disimpan.');
    }

    public function edit(VillageProfilePage $profilePage): View
    {
        return view('admin.profile-pages.edit', [
            'profilePage' => $profilePage,
            'slug' => $profilePage->slug,
            'label' => VillageProfilePage::SLUGS[$profilePage->slug] ?? $profilePage->slug,
        ]);
    }

    public function update(Request $request, VillageProfilePage $profilePage): RedirectResponse
    {
        $validated = $request->validate($this->rules($profilePage->slug));

        $profilePage->title = $validated['title'];
        $profilePage->subtitle = $validated['subtitle'] ?? null;
        $profilePage->content = $validated['content'] ?? null;
        $profilePage->source_url = $validated['source_url'] ?? null;
        $profilePage->payload = $this->parsePayload($validated['payload_json'] ?? null);
        $profilePage->is_published = (bool) ($validated['is_published'] ?? false);
        $profilePage->published_at = $profilePage->is_published ? ($profilePage->published_at ?? now()) : null;
        $profilePage->save();

        return redirect()->route('admin.profile-pages.index')->with('status', 'Konten halaman profil berhasil diperbarui.');
    }

    public function destroy(VillageProfilePage $profilePage): RedirectResponse
    {
        $profilePage->delete();

        return redirect()->route('admin.profile-pages.index')->with('status', 'Konten halaman profil berhasil dihapus.');
    }

    private function rules(?string $forceSlug = null): array
    {
        $slugRule = ['required', 'string', 'max:40'];
        if ($forceSlug) {
            $slugRule[] = 'in:'.$forceSlug;
        }

        return [
            'slug' => $slugRule,
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'source_url' => ['nullable', 'url', 'max:2000'],
            'payload_json' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }

    private function parsePayload(?string $payloadJson): ?array
    {
        $payloadJson = trim((string) $payloadJson);
        if ($payloadJson === '') {
            return null;
        }

        $decoded = json_decode($payloadJson, true);
        return is_array($decoded) ? $decoded : null;
    }
}

