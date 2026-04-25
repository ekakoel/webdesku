<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->deduplicateNewsSlugPerVillage();

        if (Schema::hasTable('news')) {
            Schema::table('news', function (Blueprint $table) {
                try {
                    $table->unique(['village_id', 'slug'], 'news_village_slug_unique');
                } catch (\Throwable $e) {
                }
                try {
                    $table->index(['village_id', 'is_published', 'published_at'], 'news_publish_lookup_idx');
                } catch (\Throwable $e) {
                }
            });
        }

        if (Schema::hasTable('agendas')) {
            Schema::table('agendas', function (Blueprint $table) {
                try {
                    $table->index(['village_id', 'is_published', 'start_at'], 'agendas_publish_start_idx');
                } catch (\Throwable $e) {
                }
            });
        }

        if (Schema::hasTable('announcements')) {
            Schema::table('announcements', function (Blueprint $table) {
                try {
                    $table->index(['village_id', 'is_published', 'published_at'], 'announcements_publish_lookup_idx');
                } catch (\Throwable $e) {
                }
            });
        }

        if (Schema::hasTable('services')) {
            Schema::table('services', function (Blueprint $table) {
                try {
                    $table->index(['village_id', 'is_published', 'is_featured'], 'services_publish_featured_idx');
                } catch (\Throwable $e) {
                }
            });
        }

        if (Schema::hasTable('galleries')) {
            Schema::table('galleries', function (Blueprint $table) {
                try {
                    $table->index(['village_id', 'is_published', 'created_at'], 'galleries_publish_lookup_idx');
                } catch (\Throwable $e) {
                }
            });
        }

        if (Schema::hasTable('sliders')) {
            Schema::table('sliders', function (Blueprint $table) {
                try {
                    $table->index(['village_id', 'is_published', 'is_active', 'sort_order'], 'sliders_publish_sort_idx');
                } catch (\Throwable $e) {
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('news')) {
            Schema::table('news', function (Blueprint $table) {
                try {
                    $table->dropUnique('news_village_slug_unique');
                } catch (\Throwable $e) {
                }
                try {
                    $table->dropIndex('news_publish_lookup_idx');
                } catch (\Throwable $e) {
                }
            });
        }

        if (Schema::hasTable('agendas')) {
            Schema::table('agendas', function (Blueprint $table) {
                try {
                    $table->dropIndex('agendas_publish_start_idx');
                } catch (\Throwable $e) {
                }
            });
        }

        if (Schema::hasTable('announcements')) {
            Schema::table('announcements', function (Blueprint $table) {
                try {
                    $table->dropIndex('announcements_publish_lookup_idx');
                } catch (\Throwable $e) {
                }
            });
        }

        if (Schema::hasTable('services')) {
            Schema::table('services', function (Blueprint $table) {
                try {
                    $table->dropIndex('services_publish_featured_idx');
                } catch (\Throwable $e) {
                }
            });
        }

        if (Schema::hasTable('galleries')) {
            Schema::table('galleries', function (Blueprint $table) {
                try {
                    $table->dropIndex('galleries_publish_lookup_idx');
                } catch (\Throwable $e) {
                }
            });
        }

        if (Schema::hasTable('sliders')) {
            Schema::table('sliders', function (Blueprint $table) {
                try {
                    $table->dropIndex('sliders_publish_sort_idx');
                } catch (\Throwable $e) {
                }
            });
        }
    }

    private function deduplicateNewsSlugPerVillage(): void
    {
        if (!Schema::hasTable('news') || !Schema::hasColumn('news', 'village_id') || !Schema::hasColumn('news', 'slug')) {
            return;
        }

        $duplicates = DB::table('news')
            ->select('village_id', 'slug', DB::raw('COUNT(*) as total'))
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->groupBy('village_id', 'slug')
            ->having('total', '>', 1)
            ->get();

        foreach ($duplicates as $duplicate) {
            $rows = DB::table('news')
                ->where('village_id', $duplicate->village_id)
                ->where('slug', $duplicate->slug)
                ->orderBy('id')
                ->get(['id', 'slug']);

            $keepFirst = true;
            foreach ($rows as $row) {
                if ($keepFirst) {
                    $keepFirst = false;
                    continue;
                }

                DB::table('news')
                    ->where('id', $row->id)
                    ->update(['slug' => $row->slug.'-'.$row->id]);
            }
        }
    }
};

