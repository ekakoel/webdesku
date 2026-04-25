<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\Announcement;
use App\Models\Gallery;
use App\Models\News;
use App\Models\ServiceRequest;
use App\Models\VillageAsset;
use App\Models\VillagePopulation;
use App\Models\VillagePopulationStat;
use App\Models\VillageTransparencyItem;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'news' => $this->countRows('news', News::class),
            'agendas' => $this->countRows('agendas', Agenda::class),
            'announcements' => $this->countRows('announcements', Announcement::class),
            'services_requests' => $this->countRows('service_requests', ServiceRequest::class),
            'galleries' => $this->countRows('galleries', Gallery::class),
            'assets' => $this->countRows('village_assets', VillageAsset::class),
            'population_years' => $this->countRows('village_populations', VillagePopulation::class),
            'population_stats' => $this->countRows('village_population_stats', VillagePopulationStat::class),
            'transparency' => $this->countRows('village_transparency_items', VillageTransparencyItem::class),
        ];

        $status = [
            'news_published' => $this->countByBoolean('news', News::class, 'is_published', true),
            'news_draft' => $this->countByBoolean('news', News::class, 'is_published', false),
            'agendas_published' => $this->countByBoolean('agendas', Agenda::class, 'is_published', true),
            'agendas_draft' => $this->countByBoolean('agendas', Agenda::class, 'is_published', false),
            'requests_done' => $this->countByString('service_requests', ServiceRequest::class, 'status', 'selesai'),
            'requests_pending' => $this->countByString('service_requests', ServiceRequest::class, 'status', 'diajukan'),
        ];

        return view('admin.dashboard', compact('stats', 'status'));
    }

    private function countRows(string $table, string $modelClass): int
    {
        if (!Schema::hasTable($table)) {
            return 0;
        }

        return (int) $modelClass::query()->count();
    }

    private function countByBoolean(string $table, string $modelClass, string $column, bool $value): int
    {
        if (!Schema::hasTable($table) || !Schema::hasColumn($table, $column)) {
            return 0;
        }

        return (int) $modelClass::query()->where($column, $value)->count();
    }

    private function countByString(string $table, string $modelClass, string $column, string $value): int
    {
        if (!Schema::hasTable($table) || !Schema::hasColumn($table, $column)) {
            return 0;
        }

        return (int) $modelClass::query()->where($column, $value)->count();
    }
}

