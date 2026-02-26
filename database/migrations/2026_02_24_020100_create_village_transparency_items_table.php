<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('village_transparency_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('fiscal_year')->nullable();
            $table->string('category', 40)->default('laporan');
            $table->string('title', 255);
            $table->unsignedBigInteger('amount')->nullable();
            $table->text('description')->nullable();
            $table->string('document_url', 2000)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['village_id', 'fiscal_year', 'category'], 'village_transparency_items_main_idx');
            $table->index(['is_published', 'sort_order'], 'village_transparency_items_publish_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('village_transparency_items');
    }
};

