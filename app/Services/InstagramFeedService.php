<?php

namespace App\Services;

use App\Models\Village;
use App\Models\VillageInstagramPost;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class InstagramFeedService
{
    public function syncVillage(Village $village, int $limit = 6): int
    {
        if (!$village->instagram_enabled) {
            return 0;
        }

        $token = trim((string) $village->instagram_access_token);
        if ($token === '') {
            throw new RuntimeException('Access token Instagram belum diisi.');
        }

        $response = Http::timeout(20)
            ->acceptJson()
            ->get('https://graph.instagram.com/me/media', [
                'fields' => 'id,caption,media_type,media_url,thumbnail_url,permalink,timestamp',
                'access_token' => $token,
                'limit' => max(1, min($limit, 12)),
            ]);

        if (!$response->successful()) {
            $message = data_get($response->json(), 'error.message')
                ?: 'Permintaan ke Instagram Graph API gagal.';
            throw new RuntimeException($message);
        }

        $items = collect((array) data_get($response->json(), 'data', []));
        if ($items->isEmpty()) {
            $village->update([
                'instagram_last_sync_at' => now(),
                'instagram_last_error' => null,
            ]);
            return 0;
        }

        foreach ($items as $item) {
            $postId = (string) ($item['id'] ?? '');
            if ($postId === '') {
                continue;
            }

            VillageInstagramPost::query()->updateOrCreate(
                [
                    'village_id' => $village->id,
                    'instagram_post_id' => $postId,
                ],
                [
                    'media_type' => (string) ($item['media_type'] ?? ''),
                    'caption' => (string) ($item['caption'] ?? ''),
                    'media_url' => (string) ($item['media_url'] ?? ''),
                    'thumbnail_url' => (string) ($item['thumbnail_url'] ?? ''),
                    'permalink' => (string) ($item['permalink'] ?? ''),
                    'posted_at' => $item['timestamp'] ?? null,
                    'payload' => $item,
                ]
            );
        }

        VillageInstagramPost::query()
            ->where('village_id', $village->id)
            ->orderByDesc('posted_at')
            ->orderByDesc('id')
            ->skip(max(1, min($limit, 12)))
            ->take(200)
            ->delete();

        $village->update([
            'instagram_connected_at' => $village->instagram_connected_at ?: now(),
            'instagram_last_sync_at' => now(),
            'instagram_last_error' => null,
        ]);

        return $items->count();
    }
}
