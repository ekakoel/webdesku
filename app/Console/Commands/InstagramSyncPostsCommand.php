<?php

namespace App\Console\Commands;

use App\Models\Village;
use App\Services\InstagramFeedService;
use Illuminate\Console\Command;

class InstagramSyncPostsCommand extends Command
{
    protected $signature = 'instagram:sync-posts {--village_id= : Sinkronisasi untuk 1 desa} {--limit=6 : Jumlah post terbaru yang disimpan}';

    protected $description = 'Sinkronisasi postingan Instagram terbaru ke tabel lokal desa.';

    public function handle(InstagramFeedService $instagramFeedService): int
    {
        $limit = (int) $this->option('limit');

        $query = Village::query()
            ->where('instagram_enabled', true)
            ->whereNotNull('instagram_access_token');

        if ($this->option('village_id')) {
            $query->whereKey((int) $this->option('village_id'));
        }

        $villages = $query->get();
        if ($villages->isEmpty()) {
            $this->warn('Tidak ada desa dengan integrasi Instagram aktif.');
            return self::SUCCESS;
        }

        foreach ($villages as $village) {
            try {
                $count = $instagramFeedService->syncVillage($village, $limit);
                $this->info("{$village->name}: sinkron {$count} postingan.");
            } catch (\Throwable $e) {
                $village->update([
                    'instagram_last_error' => $e->getMessage(),
                    'instagram_last_sync_at' => now(),
                ]);
                $this->error("{$village->name}: {$e->getMessage()}");
            }
        }

        return self::SUCCESS;
    }
}
