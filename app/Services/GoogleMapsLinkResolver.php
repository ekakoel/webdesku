<?php

namespace App\Services;

use GuzzleHttp\Client;

class GoogleMapsLinkResolver
{
    public function resolve(string $url): ?array
    {
        $url = trim($url);
        if ($url === '') {
            return null;
        }

        $finalUrl = $this->followRedirects($url) ?? $url;
        $coords = $this->extractCoordinates($finalUrl);
        if (!$coords) {
            return null;
        }

        return [
            'latitude' => $coords['latitude'],
            'longitude' => $coords['longitude'],
            'final_url' => $finalUrl,
        ];
    }

    private function followRedirects(string $url): ?string
    {
        try {
            $client = new Client([
                'timeout' => 10,
                'connect_timeout' => 6,
                'http_errors' => false,
                'allow_redirects' => [
                    'max' => 10,
                    'strict' => true,
                    'referer' => true,
                    'track_redirects' => true,
                ],
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (compatible; Webdesku/1.0)',
                    'Accept-Language' => 'id-ID,id;q=0.9,en;q=0.8',
                ],
            ]);

            $response = $client->request('GET', $url);
            $history = $response->getHeader('X-Guzzle-Redirect-History');
            if (count($history) > 0) {
                return (string) end($history);
            }

            return $url;
        } catch (\Throwable) {
            return null;
        }
    }

    private function extractCoordinates(string $url): ?array
    {
        $patterns = [
            '/@(-?\d+\.\d+),(-?\d+\.\d+),/i',
            '/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/i',
            '/[?&](?:q|ll|destination)=(-?\d+\.\d+),(-?\d+\.\d+)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $m)) {
                $lat = (float) $m[1];
                $lng = (float) $m[2];

                if ($lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180) {
                    return [
                        'latitude' => round($lat, 7),
                        'longitude' => round($lng, 7),
                    ];
                }
            }
        }

        return null;
    }
}
