<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('village_population_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->string('category', 40);
            $table->string('label', 120);
            $table->unsignedInteger('value')->default(0);
            $table->string('unit', 30)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['village_id', 'year', 'category'], 'village_population_stats_year_category_idx');
            $table->index(['is_published', 'sort_order'], 'village_population_stats_publish_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('village_population_stats');
    }
};

