<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->string('ticket_code', 30)->unique();
            $table->string('applicant_name');
            $table->string('nik', 16);
            $table->string('kk_number', 16)->nullable();
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->text('address');
            $table->text('description')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('status', 20)->default('diajukan');
            $table->text('status_note')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['service_id', 'status']);
            $table->index(['village_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
