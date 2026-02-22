<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('village_populations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedInteger('male')->default(0);
            $table->unsignedInteger('female')->default(0);
            $table->unsignedInteger('households')->default(0);
            $table->text('notes')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->unique(['village_id', 'year']);
            $table->index(['is_published', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('village_populations');
    }
};

