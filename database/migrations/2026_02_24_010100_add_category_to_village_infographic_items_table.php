<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('village_infographic_items', function (Blueprint $table) {
            $table->string('category', 50)->default('umum')->after('village_id');
            $table->index(['category', 'sort_order'], 'village_infographic_items_category_sort_idx');
        });
    }

    public function down(): void
    {
        Schema::table('village_infographic_items', function (Blueprint $table) {
            $table->dropIndex('village_infographic_items_category_sort_idx');
            $table->dropColumn('category');
        });
    }
};

