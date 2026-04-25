<?php

use App\Models\Announcement;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('announcements')) {
            return;
        }

        Schema::table('announcements', function (Blueprint $table) {
            if (!Schema::hasColumn('announcements', 'type')) {
                $table->string('type', 40)->default(Announcement::TYPE_UMUM)->after('village_id');
            }
        });

        DB::table('announcements')
            ->whereNull('type')
            ->orWhere('type', '')
            ->update(['type' => Announcement::TYPE_UMUM]);

        Schema::table('announcements', function (Blueprint $table) {
            try {
                $table->index(['village_id', 'type', 'is_published'], 'announcements_type_publish_idx');
            } catch (\Throwable $e) {
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('announcements')) {
            return;
        }

        Schema::table('announcements', function (Blueprint $table) {
            try {
                $table->dropIndex('announcements_type_publish_idx');
            } catch (\Throwable $e) {
            }
            if (Schema::hasColumn('announcements', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};

