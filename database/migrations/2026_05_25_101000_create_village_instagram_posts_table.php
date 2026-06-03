<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('village_instagram_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained()->cascadeOnDelete();
            $table->string('instagram_post_id')->index();
            $table->string('media_type', 30)->nullable();
            $table->text('caption')->nullable();
            $table->string('media_url')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->string('permalink')->nullable();
            $table->timestamp('posted_at')->nullable()->index();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->unique(['village_id', 'instagram_post_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('village_instagram_posts');
    }
};
