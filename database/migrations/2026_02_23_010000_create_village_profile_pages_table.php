<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('village_profile_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained()->cascadeOnDelete();
            $table->string('slug', 40);
            $table->string('title');
            $table->text('subtitle')->nullable();
            $table->longText('content')->nullable();
            $table->string('source_url', 2000)->nullable();
            $table->json('payload')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->unique(['village_id', 'slug']);
            $table->index(['slug', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('village_profile_pages');
    }
};

