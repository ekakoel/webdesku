<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('village_land_use_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('fiscal_year')->nullable();
            $table->string('label', 120);
            $table->decimal('area_value', 14, 2)->default(0);
            $table->string('unit', 20)->default('Ha');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['village_id', 'fiscal_year'], 'vlua_main_idx');
            $table->index(['is_published', 'sort_order'], 'vlua_publish_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('village_land_use_areas');
    }
};
