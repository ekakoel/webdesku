<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate([
            'email' => 'admin@desa.test',
        ], [
            'name' => 'Admin Desa',
            'password' => bcrypt('password'),
            'role' => 'aparat',
            'email_verified_at' => now(),
        ]);

        $this->call(DanginPuriKauhSeeder::class);
    }
}
