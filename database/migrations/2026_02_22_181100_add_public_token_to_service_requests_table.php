<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->string('public_token', 40)->nullable()->unique()->after('ticket_code');
        });

        if (Schema::hasTable('service_requests')) {
            DB::table('service_requests')
                ->whereNull('public_token')
                ->orderBy('id')
                ->chunkById(100, function ($rows): void {
                    foreach ($rows as $row) {
                        DB::table('service_requests')
                            ->where('id', $row->id)
                            ->update(['public_token' => Str::random(40)]);
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropUnique(['public_token']);
            $table->dropColumn('public_token');
        });
    }
};
