<?php

namespace App\Services;

use App\Models\Village;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class BigBoundaryService
{
    private const BIG_QUERY_URL = 'https://geoservices.big.go.id/rbi/rest/services/BATASWILAYAH/Administrasi_AR_KelDesa_10K/MapServer/0/query';

    public function importToVillage(
        Village $village,
        string $desa,
        ?string $kecamatan = null,
        ?string $kota = null,
        ?string $provinsi = null,
    ): array {
        $feature = $this->fetchFeature($desa, $kecamatan, $kota, $provinsi);

        $geometry = $feature['geometry'] ?? null;
        if (!$geometry) {
            throw new RuntimeException('Geometry batas wilayah tidak ditemukan pada respons BIG.');
        }

        [$latitude, $longitude] = $this->computeCentroid($geometry);

        $village->update([
            'boundary_geojson' => [
                'type' => 'Feature',
                'properties' => $feature['properties'] ?? [],
                'geometry' => $geometry,
            ],
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        return [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'properties' => $feature['properties'] ?? [],
        ];
    }

    public function fetchFeature(
        string $desa,
        ?string $kecamatan = null,
        ?string $kota = null,
        ?string $provinsi = null,
    ): array {
        $attempts = $this->buildQueryAttempts($desa, $kecamatan, $kota, $provinsi);
        $lastWhere = null;

        foreach ($attempts as $where) {
            $lastWhere = $where;
            $features = $this->queryFeatures($where);

            if (count($features) > 0) {
                return $this->pickBestFeature($features, $desa, $kecamatan, $kota, $provinsi);
            }
        }

        $fallbackWhere = $this->buildWhereClause(
            $this->normalize($desa, true),
            $kecamatan ? $this->normalize($kecamatan, true) : null,
            $kota ? $this->normalize($kota, true) : null,
            $provinsi ? $this->normalize($provinsi, true) : null
        );
        $fallbackFeatures = $this->queryFeatures($fallbackWhere);
        if (count($fallbackFeatures) > 0) {
            return $this->pickBestFeature($fallbackFeatures, $desa, $kecamatan, $kota, $provinsi);
        }

        throw new RuntimeException("Data batas wilayah tidak ditemukan di BIG. Coba sesuaikan nama desa/kecamatan/kota/provinsi. WHERE terakhir: {$lastWhere}");
    }

    private function buildWhereClause(
        string $desa,
        ?string $kecamatan = null,
        ?string $kota = null,
        ?string $provinsi = null,
    ): string {
        $clauses = ["UPPER(WADMKD) = '".$this->escapeSql($desa)."'"];

        if ($kecamatan) {
            $clauses[] = "UPPER(WADMKC) = '".$this->escapeSql($kecamatan)."'";
        }

        if ($kota) {
            $clauses[] = "UPPER(WADMKK) = '".$this->escapeSql($kota)."'";
        }

        if ($provinsi) {
            $clauses[] = "UPPER(WADMPR) = '".$this->escapeSql($provinsi)."'";
        }

        return implode(' AND ', $clauses);
    }

    private function normalize(string $value, bool $stripAdministrativePrefix = true): string
    {
        $normalized = strtoupper(trim($value));
        $normalized = preg_replace('/\s+/', ' ', (string) $normalized);

        if ($stripAdministrativePrefix) {
            $normalized = preg_replace('/^(DESA|KELURAHAN)\s+/', '', (string) $normalized);
            $normalized = preg_replace('/^(KEC\.?|KECAMATAN)\s+/', '', (string) $normalized);
            $normalized = preg_replace('/^(KAB\.?|KABUPATEN|KOTA)\s+/', '', (string) $normalized);
            $normalized = trim((string) $normalized);
        }

        return (string) $normalized;
    }

    private function escapeSql(string $value): string
    {
        return str_replace("'", "''", $value);
    }

    private function buildQueryAttempts(string $desa, ?string $kecamatan, ?string $kota, ?string $provinsi): array
    {
        $desaVariants = $this->buildNameVariants($desa, ['DESA', 'KELURAHAN']);
        $kecamatanVariants = $kecamatan ? $this->buildNameVariants($kecamatan, ['KECAMATAN']) : [null];
        $kotaVariants = $kota ? $this->buildNameVariants($kota, ['KOTA', 'KABUPATEN']) : [null];
        $provVariants = $provinsi ? $this->buildNameVariants($provinsi, []) : [null];

        $attempts = [];

        foreach ($desaVariants as $d) {
            foreach ($kecamatanVariants as $kc) {
                foreach ($kotaVariants as $kk) {
                    foreach ($provVariants as $pr) {
                        $attempts[] = $this->buildWhereClause($d, $kc, $kk, $pr);
                    }
                }
            }
        }

        foreach ($desaVariants as $d) {
            foreach ($kotaVariants as $kk) {
                foreach ($provVariants as $pr) {
                    $attempts[] = $this->buildWhereClause($d, null, $kk, $pr);
                }
            }
        }

        return array_values(array_unique(array_filter($attempts)));
    }

    private function buildNameVariants(string $value, array $prefixes): array
    {
        $base = $this->normalize($value, true);
        $raw = $this->normalize($value, false);

        $variants = [$base, $raw];
        foreach ($prefixes as $prefix) {
            $variants[] = "{$prefix} {$base}";
        }

        $variants[] = str_replace('.', '', $base);
        $variants[] = str_replace('.', '', $raw);

        return array_values(array_unique(array_filter(array_map('trim', $variants))));
    }

    private function queryFeatures(string $where): array
    {
        $response = Http::timeout(30)
            ->retry(2, 700)
            ->get(self::BIG_QUERY_URL, [
                'where' => $where,
                'outFields' => '*',
                'returnGeometry' => 'true',
                'f' => 'geojson',
                'outSR' => 4326,
            ])
            ->throw()
            ->json();

        return $response['features'] ?? [];
    }

    private function pickBestFeature(array $features, string $desa, ?string $kecamatan, ?string $kota, ?string $provinsi): array
    {
        if (count($features) === 1) {
            return $features[0];
        }

        $target = [
            'desa' => $this->normalize($desa, true),
            'kecamatan' => $kecamatan ? $this->normalize($kecamatan, true) : null,
            'kota' => $kota ? $this->normalize($kota, true) : null,
            'provinsi' => $provinsi ? $this->normalize($provinsi, true) : null,
        ];

        $scored = collect($features)->map(function (array $feature) use ($target) {
            $props = $feature['properties'] ?? [];

            $wadmkd = $this->normalize((string) ($props['WADMKD'] ?? ''), true);
            $wadmkc = $this->normalize((string) ($props['WADMKC'] ?? ''), true);
            $wadmkk = $this->normalize((string) ($props['WADMKK'] ?? ''), true);
            $wadmpr = $this->normalize((string) ($props['WADMPR'] ?? ''), true);

            $score = 0;
            if ($wadmkd === $target['desa']) {
                $score += 5;
            } elseif (Str::contains($wadmkd, $target['desa']) || Str::contains($target['desa'], $wadmkd)) {
                $score += 3;
            }

            if ($target['kecamatan']) {
                if ($wadmkc === $target['kecamatan']) {
                    $score += 3;
                } elseif (Str::contains($wadmkc, $target['kecamatan']) || Str::contains($target['kecamatan'], $wadmkc)) {
                    $score += 2;
                }
            }

            if ($target['kota']) {
                if ($wadmkk === $target['kota']) {
                    $score += 3;
                } elseif (Str::contains($wadmkk, $target['kota']) || Str::contains($target['kota'], $wadmkk)) {
                    $score += 2;
                }
            }

            if ($target['provinsi']) {
                if ($wadmpr === $target['provinsi']) {
                    $score += 2;
                } elseif (Str::contains($wadmpr, $target['provinsi']) || Str::contains($target['provinsi'], $wadmpr)) {
                    $score += 1;
                }
            }

            return [
                'score' => $score,
                'feature' => $feature,
            ];
        })->sortByDesc('score')->values();

        return $scored->first()['feature'] ?? $features[0];
    }

    private function computeCentroid(array $geometry): array
    {
        $type = $geometry['type'] ?? null;
        $coordinates = $geometry['coordinates'] ?? null;

        if (!$type || !$coordinates) {
            throw new RuntimeException('Format geometry tidak valid untuk menghitung centroid.');
        }

        if ($type === 'Polygon') {
            return $this->centroidFromRing($coordinates[0] ?? []);
        }

        if ($type === 'MultiPolygon') {
            $weightedX = 0.0;
            $weightedY = 0.0;
            $totalWeight = 0.0;

            foreach ($coordinates as $polygon) {
                $ring = $polygon[0] ?? [];
                if (count($ring) < 3) {
                    continue;
                }

                [$lat, $lng, $area] = $this->centroidAndAreaFromRing($ring);
                $weight = abs($area);

                $weightedX += $lng * $weight;
                $weightedY += $lat * $weight;
                $totalWeight += $weight;
            }

            if ($totalWeight > 0) {
                return [$weightedY / $totalWeight, $weightedX / $totalWeight];
            }
        }

        throw new RuntimeException("Tipe geometry '{$type}' belum didukung untuk centroid.");
    }

    private function centroidFromRing(array $ring): array
    {
        [$lat, $lng] = $this->centroidAndAreaFromRing($ring);

        return [$lat, $lng];
    }

    private function centroidAndAreaFromRing(array $ring): array
    {
        if (count($ring) < 3) {
            throw new RuntimeException('Ring polygon tidak cukup titik untuk menghitung centroid.');
        }

        $sum = 0.0;
        $cx = 0.0;
        $cy = 0.0;

        $pointCount = count($ring);
        for ($i = 0; $i < $pointCount - 1; $i++) {
            [$x1, $y1] = $ring[$i];
            [$x2, $y2] = $ring[$i + 1];
            $cross = ($x1 * $y2) - ($x2 * $y1);
            $sum += $cross;
            $cx += ($x1 + $x2) * $cross;
            $cy += ($y1 + $y2) * $cross;
        }

        $area = $sum / 2.0;
        if (abs($area) < 1e-12) {
            $totalX = 0.0;
            $totalY = 0.0;
            foreach ($ring as $point) {
                $totalX += $point[0];
                $totalY += $point[1];
            }

            $count = count($ring);

            return [$totalY / $count, $totalX / $count, 0.0];
        }

        $centroidX = $cx / (6.0 * $area);
        $centroidY = $cy / (6.0 * $area);

        return [$centroidY, $centroidX, $area];
    }
}
