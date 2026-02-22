<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\Village;
use App\Services\GoogleMapsLinkResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AgendaController extends Controller
{
    public function index(): View
    {
        $agendas = Agenda::query()
            ->orderBy('start_at')
            ->paginate(10);

        return view('admin.agendas.index', compact('agendas'));
    }

    public function create(): View
    {
        return view('admin.agendas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_if:is_published,1'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_if:is_published,1'],
            'map_url' => ['nullable', 'url', 'max:2000'],
            'poster' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'is_published' => ['nullable', 'boolean'],
        ], [
            'latitude.required_if' => 'Latitude wajib diisi saat agenda dipublish.',
            'longitude.required_if' => 'Longitude wajib diisi saat agenda dipublish.',
        ]);

        $this->fillCoordinatesFromMapLink($validated, app(GoogleMapsLinkResolver::class));

        $village = Village::query()->first();

        if (!$village) {
            return back()->withErrors(['title' => 'Data desa belum tersedia.'])->withInput();
        }

        $validated['village_id'] = $village->id;
        $validated['is_published'] = (bool) ($validated['is_published'] ?? false);
        $validated['published_at'] = $validated['is_published'] ? now() : null;
        $validated['poster_path'] = $request->hasFile('poster')
            ? $request->file('poster')->store('agendas', 'public')
            : null;

        unset($validated['poster']);

        Agenda::query()->create($validated);

        return redirect()->route('admin.agendas.index')->with('status', 'Agenda berhasil ditambahkan.');
    }

    public function edit(Agenda $agenda): View
    {
        return view('admin.agendas.edit', compact('agenda'));
    }

    public function update(Request $request, Agenda $agenda): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_if:is_published,1'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_if:is_published,1'],
            'map_url' => ['nullable', 'url', 'max:2000'],
            'poster' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_poster' => ['nullable', 'boolean'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'is_published' => ['nullable', 'boolean'],
        ], [
            'latitude.required_if' => 'Latitude wajib diisi saat agenda dipublish.',
            'longitude.required_if' => 'Longitude wajib diisi saat agenda dipublish.',
        ]);

        $this->fillCoordinatesFromMapLink($validated, app(GoogleMapsLinkResolver::class));

        if ((bool) $request->boolean('remove_poster')) {
            if ($agenda->hasLocalPoster() && Storage::disk('public')->exists($agenda->poster_path)) {
                Storage::disk('public')->delete($agenda->poster_path);
            }
            $agenda->poster_path = null;
        }

        if ($request->hasFile('poster')) {
            if ($agenda->hasLocalPoster() && Storage::disk('public')->exists($agenda->poster_path)) {
                Storage::disk('public')->delete($agenda->poster_path);
            }
            $agenda->poster_path = $request->file('poster')->store('agendas', 'public');
        }

        unset($validated['poster'], $validated['remove_poster']);
        $agenda->fill($validated);
        $agenda->is_published = (bool) ($validated['is_published'] ?? false);
        $agenda->published_at = $agenda->is_published ? ($agenda->published_at ?? now()) : null;
        $agenda->save();

        return redirect()->route('admin.agendas.index')->with('status', 'Agenda berhasil diperbarui.');
    }

    public function destroy(Agenda $agenda): RedirectResponse
    {
        if ($agenda->hasLocalPoster() && Storage::disk('public')->exists($agenda->poster_path)) {
            Storage::disk('public')->delete($agenda->poster_path);
        }

        $agenda->delete();

        return redirect()->route('admin.agendas.index')->with('status', 'Agenda berhasil dihapus.');
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
    }
}
