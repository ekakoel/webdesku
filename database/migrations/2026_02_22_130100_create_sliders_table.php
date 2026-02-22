<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->text('caption')->nullable();
            $table->string('cta_text')->nullable();
            $table->string('cta_url')->nullable();
            $table->string('image_path');
            $table->string('image_alt')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};
