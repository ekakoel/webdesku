<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE agendas MODIFY map_url TEXT NULL');
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE agendas MODIFY map_url VARCHAR(255) NULL');
    }
};
