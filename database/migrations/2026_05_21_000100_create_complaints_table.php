<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained()->cascadeOnDelete();
            $table->string('ticket_code', 30)->unique();
            $table->string('public_token', 40)->unique();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->string('category', 80)->default('umum');
            $table->string('title', 190);
            $table->text('description');
            $table->string('location')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('status', 20)->default('baru');
            $table->text('status_note')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['village_id', 'status']);
            $table->index(['village_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};

