<?php

namespace App\Console\Commands;

use App\Models\Village;
use App\Services\BigBoundaryService;
use Illuminate\Console\Command;
use RuntimeException;

class ImportVillageBoundaryFromBig extends Command
{
    protected $signature = 'boundary:import-big
        {--slug= : Slug desa di tabel villages}
        {--village_id= : ID desa di tabel villages}
        {--desa= : Nama desa/kelurahan sesuai data BIG}
        {--kecamatan= : Nama kecamatan}
        {--kota= : Nama kota/kabupaten}
        {--provinsi= : Nama provinsi}';

    protected $description = 'Import batas wilayah desa dari BIG, simpan ke boundary_geojson, hitung centroid, dan update villages.';

    public function handle(BigBoundaryService $bigBoundaryService): int
    {
        $village = $this->resolveVillage();

        if (!$village) {
            $this->error('Data desa tidak ditemukan. Gunakan --slug atau --village_id.');

            return self::FAILURE;
        }

        $desa = $this->option('desa') ?: $this->normalizeVillageName($village->name);
        $kecamatan = $this->option('kecamatan') ?: $village->district;
        $kota = $this->option('kota') ?: $village->city;
        $provinsi = $this->option('provinsi') ?: $village->province;

        try {
            $result = $bigBoundaryService->importToVillage(
                $village,
                $desa,
                $kecamatan,
                $kota,
                $provinsi
            );
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        } catch (\Throwable $e) {
            $this->error('Gagal import batas wilayah: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info('Import batas wilayah berhasil.');
        $this->line('Desa: '.$village->name);
        $this->line('Latitude: '.$result['latitude']);
        $this->line('Longitude: '.$result['longitude']);

        return self::SUCCESS;
    }

    private function resolveVillage(): ?Village
    {
        if ($this->option('village_id')) {
            return Village::query()->find($this->option('village_id'));
        }

        if ($this->option('slug')) {
            return Village::query()->where('slug', $this->option('slug'))->first();
        }

        return Village::query()->first();
    }

    private function normalizeVillageName(string $name): string
    {
        return trim((string) preg_replace('/^(Desa|Kelurahan)\s+/i', '', $name));
    }
}
