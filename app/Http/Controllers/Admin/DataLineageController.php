<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DataLineageController extends Controller
{
    public function index(): View
    {
        $modules = [
            [
                'key' => 'aset_desa',
                'label' => 'Infografis Aset Desa',
                'table' => 'village_assets',
                'public_page' => 'Infografis > Aset Desa',
                'admin_route' => 'admin.village-assets.index',
            ],
            [
                'key' => 'penduduk_tahunan',
                'label' => 'Infografis Penduduk Tahunan',
                'table' => 'village_populations',
                'public_page' => 'Infografis > Penduduk',
                'admin_route' => 'admin.village-populations.index',
            ],
            [
                'key' => 'penduduk_kategori',
                'label' => 'Statistik Kategori Penduduk',
                'table' => 'village_population_stats',
                'public_page' => 'Infografis > Penduduk, Gambaran Umum Desa',
                'admin_route' => 'admin.village-population-stats.index',
            ],
            [
                'key' => 'luas_wilayah',
                'label' => 'Luas Wilayah Menurut Penggunaan',
                'table' => 'village_land_use_areas',
                'public_page' => 'Profil > Gambaran Umum Desa',
                'admin_route' => 'admin.village-land-use-areas.index',
            ],
            [
                'key' => 'infografis_lainnya',
                'label' => 'Infografis Lainnya',
                'table' => 'village_infographic_items',
                'public_page' => 'Infografis > Lainnya',
                'admin_route' => 'admin.village-infographic-items.index',
            ],
            [
                'key' => 'apbdes',
                'label' => 'APBDes (Rincian & Ringkasan)',
                'table' => 'village_apbdes_items',
                'public_page' => 'Transparansi > APBDes, Infografis',
                'admin_route' => 'admin.village-apbdes-items.index',
            ],
            [
                'key' => 'dokumen_apbdes',
                'label' => 'Dokumen/Laporan APBDes',
                'table' => 'village_apbdes_documents',
                'public_page' => 'Transparansi > APBDes',
                'admin_route' => 'admin.village-apbdes-documents.index',
            ],
            [
                'key' => 'dokumen_transparansi',
                'label' => 'Dokumen Transparansi Umum',
                'table' => 'village_transparency_documents',
                'public_page' => 'Transparansi > Dokumen Transparansi',
                'admin_route' => 'admin.village-transparency-documents.index',
            ],
        ];

        $rows = collect($modules)->map(function (array $module) {
            $table = $module['table'];
            $exists = Schema::hasTable($table);
            $count = 0;
            $publishedCount = 0;
            $lastUpdated = null;

            if ($exists) {
                $query = DB::table($table);
                $count = (int) $query->count();
                if (Schema::hasColumn($table, 'is_published')) {
                    $publishedCount = (int) DB::table($table)->where('is_published', true)->count();
                } else {
                    $publishedCount = $count;
                }
                if (Schema::hasColumn($table, 'updated_at')) {
                    $lastUpdated = DB::table($table)->max('updated_at');
                }
            }

            $module['exists'] = $exists;
            $module['count'] = $count;
            $module['published_count'] = $publishedCount;
            $module['last_updated'] = $lastUpdated;

            return $module;
        });

        return view('admin.data-lineage.index', [
            'rows' => $rows,
        ]);
    }
}
