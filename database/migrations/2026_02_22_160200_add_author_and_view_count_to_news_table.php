<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            if (!Schema::hasColumn('news', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('village_id')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('news', 'view_count')) {
                $table->unsignedInteger('view_count')->default(0)->after('thumbnail');
            }
        });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            if (Schema::hasColumn('news', 'created_by')) {
                $table->dropConstrainedForeignId('created_by');
            }

            if (Schema::hasColumn('news', 'view_count')) {
                $table->dropColumn('view_count');
            }
        });
    }
};
