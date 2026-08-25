<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('village_infographic_items', function (Blueprint $table) {
            if (! Schema::hasColumn('village_infographic_items', 'year')) {
                $table->unsignedSmallInteger('year')->nullable()->after('category');
            }

            if (! Schema::hasColumn('village_infographic_items', 'source')) {
                $table->string('source')->nullable()->after('unit');
            }

            if (! Schema::hasColumn('village_infographic_items', 'notes')) {
                $table->text('notes')->nullable()->after('source');
            }
        });

        Schema::table('village_infographic_items', function (Blueprint $table) {
            $table->index(['village_id', 'category', 'year'], 'village_infographic_items_village_category_year_idx');
            $table->index(['is_published', 'category', 'year'], 'village_infographic_items_public_category_year_idx');
        });
    }

    public function down(): void
    {
        Schema::table('village_infographic_items', function (Blueprint $table) {
            $table->dropIndex('village_infographic_items_village_category_year_idx');
            $table->dropIndex('village_infographic_items_public_category_year_idx');
        });

        Schema::table('village_infographic_items', function (Blueprint $table) {
            if (Schema::hasColumn('village_infographic_items', 'notes')) {
                $table->dropColumn('notes');
            }

            if (Schema::hasColumn('village_infographic_items', 'source')) {
                $table->dropColumn('source');
            }

            if (Schema::hasColumn('village_infographic_items', 'year')) {
                $table->dropColumn('year');
            }
        });
    }
};
