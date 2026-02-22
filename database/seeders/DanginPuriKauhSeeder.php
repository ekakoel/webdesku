<?php

namespace Database\Seeders;

use App\Models\Agenda;
use App\Models\Announcement;
use App\Models\Gallery;
use App\Models\News;
use App\Models\NewsImage;
use App\Models\Village;
use App\Models\VillageAsset;
use App\Models\VillageApbdesItem;
use App\Models\VillageHeadMessage;
use App\Models\VillageInfographicItem;
use App\Models\VillageOfficial;
use App\Models\VillagePopulation;
use App\Models\VillageProfilePage;
use App\Models\VillageService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DanginPuriKauhSeeder extends Seeder
{
    public function run(): void
    {
        $village = Village::query()->updateOrCreate(
            ['slug' => 'danginpuri'],
            [
                'name' => 'Desa Dangin Puri Kauh',
                'description' => 'Desa Dangin Puri Kauh merupakan salah satu desa di Kecamatan Denpasar Utara, Kota Denpasar, Bali. Desa ini berbatasan dengan Desa Dangin Puri Kaja di utara, Kelurahan Dauh Puri Kaja di selatan, Kelurahan Tonja di timur, dan Kelurahan Pemecutan di barat. Terdiri dari 12 banjar/lingkungan dengan total penduduk sekitar 16.170 jiwa.',
                'head_name' => 'Perbekel Desa Dangin Puri Kauh',
                'address' => 'Jl. Gunung Soputan No.49, Denpasar',
                'phone' => '(0361) 480288',
                'email' => 'danginpurikauh@gmail.com',
                'website' => 'https://www.danginpurikauh.denpasarkota.go.id/',
                'postal_code' => '80117',
                'district' => 'Denpasar Utara',
                'city' => 'Kota Denpasar',
                'province' => 'Bali',
                'country' => 'Indonesia',
                'area_km2' => 2.08,
                'population' => 16170,
                'population_male' => 8092,
                'population_female' => 8078,
                'households' => 4738,
                'rt_count' => 39,
                'rw_count' => 0,
                'history' => 'Desa Dangin Puri Kauh sebelumnya merupakan bagian dari wilayah Pemerintahan Desa Dangin Puri (Kelurahan Dangin Puri). Berdasarkan SK Gubernur Kepala Daerah Tingkat I Bali Nomor 57 Tahun 1982, Desa Dangin Puri dimekarkan menjadi lima wilayah: Kelurahan Dangin Puri, Desa Dangin Puri Kangin, Desa Dangin Puri Kelod, Desa Dangin Puri Kauh, dan Desa Dangin Puri Kaja. Pada awal pembentukannya, Desa Dangin Puri Kauh membawahi 5 Banjar Dinas dan 1 RT dengan luas 72,10 hektar.',
                'vision' => 'TERWUJUDNYA PELAYANAN MASYARAKAT YANG TRANSPARAN, AKUNTABILITAS, INOVATIF, DAN MANDIRI BERDASARKAN POTENSI DESA SERTA BERLANDASKAN ADAT DAN BUDAYA.',
                'mission' => "Mewujudkan pemerintahan desa yang baik, bersih, efektif, jujur, dan berwibawa.\nMeningkatkan profesionalitas kerja perangkat desa agar pelayanan cepat, tepat, dan efisien.\nMeningkatkan sarana prasarana desa untuk menunjang kesejahteraan masyarakat.\nMeningkatkan perekonomian masyarakat melalui kelompok usaha warga desa.\nMengembangkan BUM Desa untuk mendorong kesejahteraan ekonomi masyarakat.\nMeningkatkan pelayanan dan fasilitasi kesehatan masyarakat desa secara maksimal.\nMeningkatkan kehidupan desa secara dinamis dalam segi keagamaan dan kebudayaan.\nMenciptakan lingkungan desa yang bersih, sehat, dan hijau.\nMenata lingkungan desa agar nyaman, aman, tertib, dan terkendali dengan melibatkan masyarakat.",
                'head_greeting' => 'Pemerintah desa berkomitmen memberi layanan cepat, transparan, dan inklusif bagi seluruh warga.',
                'quick_info' => [
                    'Pelayanan kantor: Senin-Jumat 08:00-15:00 WITA',
                    'Layanan administrasi desa tersedia online',
                    'Informasi publik diperbarui berkala',
                    'Boundary wilayah dapat diupdate dari layanan BIG melalui menu Kelola Map Desa',
                ],
                'apb_income' => 2450000000,
                'apb_expense' => 2220000000,
                'apb_financing' => 230000000,
            ]
        );

        // Jangan menimpa boundary_geojson hasil import BIG jika sudah ada.
        // Koordinat default hanya dipakai saat data map belum pernah diisi.
        if (!$village->latitude || !$village->longitude) {
            $village->update([
                'latitude' => -8.6512299,
                'longitude' => 115.2148033,
            ]);
        }

        $services = [
            [
                'name' => 'Surat Pengantar',
                'description' => 'Pengajuan surat pengantar untuk kebutuhan administrasi warga.',
                'sla_target_hours' => 24,
                'requirements' => "Fotokopi KTP pemohon\nFotokopi Kartu Keluarga\nSurat keterangan dari banjar/lingkungan",
                'process' => "Pemohon mengisi form pengajuan online\nPetugas desa melakukan verifikasi berkas\nSurat pengantar diterbitkan dan dapat diambil di kantor desa",
                'icon' => 'SP',
                'is_featured' => true,
            ],
            [
                'name' => 'Surat Keterangan Domisili',
                'description' => 'Layanan surat domisili untuk penduduk desa.',
                'sla_target_hours' => 24,
                'requirements' => "Fotokopi KTP\nFotokopi Kartu Keluarga\nData alamat lengkap domisili",
                'process' => "Pemohon mengajukan layanan\nPetugas memverifikasi data domisili\nSurat domisili disahkan dan dikirim notifikasi",
                'icon' => 'SK',
                'is_featured' => true,
            ],
            [
                'name' => 'Legalisasi Dokumen',
                'description' => 'Pelayanan legalisasi dokumen tingkat desa.',
                'sla_target_hours' => 48,
                'requirements' => "Dokumen asli yang akan dilegalisasi\nFotokopi dokumen\nKTP pemohon",
                'process' => "Pemohon menyerahkan dokumen\nPetugas memeriksa keabsahan dokumen\nDokumen dilegalisasi oleh pejabat berwenang",
                'icon' => 'LD',
                'is_featured' => false,
            ],
            [
                'name' => 'Pelayanan Pengaduan Warga',
                'description' => 'Kanal pengaduan dan aspirasi masyarakat.',
                'sla_target_hours' => 72,
                'requirements' => "Identitas pelapor\nUraian aduan\nLampiran pendukung (jika ada)",
                'process' => "Aduan diterima sistem desa\nAduan diverifikasi dan diteruskan ke unit terkait\nWarga menerima tindak lanjut/status penanganan",
                'icon' => 'PW',
                'is_featured' => true,
            ],
            [
                'name' => 'Informasi Bantuan Sosial',
                'description' => 'Informasi program bantuan sosial untuk warga.',
                'sla_target_hours' => 120,
                'requirements' => "NIK dan KK calon penerima\nData kondisi sosial ekonomi\nDokumen pendukung sesuai program",
                'process' => "Warga mengajukan permohonan informasi/pendaftaran\nPetugas mengecek kesesuaian kriteria\nHasil verifikasi diumumkan melalui kanal resmi desa",
                'icon' => 'BS',
                'is_featured' => false,
            ],
        ];

        foreach ($services as $service) {
            VillageService::query()->updateOrCreate(
                ['slug' => Str::slug($service['name'])],
                [
                    'village_id' => $village->id,
                    'name' => $service['name'],
                    'description' => $service['description'],
                    'sla_target_hours' => $service['sla_target_hours'],
                    'requirements' => $service['requirements'],
                    'process' => $service['process'],
                    'icon' => $service['icon'],
                    'is_featured' => $service['is_featured'],
                    'is_published' => true,
                    'published_at' => now(),
                ]
            );
        }

        $officials = [
            ['name' => 'Kaur Umum', 'position' => 'KAUR Umum & TU', 'unit' => 'Sekretariat', 'sort_order' => 2, 'is_highlighted' => true],
            ['name' => 'Kaur Perencanaan', 'position' => 'KAUR Perencanaan', 'unit' => 'Sekretariat', 'sort_order' => 3, 'is_highlighted' => false],
            ['name' => 'Kaur Keuangan', 'position' => 'KAUR Keuangan', 'unit' => 'Sekretariat', 'sort_order' => 4, 'is_highlighted' => false],
            ['name' => 'Kasi Pemerintahan', 'position' => 'Kasi Pemerintahan', 'unit' => 'Pemerintahan', 'sort_order' => 5, 'is_highlighted' => false],
            ['name' => 'Kasi Kesejahteraan Rakyat', 'position' => 'Kasi Kesejahteraan Rakyat', 'unit' => 'Kesejahteraan Rakyat', 'sort_order' => 6, 'is_highlighted' => false],
            ['name' => 'Kasi Pelayanan', 'position' => 'Kasi Pelayanan', 'unit' => 'Pelayanan', 'sort_order' => 7, 'is_highlighted' => false],
        ];

        foreach ($officials as $official) {
            VillageOfficial::query()->updateOrCreate(
                [
                    'village_id' => $village->id,
                    'name' => $official['name'],
                ],
                [
                    'position' => $official['position'],
                    'unit' => $official['unit'],
                    'sort_order' => $official['sort_order'],
                    'is_highlighted' => $official['is_highlighted'],
                    'is_published' => true,
                    'published_at' => now(),
                ]
            );
        }

        VillageHeadMessage::query()->updateOrCreate(
            [
                'village_id' => $village->id,
                'name' => $village->head_name ?? 'Kepala Desa Dangin Puri Kauh',
            ],
            [
                'position' => 'Kepala Desa',
                'message' => $village->head_greeting ?? 'Pemerintah desa berkomitmen memberi layanan cepat, transparan, dan inklusif bagi seluruh warga.',
                'signature' => 'Salam hormat, Kepala Desa',
                'is_published' => true,
                'published_at' => now(),
            ]
        );

        $newsItems = [
            [
                'title' => 'Posyandu Balita Banjar Teges Kawan 2024',
                'slug' => 'posyandu-balita-banjar-teges-kawan-2024',
                'content' => 'Kegiatan Posyandu Balita dilaksanakan di Banjar Teges Kawan sebagai bagian dari pelayanan kesehatan dasar masyarakat.',
                'thumbnail' => 'https://images.unsplash.com/photo-1526256262350-7da7584cf5eb?auto=format&fit=crop&w=1280&q=80',
                'view_count' => 126,
                'published_at' => Carbon::parse('2024-03-26 11:43:53'),
            ],
            [
                'title' => 'Posbindu Banjar Abiantimbul',
                'slug' => 'posbindu-banjar-abiantimbul',
                'content' => 'Pelaksanaan Posbindu di Banjar Abiantimbul untuk pemeriksaan kesehatan rutin warga.',
                'thumbnail' => 'https://images.unsplash.com/photo-1516841273335-e39b37888115?auto=format&fit=crop&w=1280&q=80',
                'view_count' => 98,
                'published_at' => Carbon::parse('2024-03-26 11:40:27'),
            ],
            [
                'title' => 'Kunjungan Kerja Kabupaten Gorontalo',
                'slug' => 'kunjungan-kerja-kabupaten-gorontalo',
                'content' => 'Desa Dangin Puri Kauh menerima kunjungan kerja dari Kabupaten Gorontalo dalam rangka studi tata kelola desa.',
                'thumbnail' => 'https://images.unsplash.com/photo-1528605248644-14dd04022da1?auto=format&fit=crop&w=1280&q=80',
                'view_count' => 87,
                'published_at' => Carbon::parse('2024-03-26 11:36:43'),
            ],
        ];

        foreach ($newsItems as $item) {
            $news = News::query()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'village_id' => $village->id,
                    'title' => $item['title'],
                    'content' => $item['content'],
                    'thumbnail' => $item['thumbnail'] ?? null,
                    'view_count' => $item['view_count'] ?? 0,
                    'is_published' => true,
                    'published_at' => $item['published_at'],
                ]
            );

            $galleryMap = [
                'posyandu-balita-banjar-teges-kawan-2024' => [
                    'https://images.unsplash.com/photo-1526256262350-7da7584cf5eb?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1576765608866-5b51046452be?auto=format&fit=crop&w=1200&q=80',
                ],
                'posbindu-banjar-abiantimbul' => [
                    'https://images.unsplash.com/photo-1579154204601-01588f351e67?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1584515933487-779824d29309?auto=format&fit=crop&w=1200&q=80',
                ],
                'kunjungan-kerja-kabupaten-gorontalo' => [
                    'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1515187029135-18ee286d815b?auto=format&fit=crop&w=1200&q=80',
                ],
            ];

            foreach (($galleryMap[$item['slug']] ?? []) as $index => $url) {
                NewsImage::query()->updateOrCreate(
                    [
                        'news_id' => $news->id,
                        'sort_order' => $index,
                    ],
                    [
                        'image_path' => $url,
                        'caption' => 'Dokumentasi kegiatan',
                    ]
                );
            }
        }

        $announcement = Announcement::query()->updateOrCreate(
            ['title' => 'Selamat Datang di Website Resmi Desa Dangin Puri Kauh'],
            [
                'village_id' => $village->id,
                'content' => 'Website resmi desa digunakan sebagai pusat informasi, layanan publik, dan transparansi pemerintahan desa.',
                'reference_url' => 'https://www.danginpurikauh.denpasarkota.go.id/',
                'is_published' => true,
                'published_at' => now(),
            ]
        );

        $agendas = [
            [
                'title' => 'Pelayanan Posyandu Bulanan',
                'description' => 'Pelayanan kesehatan ibu dan balita pada minggu pertama setiap bulan.',
                'location' => 'Balai Banjar',
                'latitude' => -8.6509812,
                'longitude' => 115.2152515,
                'map_url' => 'https://maps.google.com/?q=-8.6509812,115.2152515',
                'poster_path' => 'https://images.unsplash.com/photo-1576765608866-5b51046452be?auto=format&fit=crop&w=1280&q=80',
                'start_at' => now()->addDays(7)->setTime(8, 0),
                'end_at' => now()->addDays(7)->setTime(11, 0),
            ],
            [
                'title' => 'Musyawarah Desa Triwulan',
                'description' => 'Rapat koordinasi pembangunan desa bersama perangkat dan unsur masyarakat.',
                'location' => 'Kantor Desa Dangin Puri Kauh',
                'latitude' => -8.6512299,
                'longitude' => 115.2148033,
                'map_url' => 'https://maps.google.com/?q=-8.6512299,115.2148033',
                'poster_path' => 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=1280&q=80',
                'start_at' => now()->addDays(14)->setTime(9, 0),
                'end_at' => now()->addDays(14)->setTime(12, 0),
            ],
        ];

        foreach ($agendas as $agenda) {
            Agenda::query()->updateOrCreate(
                [
                    'village_id' => $village->id,
                    'title' => $agenda['title'],
                ],
                [
                    'description' => $agenda['description'],
                    'location' => $agenda['location'],
                    'latitude' => $agenda['latitude'] ?? null,
                    'longitude' => $agenda['longitude'] ?? null,
                    'map_url' => $agenda['map_url'] ?? null,
                    'poster_path' => $agenda['poster_path'] ?? null,
                    'start_at' => $agenda['start_at'],
                    'end_at' => $agenda['end_at'],
                    'is_published' => true,
                    'published_at' => now(),
                ]
            );
        }

        $galleryItems = [
            ['title' => 'Kegiatan Posyandu', 'caption' => 'Dokumentasi pelayanan Posyandu desa.', 'category' => 'Kesehatan'],
            ['title' => 'Musyawarah Desa', 'caption' => 'Forum koordinasi pembangunan desa.', 'category' => 'Pemerintahan'],
            ['title' => 'Pelayanan Administrasi', 'caption' => 'Aktivitas pelayanan masyarakat di kantor desa.', 'category' => 'Pelayanan'],
            ['title' => 'Kegiatan Warga', 'caption' => 'Gotong royong dan partisipasi masyarakat.', 'category' => 'Sosial'],
        ];

        foreach ($galleryItems as $gallery) {
            Gallery::query()->updateOrCreate(
                [
                    'village_id' => $village->id,
                    'title' => $gallery['title'],
                ],
                [
                    'caption' => $gallery['caption'],
                    'category' => $gallery['category'],
                    'is_published' => true,
                    'published_at' => now(),
                ]
            );
        }

        $assetItems = [
            [
                'name' => 'Kantor Desa Dangin Puri Kauh',
                'type' => 'aset_desa',
                'subcategory' => 'Kantor Pemerintahan',
                'description' => 'Pusat pelayanan administrasi dan pemerintahan desa.',
                'address' => 'Jl. Gunung Soputan No.49, Denpasar',
                'latitude' => -8.6463603,
                'longitude' => 115.2161072,
                'map_url' => 'https://maps.google.com/?q=-8.6463603,115.2161072',
                'sort_order' => 1,
            ],
            [
                'name' => 'Lapangan Desa',
                'type' => 'aset_desa',
                'subcategory' => 'Lapangan Olahraga',
                'description' => 'Lapangan multifungsi untuk kegiatan olahraga dan acara warga.',
                'address' => 'Dangin Puri Kauh',
                'latitude' => -8.6493800,
                'longitude' => 115.2148300,
                'map_url' => 'https://maps.google.com/?q=-8.64938,115.21483',
                'sort_order' => 2,
            ],
            [
                'name' => 'Balai Banjar Teges',
                'type' => 'fasilitas_umum',
                'subcategory' => 'Balai Banjar',
                'description' => 'Tempat rapat banjar dan kegiatan sosial masyarakat.',
                'address' => 'Banjar Teges, Dangin Puri Kauh',
                'latitude' => -8.6511200,
                'longitude' => 115.2171000,
                'map_url' => 'https://maps.google.com/?q=-8.65112,115.2171',
                'sort_order' => 3,
            ],
            [
                'name' => 'Pasar Tradisional Setempat',
                'type' => 'pasar',
                'subcategory' => 'Pasar Rakyat',
                'description' => 'Sentra perdagangan kebutuhan harian masyarakat desa.',
                'address' => 'Kawasan Denpasar Utara',
                'latitude' => -8.6482000,
                'longitude' => 115.2119000,
                'map_url' => 'https://maps.google.com/?q=-8.6482,115.2119',
                'sort_order' => 4,
            ],
            [
                'name' => 'UMKM Kerajinan Lokal',
                'type' => 'umkm',
                'subcategory' => 'Kerajinan',
                'description' => 'Lokasi UMKM binaan desa bidang kerajinan rumah tangga.',
                'address' => 'Lingkungan Dangin Puri Kauh',
                'latitude' => -8.6520100,
                'longitude' => 115.2136600,
                'map_url' => 'https://maps.google.com/?q=-8.65201,115.21366',
                'sort_order' => 5,
            ],
            [
                'name' => 'PAUD/TK Desa',
                'type' => 'pendidikan',
                'subcategory' => 'Pendidikan Anak',
                'description' => 'Fasilitas pendidikan usia dini dalam wilayah desa.',
                'address' => 'Dangin Puri Kauh',
                'latitude' => -8.6505300,
                'longitude' => 115.2129200,
                'map_url' => 'https://maps.google.com/?q=-8.65053,115.21292',
                'sort_order' => 6,
            ],
            [
                'name' => 'Puskesmas Pembantu',
                'type' => 'kesehatan',
                'subcategory' => 'Layanan Kesehatan',
                'description' => 'Fasilitas kesehatan dasar untuk layanan warga.',
                'address' => 'Denpasar Utara',
                'latitude' => -8.6478600,
                'longitude' => 115.2154600,
                'map_url' => 'https://maps.google.com/?q=-8.64786,115.21546',
                'sort_order' => 7,
            ],
        ];

        foreach ($assetItems as $asset) {
            VillageAsset::query()->updateOrCreate(
                [
                    'village_id' => $village->id,
                    'name' => $asset['name'],
                ],
                [
                    'type' => $asset['type'],
                    'subcategory' => $asset['subcategory'],
                    'description' => $asset['description'],
                    'address' => $asset['address'],
                    'latitude' => $asset['latitude'],
                    'longitude' => $asset['longitude'],
                    'map_url' => $asset['map_url'],
                    'sort_order' => $asset['sort_order'],
                    'is_published' => true,
                    'published_at' => now(),
                ]
            );
        }

        $populationItems = [
            ['year' => 2023, 'male' => 7980, 'female' => 7935, 'households' => 4650],
            ['year' => 2024, 'male' => 8045, 'female' => 8012, 'households' => 4708],
            ['year' => 2025, 'male' => 8092, 'female' => 8078, 'households' => 4738],
        ];

        foreach ($populationItems as $row) {
            VillagePopulation::query()->updateOrCreate(
                [
                    'village_id' => $village->id,
                    'year' => $row['year'],
                ],
                [
                    'male' => $row['male'],
                    'female' => $row['female'],
                    'households' => $row['households'],
                    'sort_order' => 0,
                    'is_published' => true,
                    'published_at' => now(),
                ]
            );
        }

        $apbdesItems = [
            ['fiscal_year' => 2025, 'type' => 'pendapatan', 'category' => 'Dana Desa', 'amount' => 1250000000],
            ['fiscal_year' => 2025, 'type' => 'pendapatan', 'category' => 'ADD', 'amount' => 760000000],
            ['fiscal_year' => 2025, 'type' => 'belanja', 'category' => 'Belanja Penyelenggaraan Pemerintahan Desa', 'amount' => 680000000],
            ['fiscal_year' => 2025, 'type' => 'belanja', 'category' => 'Belanja Pembangunan Desa', 'amount' => 940000000],
            ['fiscal_year' => 2025, 'type' => 'belanja', 'category' => 'Belanja Pembinaan Kemasyarakatan', 'amount' => 390000000],
            ['fiscal_year' => 2025, 'type' => 'pembiayaan', 'category' => 'Silpa Tahun Lalu', 'amount' => 230000000],
        ];

        foreach ($apbdesItems as $index => $row) {
            VillageApbdesItem::query()->updateOrCreate(
                [
                    'village_id' => $village->id,
                    'fiscal_year' => $row['fiscal_year'],
                    'type' => $row['type'],
                    'category' => $row['category'],
                ],
                [
                    'amount' => $row['amount'],
                    'sort_order' => $index,
                    'is_published' => true,
                    'published_at' => now(),
                ]
            );
        }

        $otherInfographics = [
            ['title' => 'Jumlah Banjar', 'value' => '12', 'unit' => 'banjar', 'icon' => 'BNJ', 'color' => '#0c3f7f'],
            ['title' => 'UMKM Terdaftar', 'value' => '87', 'unit' => 'unit', 'icon' => 'UMKM', 'color' => '#16a34a'],
            ['title' => 'Kader PKK Aktif', 'value' => '54', 'unit' => 'orang', 'icon' => 'PKK', 'color' => '#ec4899'],
        ];

        foreach ($otherInfographics as $index => $item) {
            VillageInfographicItem::query()->updateOrCreate(
                [
                    'village_id' => $village->id,
                    'title' => $item['title'],
                ],
                [
                    'value' => $item['value'],
                    'unit' => $item['unit'],
                    'icon' => $item['icon'],
                    'color' => $item['color'],
                    'sort_order' => $index,
                    'is_published' => true,
                    'published_at' => now(),
                ]
            );
        }

        $profilePages = [
            [
                'slug' => VillageProfilePage::SLUG_GAMBARAN,
                'title' => 'Gambaran Umum Desa',
                'subtitle' => 'Profil wilayah, kependudukan, dan potensi Desa Dangin Puri Kauh.',
                'source_url' => 'https://www.danginpurikauh.denpasarkota.go.id/page/gambaran-umum-desa',
            ],
            [
                'slug' => VillageProfilePage::SLUG_SEJARAH,
                'title' => 'Sejarah Desa',
                'subtitle' => 'Riwayat pembentukan dan perkembangan Desa Dangin Puri Kauh.',
                'source_url' => 'https://www.danginpurikauh.denpasarkota.go.id/page/sejarah-desa',
            ],
            [
                'slug' => VillageProfilePage::SLUG_VISIMISI,
                'title' => 'Visi dan Misi Desa',
                'subtitle' => 'Arah kebijakan dan tujuan pembangunan desa.',
                'source_url' => 'https://www.danginpurikauh.denpasarkota.go.id/page/visi-dan-misi-desa',
            ],
            [
                'slug' => VillageProfilePage::SLUG_ORGANISASI,
                'title' => 'Susunan Organisasi Pemerintah Desa Dangin Puri Kauh',
                'subtitle' => 'Struktur aparatur desa berdasarkan unit kerja.',
                'source_url' => 'https://www.danginpurikauh.denpasarkota.go.id/page/susunan-organisasi',
            ],
        ];

        foreach ($profilePages as $page) {
            VillageProfilePage::query()->updateOrCreate(
                [
                    'village_id' => $village->id,
                    'slug' => $page['slug'],
                ],
                [
                    'title' => $page['title'],
                    'subtitle' => $page['subtitle'],
                    'source_url' => $page['source_url'],
                    'is_published' => true,
                    'published_at' => now(),
                ]
            );
        }
    }
}
