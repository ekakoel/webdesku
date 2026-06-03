<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('villages', function (Blueprint $table) {
            $table->boolean('instagram_enabled')->default(false)->after('head_greeting');
            $table->string('instagram_username')->nullable()->after('instagram_enabled');
            $table->string('instagram_user_id')->nullable()->after('instagram_username');
            $table->text('instagram_access_token')->nullable()->after('instagram_user_id');
            $table->timestamp('instagram_connected_at')->nullable()->after('instagram_access_token');
            $table->timestamp('instagram_last_sync_at')->nullable()->after('instagram_connected_at');
            $table->text('instagram_last_error')->nullable()->after('instagram_last_sync_at');
        });
    }

    public function down(): void
    {
        Schema::table('villages', function (Blueprint $table) {
            $table->dropColumn([
                'instagram_enabled',
                'instagram_username',
                'instagram_user_id',
                'instagram_access_token',
                'instagram_connected_at',
                'instagram_last_sync_at',
                'instagram_last_error',
            ]);
        });
    }
};
