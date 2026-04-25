<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Announcement;
use App\Models\Gallery;
use App\Models\News;
use App\Models\Slider;
use App\Models\Village;
use App\Models\VillageHeadMessage;
use App\Models\VillageOfficial;
use App\Models\ServiceRequest;
use App\Models\VillageAsset;
use App\Models\VillageApbdesItem;
use App\Models\VillageInfographicItem;
use App\Models\VillagePopulation;
use App\Models\VillagePopulationStat;
use App\Models\VillageProfilePage;
use App\Models\VillageService;
use App\Models\VillageTransparencyItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class HomeController extends Controller
{
    public function index(): View
    {
        $village = $this->currentVillage();

        $news = Schema::hasTable('news')
            ? $this->publishedNewsQuery($village)->take(3)->get()
            : collect();
        $sliders = Schema::hasTable('sliders')
            ? $this->publishedSlidersQuery($village)->take(8)->get()
            : collect();
        $services = Schema::hasTable('services')
            ? $this->publishedServicesQuery($village)->take(4)->get()
            : collect();
        $headMessage = Schema::hasTable('village_head_messages')
            ? $this->publishedHeadMessagesQuery($village)->first()
            : null;
        $officials = Schema::hasTable('village_officials')
            ? $this->publishedOfficialsQuery($village)->take(4)->get()
            : collect();
        $agendas = Schema::hasTable('agendas')
            ? $this->publishedAgendasQuery($village)->take(4)->get()
            : collect();
        $announcements = Schema::hasTable('announcements')
            ? $this->publishedAnnouncementsQuery($village)->take(3)->get()
            : collect();
        $galleries = Schema::hasTable('galleries')
            ? $this->publishedGalleriesQuery($village)->take(4)->get()
            : collect();

        $population = $this->normalizedVillagePopulation($village);
        $populationTotal = $population['total'];
        $populationMale = $population['male'];
        $populationFemale = $population['female'];

        $hasGenderStats = $populationMale > 0 || $populationFemale > 0;

        $stats = [
            ['label' => 'Total Penduduk', 'value' => $populationTotal > 0 ? number_format($populationTotal, 0, ',', '.').' Jiwa' : '-'],
            ['label' => 'Penduduk Laki-laki', 'value' => $populationMale > 0 ? number_format($populationMale, 0, ',', '.').' Jiwa' : '-'],
            ['label' => 'Penduduk Perempuan', 'value' => $populationFemale > 0 ? number_format($populationFemale, 0, ',', '.').' Jiwa' : '-'],
            ['label' => 'Kepala Keluarga', 'value' => $village?->households ? number_format($village->households, 0, ',', '.').' KK' : '-'],
            ['label' => 'Luas Wilayah', 'value' => $village?->area_km2 ? number_format($village->area_km2, 2, ',', '.').' Km2' : '-'],
            ['label' => 'RT / RW', 'value' => $village ? (($village->rt_count ?? 0).' / '.($village->rw_count ?? 0)) : '-'],
        ];

        $populationChart = [
            'male' => $populationMale,
            'female' => $populationFemale,
            'has_data' => $hasGenderStats,
        ];

        return view('web.home', compact(
            'village',
            'news',
            'sliders',
            'services',
            'headMessage',
            'officials',
            'agendas',
            'announcements',
            'galleries',
            'stats',
            'populationChart',
        ));
    }

    public function profil(): View
    {
        return $this->profilGambaran();
    }

    public function profilGambaran(): View
    {
        $data = $this->profileContext();
        $data['title'] = 'Gambaran Umum Desa';

        return view('web.profile.gambaran', $data);
    }

    public function profilSejarah(): View
    {
        $data = $this->profileContext();
        $data['title'] = 'Sejarah Desa';

        return view('web.profile.sejarah', $data);
    }

    public function profilVisiMisi(): View
    {
        $data = $this->profileContext();
        $data['title'] = 'Visi dan Misi';

        return view('web.profile.visimisi', $data);
    }

    public function profilOrganisasi(): View
    {
        $data = $this->profileContext();
        $data['title'] = 'Susunan Organisasi';

        return view('web.profile.organisasi', $data);
    }

    public function news(): View
    {
        $village = $this->currentVillage();
        $news = Schema::hasTable('news')
            ? $this->publishedNewsQuery($village)->paginate(9)
            : $this->emptyPaginator();

        return view('web.news.index', compact('news', 'village'));
    }

    public function newsShow(string $slug): View
    {
        $village = $this->currentVillage();

        if (!Schema::hasTable('news')) {
            throw new ModelNotFoundException();
        }

        $news = $this->publishedNewsQuery($village)
            ->where('slug', $slug)
            ->firstOrFail();

        if (Schema::hasColumn('news', 'view_count')) {
            $news->increment('view_count');
            $news->refresh();
        }

        $relatedNews = $this->publishedNewsQuery($village)
            ->whereKeyNot($news->id)
            ->take(3)
            ->get();

        return view('web.news.show', compact('news', 'relatedNews', 'village'));
    }

    public function agenda(): View
    {
        $village = $this->currentVillage();
        $status = (string) request()->query('status', 'all');
        $keyword = trim((string) request()->query('q', ''));
        $monthInput = trim((string) request()->query('month', ''));
        $dayInput = trim((string) request()->query('day', ''));

        $activeMonth = $this->resolveMonth($monthInput);
        $activeDay = $this->resolveDay($dayInput);

        if (!Schema::hasTable('agendas')) {
            return view('web.agenda.index', [
                'village' => $village,
                'agendas' => $this->emptyPaginator(),
                'mapAgendas' => collect(),
                'mapAgendaItems' => collect(),
                'status' => $status,
                'keyword' => $keyword,
                'activeMonth' => $activeMonth,
                'activeDay' => $activeDay,
                'calendarWeeks' => [],
                'monthPrev' => $activeMonth->copy()->subMonth()->format('Y-m'),
                'monthNext' => $activeMonth->copy()->addMonth()->format('Y-m'),
            ]);
        }

        $now = now();
        $query = $this->publishedAgendasQuery($village)
            ->when($keyword !== '', function (Builder $query) use ($keyword) {
                $query->where(function (Builder $subQuery) use ($keyword) {
                    $subQuery->where('title', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%")
                        ->orWhere('location', 'like', "%{$keyword}%");
                });
            })
            ->when($activeDay, fn (Builder $query) => $query->whereDate('start_at', $activeDay->toDateString()));

        if ($status === 'upcoming') {
            $query->whereNotNull('start_at')->where('start_at', '>', $now);
        } elseif ($status === 'ongoing') {
            $query->whereNotNull('start_at')
                ->where('start_at', '<=', $now)
                ->where(function (Builder $subQuery) use ($now) {
                    $subQuery->whereNull('end_at')->orWhere('end_at', '>=', $now);
                });
        } elseif ($status === 'done') {
            $query->where(function (Builder $subQuery) use ($now) {
                $subQuery->whereNotNull('end_at')->where('end_at', '<', $now)
                    ->orWhere(function (Builder $subSubQuery) use ($now) {
                        $subSubQuery->whereNull('end_at')->whereNotNull('start_at')->where('start_at', '<', $now);
                    });
            });
        }

        $mapAgendas = (clone $query)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->take(200)
            ->get();
        $mapAgendaItems = $mapAgendas->map(function (Agenda $agenda) {
            return [
                'id' => $agenda->id,
                'title' => $agenda->title,
                'location' => $agenda->location,
                'start_at' => $agenda->start_at?->translatedFormat('d M Y H:i'),
                'latitude' => $agenda->latitude,
                'longitude' => $agenda->longitude,
                'poster_url' => $agenda->poster_url,
            ];
        })->values();

        $agendas = $query->paginate(9)->withQueryString();
        $calendarWeeks = $this->buildAgendaCalendar($village, $activeMonth);
        $monthPrev = $activeMonth->copy()->subMonth()->format('Y-m');
        $monthNext = $activeMonth->copy()->addMonth()->format('Y-m');

        return view('web.agenda.index', compact(
            'village',
            'agendas',
            'mapAgendas',
            'mapAgendaItems',
            'status',
            'keyword',
            'activeMonth',
            'activeDay',
            'calendarWeeks',
            'monthPrev',
            'monthNext',
        ));
    }

    public function agendaShow(Agenda $agenda): View
    {
        $village = $this->currentVillage();

        if (Schema::hasColumn('agendas', 'is_published') && !$agenda->is_published) {
            throw new ModelNotFoundException();
        }

        if ($village && $agenda->village_id !== $village->id) {
            throw new ModelNotFoundException();
        }

        $relatedAgendas = $this->publishedAgendasQuery($village)
            ->whereKeyNot($agenda->id)
            ->take(3)
            ->get();

        return view('web.agenda.show', compact('agenda', 'relatedAgendas', 'village'));
    }

    public function services(): View
    {
        $village = $this->currentVillage();
        $services = Schema::hasTable('services')
            ? $this->publishedServicesQuery($village)->paginate(12)
            : $this->emptyPaginator();

        return view('web.services.index', compact('services', 'village'));
    }

    public function serviceShow(string $slug): View
    {
        $village = $this->currentVillage();

        if (!Schema::hasTable('services')) {
            throw new ModelNotFoundException();
        }

        $service = $this->publishedServicesQuery($village)
            ->where('slug', $slug)
            ->firstOrFail();

        return view('web.services.show', compact('service', 'village'));
    }

    public function serviceApply(Request $request, string $slug): RedirectResponse
    {
        $village = $this->currentVillage();

        if (!Schema::hasTable('services') || !Schema::hasTable('service_requests')) {
            return back()->withErrors(['service' => 'Modul layanan belum aktif.']);
        }

        $service = $this->publishedServicesQuery($village)
            ->where('slug', $slug)
            ->firstOrFail();

        $validated = $request->validate([
            'applicant_name' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'digits:16'],
            'kk_number' => ['nullable', 'digits:16'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string'],
            'description' => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'nik.digits' => 'NIK harus 16 digit.',
            'kk_number.digits' => 'Nomor KK harus 16 digit.',
            'attachment.mimes' => 'Lampiran harus berupa PDF/JPG/PNG/WEBP.',
        ]);

        $ticketCode = $this->generateServiceTicketCode();
        $publicToken = $this->generateServicePublicToken();
        $attachmentPath = $request->hasFile('attachment')
            ? $request->file('attachment')->store('service-requests', 'public')
            : null;

        ServiceRequest::query()->create([
            'village_id' => $village?->id ?? $service->village_id,
            'service_id' => $service->id,
            'ticket_code' => $ticketCode,
            'public_token' => $publicToken,
            'applicant_name' => $validated['applicant_name'],
            'nik' => $validated['nik'],
            'kk_number' => $validated['kk_number'] ?? null,
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'],
            'description' => $validated['description'] ?? null,
            'attachment_path' => $attachmentPath,
            'status' => 'diajukan',
            'submitted_at' => now(),
        ]);

        return redirect()
            ->route('services.show', $service->slug)
            ->with('status', "Pengajuan layanan berhasil dikirim. Nomor tiket Anda: {$ticketCode}")
            ->with('ticket_code', $ticketCode)
            ->with('receipt_url', route('services.receipt', $publicToken));
    }

    public function serviceReceipt(string $token)
    {
        if (!Schema::hasTable('service_requests')) {
            throw new ModelNotFoundException();
        }

        $request = ServiceRequest::query()
            ->with('service', 'village')
            ->where('public_token', $token)
            ->firstOrFail();

        $qrPayload = implode("\n", [
            'BUKTI PENGAJUAN LAYANAN DESA',
            'Nomor Tiket: '.$request->ticket_code,
            'Layanan: '.($request->service?->name ?? '-'),
            'Nama Pemohon: '.$request->applicant_name,
            'Tanggal Pengajuan: '.($request->submitted_at?->format('d-m-Y H:i') ?? '-'),
        ]);

        $qrSvg = QrCode::format('svg')->size(180)->margin(0)->generate($qrPayload);

        return Pdf::loadView('web.services.receipt', [
            'serviceRequest' => $request,
            'qrSvg' => $qrSvg,
        ])->setPaper('a4')->stream('bukti-pengajuan-'.$request->ticket_code.'.pdf');
    }

    public function serviceStatus(Request $request): View
    {
        $ticket = Str::upper(trim((string) $request->query('ticket', '')));
        $serviceRequest = null;

        if ($ticket !== '' && Schema::hasTable('service_requests')) {
            $serviceRequest = ServiceRequest::query()
                ->with('service', 'village')
                ->where('ticket_code', $ticket)
                ->first();
        }

        return view('web.services.status', [
            'ticket' => $ticket,
            'serviceRequest' => $serviceRequest,
        ]);
    }

    public function transparansi(): View
    {
        $village = $this->currentVillage();
        $year = (int) request()->query('year', 0);

        $apbdesYears = Schema::hasTable('village_apbdes_items')
            ? $this->publishedApbdesQuery($village)->select('fiscal_year')->distinct()->orderByDesc('fiscal_year')->pluck('fiscal_year')
            : collect();
        $selectedYear = $year > 0 ? $year : (int) ($apbdesYears->first() ?? 0);
        $apbdesItems = ($selectedYear > 0 && Schema::hasTable('village_apbdes_items'))
            ? $this->publishedApbdesQuery($village)
                ->where('fiscal_year', $selectedYear)
                ->orderBy('type')
                ->orderBy('sort_order')
                ->get()
            : collect();
        $apbdesSummary = [
            'pendapatan' => (int) $apbdesItems->where('type', 'pendapatan')->sum('amount'),
            'belanja' => (int) $apbdesItems->where('type', 'belanja')->sum('amount'),
            'pembiayaan' => (int) $apbdesItems->where('type', 'pembiayaan')->sum('amount'),
        ];

        $transparencyItems = Schema::hasTable('village_transparency_items')
            ? $this->publishedTransparencyQuery($village)
                ->when($selectedYear > 0, fn (Builder $query) => $query->where(function (Builder $subQuery) use ($selectedYear) {
                    $subQuery->where('fiscal_year', $selectedYear)->orWhereNull('fiscal_year');
                }))
                ->orderByDesc('fiscal_year')
                ->orderBy('category')
                ->orderBy('sort_order')
                ->get()
            : collect();
        $transparencyCategories = $transparencyItems->groupBy('category');

        return view('web.transparency.index', [
            'village' => $village,
            'apbdesYears' => $apbdesYears,
            'selectedYear' => $selectedYear > 0 ? $selectedYear : null,
            'apbdesItems' => $apbdesItems,
            'apbdesSummary' => $apbdesSummary,
            'transparencyItems' => $transparencyItems,
            'transparencyCategories' => $transparencyCategories,
        ]);
    }

    public function infografis(): View
    {
        $village = $this->currentVillage();
        $tab = (string) request()->query('tab', 'aset');
        $type = (string) request()->query('type', 'all');
        $keyword = trim((string) request()->query('q', ''));
        $year = (int) request()->query('year', 0);
        $infographicGovernanceMeta = $this->buildInfographicGovernanceMeta($village);

        if (!Schema::hasTable('village_assets')) {
            return view('web.infographics.index', [
                'village' => $village,
                'assets' => $this->emptyPaginator(),
                'assetMapItems' => collect(),
                'populations' => collect(),
                'populationChartItems' => collect(),
                'populationStatsByCategory' => collect(),
                'apbdesItems' => collect(),
                'apbdesSummary' => ['pendapatan' => 0, 'belanja' => 0, 'pembiayaan' => 0],
                'apbdesYears' => collect(),
                'selectedYear' => null,
                'otherInfographics' => collect(),
                'tab' => $tab,
                'type' => $type,
                'keyword' => $keyword,
                'year' => $year > 0 ? $year : null,
                'typeOptions' => VillageAsset::typeOptions(),
                'infographicGovernanceMeta' => $infographicGovernanceMeta,
            ]);
        }

        $query = $this->publishedAssetsQuery($village)
            ->when($type !== 'all', fn (Builder $builder) => $builder->where('type', $type))
            ->when($keyword !== '', function (Builder $builder) use ($keyword) {
                $builder->where(function (Builder $subQuery) use ($keyword) {
                    $subQuery->where('name', 'like', "%{$keyword}%")
                        ->orWhere('subcategory', 'like', "%{$keyword}%")
                        ->orWhere('address', 'like', "%{$keyword}%");
                });
            });

        $mapItems = (clone $query)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->take(400)
            ->get()
            ->map(function (VillageAsset $asset) {
                return [
                    'id' => $asset->id,
                    'name' => $asset->name,
                    'type' => $asset->type,
                    'type_label' => $asset->typeLabel(),
                    'color' => $asset->typeColor(),
                    'subcategory' => $asset->subcategory,
                    'description' => Str::limit(strip_tags((string) $asset->description), 180),
                    'address' => $asset->address,
                    'latitude' => $asset->latitude,
                    'longitude' => $asset->longitude,
                    'map_url' => $asset->map_url,
                    'icon_url' => $asset->icon_url,
                    'contact_person' => $asset->contact_person,
                    'contact_phone' => $asset->contact_phone,
                ];
            })
            ->values();

        $assets = $query->paginate(6)->withQueryString();

        $populations = Schema::hasTable('village_populations')
            ? $this->publishedPopulationQuery($village)->orderByDesc('year')->get()
            : collect();
        $populationChartItems = $populations->map(fn (VillagePopulation $item) => [
            'year' => (string) $item->year,
            'male' => (int) $item->male,
            'female' => (int) $item->female,
        ])->values();
        $populationStatsByCategory = Schema::hasTable('village_population_stats')
            ? $this->publishedPopulationStatsQuery($village)
                ->when(
                    $populations->isNotEmpty(),
                    fn (Builder $query) => $query->where('year', (int) ($populations->first()->year ?? now()->year))
                )
                ->orderBy('category')
                ->orderBy('sort_order')
                ->orderBy('label')
                ->get()
                ->groupBy('category')
            : collect();

        $apbdesYears = Schema::hasTable('village_apbdes_items')
            ? $this->publishedApbdesQuery($village)->select('fiscal_year')->distinct()->orderByDesc('fiscal_year')->pluck('fiscal_year')
            : collect();
        $selectedYear = $year > 0 ? $year : (int) ($apbdesYears->first() ?? 0);
        $apbdesItems = ($selectedYear > 0 && Schema::hasTable('village_apbdes_items'))
            ? $this->publishedApbdesQuery($village)->where('fiscal_year', $selectedYear)->orderBy('type')->orderBy('sort_order')->get()
            : collect();
        $apbdesSummary = [
            'pendapatan' => (int) $apbdesItems->where('type', 'pendapatan')->sum('amount'),
            'belanja' => (int) $apbdesItems->where('type', 'belanja')->sum('amount'),
            'pembiayaan' => (int) $apbdesItems->where('type', 'pembiayaan')->sum('amount'),
        ];

        $otherInfographics = Schema::hasTable('village_infographic_items')
            ? $this->publishedOtherInfographicQuery($village)->orderBy('sort_order')->get()
            : collect();

        return view('web.infographics.index', [
            'village' => $village,
            'assets' => $assets,
            'assetMapItems' => $mapItems,
            'populations' => $populations,
            'populationChartItems' => $populationChartItems,
            'populationStatsByCategory' => $populationStatsByCategory,
            'apbdesItems' => $apbdesItems,
            'apbdesSummary' => $apbdesSummary,
            'apbdesYears' => $apbdesYears,
            'selectedYear' => $selectedYear > 0 ? $selectedYear : null,
            'otherInfographics' => $otherInfographics,
            'tab' => $tab,
            'type' => $type,
            'keyword' => $keyword,
            'year' => $year > 0 ? $year : null,
            'typeOptions' => VillageAsset::typeOptions(),
            'infographicGovernanceMeta' => $infographicGovernanceMeta,
        ]);
    }

    public function galeri(): View
    {
        $village = $this->currentVillage();
        $galleries = Schema::hasTable('galleries')
            ? $this->publishedGalleriesQuery($village)->paginate(12)
            : $this->emptyPaginator();

        return view('web.gallery.index', compact('galleries', 'village'));
    }

    public function pengumuman(): View
    {
        $village = $this->currentVillage();
        $keyword = trim((string) request()->query('q', ''));
        $type = (string) request()->query('type', 'all');

        $announcements = Schema::hasTable('announcements')
            ? $this->publishedAnnouncementsQuery($village)
                ->when($type !== 'all', fn (Builder $query) => $query->where('type', $type))
                ->when($keyword !== '', function (Builder $query) use ($keyword) {
                    $query->where(function (Builder $subQuery) use ($keyword) {
                        $subQuery->where('title', 'like', "%{$keyword}%")
                            ->orWhere('content', 'like', "%{$keyword}%");
                    });
                })
                ->paginate(12)
                ->withQueryString()
            : $this->emptyPaginator();

        return view('web.announcements.index', [
            'announcements' => $announcements,
            'village' => $village,
            'keyword' => $keyword,
            'selectedType' => $type,
            'typeOptions' => \App\Models\Announcement::typeOptions(),
        ]);
    }

    public function kontak(): View
    {
        $village = $this->currentVillage();

        return view('web.page', [
            'title' => 'Kontak',
            'description' => $village?->address
                ? "Alamat: {$village->address}. Telepon: ".($village->phone ?? '-').". Email: ".($village->email ?? '-').'.'
                : 'Informasi kontak kantor desa.',
        ]);
    }

    private function currentVillage(): ?Village
    {
        if (!Schema::hasTable('villages')) {
            return null;
        }

        return app()->bound('currentVillage')
            ? app('currentVillage')
            : Village::query()->first();
    }

    private function profileContext(): array
    {
        $village = $this->currentVillage();
        $population = $this->normalizedVillagePopulation($village);

        $stats = [
            ['label' => 'Penduduk', 'value' => $population['total'] > 0 ? number_format($population['total'], 0, ',', '.').' Jiwa' : '-'],
            ['label' => 'Kepala Keluarga', 'value' => $village?->households ? number_format($village->households, 0, ',', '.').' KK' : '-'],
            ['label' => 'Luas Wilayah', 'value' => $village?->area_km2 ? number_format($village->area_km2, 2, ',', '.').' Km2' : '-'],
            ['label' => 'RT / RW', 'value' => $village ? (($village->rt_count ?? 0).' / '.($village->rw_count ?? 0)) : '-'],
        ];

        $missions = $village?->mission
            ? preg_split('/\r\n|\r|\n/', (string) $village->mission) ?: []
            : [];

        $officials = Schema::hasTable('village_officials')
            ? $this->publishedOfficialsQuery($village)->take(24)->get()
            : collect();

        $profilePages = collect();
        if ($village && Schema::hasTable('village_profile_pages')) {
            $profilePages = VillageProfilePage::query()
                ->where('village_id', $village->id)
                ->where('is_published', true)
                ->get()
                ->keyBy('slug');
        }

        return [
            'village' => $village,
            'stats' => $stats,
            'missions' => array_values(array_filter(array_map('trim', $missions))),
            'officials' => $officials,
            'gambaran' => $this->gambaranProfileData($village, $profilePages->get(VillageProfilePage::SLUG_GAMBARAN)),
            'sejarahData' => $this->sejarahProfileData($village, $profilePages->get(VillageProfilePage::SLUG_SEJARAH)),
            'visiMisiData' => $this->visiMisiProfileData($village, $profilePages->get(VillageProfilePage::SLUG_VISIMISI)),
            'organisasiData' => $this->organisasiProfileData($village, $officials, $profilePages->get(VillageProfilePage::SLUG_ORGANISASI)),
            'profilePages' => $profilePages,
        ];
    }

    private function gambaranProfileData(?Village $village, ?VillageProfilePage $page = null): array
    {
        $population = $this->normalizedVillagePopulation($village);
        $populationSeries = collect();
        $religionStats = collect();
        $populationStatsByCategory = collect();
        if ($village && Schema::hasTable('village_populations')) {
            $populationSeries = $this->publishedPopulationQuery($village)
                ->orderBy('year')
                ->get()
                ->map(fn (VillagePopulation $item) => [
                    'year' => (int) $item->year,
                    'male' => (int) $item->male,
                    'female' => (int) $item->female,
                    'total' => (int) $item->total(),
                    'kk' => (int) $item->households,
                ])
                ->values();
        }
        if ($village && Schema::hasTable('village_population_stats')) {
            $statsRows = $this->publishedPopulationStatsQuery($village)
                ->orderByDesc('year')
                ->orderBy('category')
                ->orderBy('sort_order')
                ->orderBy('label')
                ->get();

            $activeYear = (int) ($statsRows->first()->year ?? 0);
            $yearRows = $statsRows
                ->when($activeYear > 0, fn ($rows) => $rows->where('year', $activeYear))
                ->map(fn (VillagePopulationStat $row) => [
                    'category' => (string) $row->category,
                    'label' => (string) $row->label,
                    'value' => (int) $row->value,
                ])
                ->values();

            $populationStatsByCategory = $yearRows
                ->groupBy('category')
                ->map(fn ($rows) => $rows->map(fn ($row) => [
                    'label' => $row['label'],
                    'value' => $row['value'],
                ])->values()->all());

            $religionStats = collect($populationStatsByCategory->get(VillagePopulationStat::CATEGORY_AGAMA, []));
            if ($religionStats->isEmpty()) {
                $religionStats = collect($populationStatsByCategory->get('agaman', []));
            }
        }

        $data = [
            'deskripsi' => (string) ($village?->description ?? ''),
            'koordinat' => [
                'latitude' => $village?->latitude,
                'longitude' => $village?->longitude,
            ],
            'batas' => [
                'utara' => null,
                'selatan' => null,
                'barat' => null,
                'timur' => null,
            ],
            'luas' => [],
            'orbitasi' => [],
            'penduduk' => [
                'kk' => (int) ($village?->households ?? 0),
                'male' => $population['male'],
                'female' => $population['female'],
                'total' => $population['total'],
            ],
            'pendidikan' => [],
            'agama' => [],
            'kesehatan' => [],
            'pekerjaan' => [],
            'penduduk_per_tahun' => $populationSeries->all(),
            'agama' => $religionStats->all(),
            'statistik_kategori_penduduk' => $populationStatsByCategory->toArray(),
        ];

        $merged = $this->mergeProfilePageData($data, $page);
        $merged['penduduk']['kk'] = (int) ($village?->households ?? 0);
        $merged['penduduk']['male'] = $population['male'];
        $merged['penduduk']['female'] = $population['female'];
        $merged['penduduk']['total'] = $population['total'];
        if ($religionStats->isNotEmpty()) {
            $merged['agama'] = $religionStats->all();
        }
        if ($populationStatsByCategory->isNotEmpty()) {
            $merged['statistik_kategori_penduduk'] = $populationStatsByCategory->toArray();
        }

        return $merged;
    }

    private function sejarahProfileData(?Village $village, ?VillageProfilePage $page = null): array
    {
        $data = [
            'ringkasan' => (string) ($village?->history ?? ''),
            'pemekaran' => [],
            'cakupan_awal' => null,
            'batas' => [],
            'perbekel' => [],
            'banjar_dinas' => [],
        ];

        return $this->mergeProfilePageData($data, $page);
    }

    private function visiMisiProfileData(?Village $village, ?VillageProfilePage $page = null): array
    {
        $missions = $village?->mission
            ? array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $village->mission) ?: [])))
            : [];

        $data = [
            'visi' => (string) ($village?->vision ?? ''),
            'misi_pokok' => $missions,
        ];

        return $this->mergeProfilePageData($data, $page);
    }

    private function organisasiProfileData(?Village $village, $officials, ?VillageProfilePage $page = null): array
    {
        $grouped = collect($officials)->groupBy(fn ($official) => $official->unit ?: 'Pemerintahan Desa');

        $data = [
            'judul' => 'Susunan Organisasi Pemerintah Desa '.($village?->name ?? ''),
            'kelompok' => $grouped,
        ];

        return $this->mergeProfilePageData($data, $page);
    }

    private function mergeProfilePageData(array $base, ?VillageProfilePage $page): array
    {
        if (!$page) {
            return $base;
        }

        $payload = is_array($page->payload) ? $page->payload : [];
        $data = $payload !== [] ? array_replace_recursive($base, $payload) : $base;

        $data['_page'] = [
            'title' => $page->title,
            'subtitle' => $page->subtitle,
            'content' => $page->content,
            'source_url' => $page->source_url,
            'slug' => $page->slug,
        ];

        if ($page->source_url) {
            $data['sumber'] = $page->source_url;
        }

        if ($page->content) {
            if ($page->slug === VillageProfilePage::SLUG_GAMBARAN) {
                $data['deskripsi'] = $page->content;
            } elseif ($page->slug === VillageProfilePage::SLUG_SEJARAH) {
                $data['ringkasan'] = $page->content;
            } elseif ($page->slug === VillageProfilePage::SLUG_VISIMISI) {
                $data['visi'] = $page->content;
            } elseif ($page->slug === VillageProfilePage::SLUG_ORGANISASI) {
                $data['pengantar'] = $page->content;
            }
        }

        return $data;
    }

    private function normalizedVillagePopulation(?Village $village): array
    {
        $total = (int) ($village?->population ?? 0);
        $male = (int) ($village?->population_male ?? 0);
        $female = (int) ($village?->population_female ?? 0);

        if ($total > 0) {
            if ($male > 0 && $female === 0) {
                $female = max($total - $male, 0);
            } elseif ($female > 0 && $male === 0) {
                $male = max($total - $female, 0);
            } elseif ($male === 0 && $female === 0) {
                $male = (int) floor($total / 2);
                $female = max($total - $male, 0);
            }
        } else {
            $total = max($male + $female, 0);
        }

        return [
            'total' => $total,
            'male' => $male,
            'female' => $female,
        ];
    }

    private function publishedNewsQuery(?Village $village): Builder
    {
        return News::query()
            ->when(Schema::hasColumn('news', 'created_by'), fn (Builder $query) => $query->with('author'))
            ->when(Schema::hasTable('news_images'), fn (Builder $query) => $query->with('images'))
            ->when(Schema::hasColumn('news', 'is_published'), fn (Builder $query) => $query->where('is_published', true))
            ->when($village, fn (Builder $query) => $query->where('village_id', $village->id))
            ->latest('published_at')
            ->latest();
    }

    private function publishedServicesQuery(?Village $village): Builder
    {
        return VillageService::query()
            ->when(Schema::hasColumn('services', 'is_published'), fn (Builder $query) => $query->where('is_published', true))
            ->when($village, fn (Builder $query) => $query->where('village_id', $village->id))
            ->orderByDesc('is_featured')
            ->latest();
    }

    private function publishedAgendasQuery(?Village $village): Builder
    {
        return Agenda::query()
            ->when(Schema::hasColumn('agendas', 'is_published'), fn (Builder $query) => $query->where('is_published', true))
            ->when($village, fn (Builder $query) => $query->where('village_id', $village->id))
            ->orderByRaw('CASE WHEN start_at IS NULL THEN 2 WHEN start_at >= ? THEN 0 ELSE 1 END', [now()])
            ->orderBy('start_at');
    }

    private function publishedOfficialsQuery(?Village $village): Builder
    {
        return VillageOfficial::query()
            ->when(Schema::hasColumn('village_officials', 'is_published'), fn (Builder $query) => $query->where('is_published', true))
            ->when($village, fn (Builder $query) => $query->where('village_id', $village->id))
            ->orderByDesc('is_highlighted')
            ->orderBy('sort_order')
            ->latest();
    }

    private function publishedHeadMessagesQuery(?Village $village): Builder
    {
        return VillageHeadMessage::query()
            ->when(Schema::hasColumn('village_head_messages', 'is_published'), fn (Builder $query) => $query->where('is_published', true))
            ->when($village, fn (Builder $query) => $query->where('village_id', $village->id))
            ->latest();
    }

    private function publishedAnnouncementsQuery(?Village $village): Builder
    {
        return Announcement::query()
            ->when(Schema::hasTable('announcement_images'), fn (Builder $query) => $query->with('images'))
            ->when(Schema::hasColumn('announcements', 'is_published'), fn (Builder $query) => $query->where('is_published', true))
            ->when($village, fn (Builder $query) => $query->where('village_id', $village->id))
            ->latest();
    }

    private function publishedGalleriesQuery(?Village $village): Builder
    {
        return Gallery::query()
            ->when(Schema::hasColumn('galleries', 'is_published'), fn (Builder $query) => $query->where('is_published', true))
            ->when($village, fn (Builder $query) => $query->where('village_id', $village->id))
            ->latest();
    }

    private function publishedSlidersQuery(?Village $village): Builder
    {
        return Slider::query()
            ->when(Schema::hasColumn('sliders', 'is_published'), fn (Builder $query) => $query->where('is_published', true))
            ->when(Schema::hasColumn('sliders', 'is_active'), fn (Builder $query) => $query->where('is_active', true))
            ->when($village, fn (Builder $query) => $query->where('village_id', $village->id))
            ->orderBy('sort_order')
            ->latest();
    }

    private function publishedAssetsQuery(?Village $village): Builder
    {
        return VillageAsset::query()
            ->when(Schema::hasColumn('village_assets', 'is_published'), fn (Builder $query) => $query->where('is_published', true))
            ->when($village, fn (Builder $query) => $query->where('village_id', $village->id))
            ->orderBy('sort_order')
            ->latest();
    }

    private function publishedPopulationQuery(?Village $village): Builder
    {
        return VillagePopulation::query()
            ->when(Schema::hasColumn('village_populations', 'is_published'), fn (Builder $query) => $query->where('is_published', true))
            ->when($village, fn (Builder $query) => $query->where('village_id', $village->id));
    }

    private function publishedPopulationStatsQuery(?Village $village): Builder
    {
        return VillagePopulationStat::query()
            ->when(Schema::hasColumn('village_population_stats', 'is_published'), fn (Builder $query) => $query->where('is_published', true))
            ->when($village, fn (Builder $query) => $query->where('village_id', $village->id));
    }

    private function publishedApbdesQuery(?Village $village): Builder
    {
        return VillageApbdesItem::query()
            ->when(Schema::hasColumn('village_apbdes_items', 'is_published'), fn (Builder $query) => $query->where('is_published', true))
            ->when($village, fn (Builder $query) => $query->where('village_id', $village->id));
    }

    private function publishedOtherInfographicQuery(?Village $village): Builder
    {
        return VillageInfographicItem::query()
            ->when(Schema::hasColumn('village_infographic_items', 'is_published'), fn (Builder $query) => $query->where('is_published', true))
            ->when($village, fn (Builder $query) => $query->where('village_id', $village->id));
    }

    private function publishedTransparencyQuery(?Village $village): Builder
    {
        return VillageTransparencyItem::query()
            ->when(Schema::hasColumn('village_transparency_items', 'is_published'), fn (Builder $query) => $query->where('is_published', true))
            ->when($village, fn (Builder $query) => $query->where('village_id', $village->id));
    }

    private function emptyPaginator(): LengthAwarePaginator
    {
        return new LengthAwarePaginator([], 0, 10);
    }

    private function buildInfographicGovernanceMeta(?Village $village): array
    {
        $villageId = $village?->id;
        $sources = [
            'Data profil desa (master)' => 'Tabel villages',
            'Data aset desa dan fasilitas' => 'Tabel village_assets',
            'Data penduduk tahunan' => 'Tabel village_populations',
            'Data statistik kategori penduduk' => 'Tabel village_population_stats',
            'Data APBDes' => 'Tabel village_apbdes_items',
            'Data infografis lainnya' => 'Tabel village_infographic_items',
        ];

        $latestUpdates = collect([
            $this->latestPublishedTableUpdate('villages', $villageId),
            $this->latestPublishedTableUpdate('village_assets', $villageId, 'is_published'),
            $this->latestPublishedTableUpdate('village_populations', $villageId, 'is_published'),
            $this->latestPublishedTableUpdate('village_population_stats', $villageId, 'is_published'),
            $this->latestPublishedTableUpdate('village_apbdes_items', $villageId, 'is_published'),
            $this->latestPublishedTableUpdate('village_infographic_items', $villageId, 'is_published'),
        ])->filter();

        $lastUpdate = $latestUpdates->isNotEmpty() ? Carbon::parse($latestUpdates->max()) : null;

        return [
            'sources' => $sources,
            'period' => 'Periode mengikuti tahun data pada masing-masing tab.',
            'last_update' => $lastUpdate?->translatedFormat('d F Y, H:i').' WITA',
            'owner' => $village?->name ? "Pemerintah {$village->name}" : 'Pemerintah Desa',
            'note' => 'Mengacu prinsip Satu Data Indonesia (Perpres 39/2019), SPBE (Perpres 95/2018), dan keterbukaan informasi publik (UU 14/2008).',
        ];
    }

    private function latestPublishedTableUpdate(string $table, ?int $villageId, ?string $publishedColumn = null): ?string
    {
        if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'updated_at')) {
            return null;
        }

        $query = \Illuminate\Support\Facades\DB::table($table);

        if ($villageId && Schema::hasColumn($table, 'village_id')) {
            $query->where('village_id', $villageId);
        } elseif ($villageId && $table === 'villages' && Schema::hasColumn($table, 'id')) {
            $query->where('id', $villageId);
        }

        if ($publishedColumn && Schema::hasColumn($table, $publishedColumn)) {
            $query->where($publishedColumn, true);
        }

        return $query->max('updated_at');
    }

    private function generateServiceTicketCode(): string
    {
        do {
            $ticket = 'LYN-'.now()->format('ymd').'-'.Str::upper(Str::random(5));
        } while (ServiceRequest::query()->where('ticket_code', $ticket)->exists());

        return $ticket;
    }

    private function generateServicePublicToken(): string
    {
        do {
            $token = Str::random(40);
        } while (ServiceRequest::query()->where('public_token', $token)->exists());

        return $token;
    }

    private function resolveMonth(string $input): Carbon
    {
        try {
            return $input !== ''
                ? Carbon::createFromFormat('Y-m', $input)->startOfMonth()
                : now()->startOfMonth();
        } catch (\Throwable) {
            return now()->startOfMonth();
        }
    }

    private function resolveDay(string $input): ?Carbon
    {
        try {
            return $input !== '' ? Carbon::createFromFormat('Y-m-d', $input)->startOfDay() : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function buildAgendaCalendar(?Village $village, Carbon $month): array
    {
        $monthStart = $month->copy()->startOfMonth();
        $monthEnd = $month->copy()->endOfMonth();
        $start = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
        $end = $monthEnd->copy()->endOfWeek(Carbon::SUNDAY);

        $agendaByDate = $this->publishedAgendasQuery($village)
            ->whereNotNull('start_at')
            ->whereBetween('start_at', [$monthStart->copy()->startOfDay(), $monthEnd->copy()->endOfDay()])
            ->get()
            ->groupBy(fn (Agenda $agenda) => $agenda->start_at?->toDateString());

        $weeks = [];
        $cursor = $start->copy();

        while ($cursor <= $end) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $dateKey = $cursor->toDateString();
                $items = $agendaByDate->get($dateKey, collect());
                $week[] = [
                    'date' => $cursor->copy(),
                    'in_month' => $cursor->isSameMonth($month),
                    'count' => $items->count(),
                    'first_title' => $items->first()?->title,
                ];
                $cursor->addDay();
            }
            $weeks[] = $week;
        }

        return $weeks;
    }
}
