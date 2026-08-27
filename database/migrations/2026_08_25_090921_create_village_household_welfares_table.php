<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('village_household_welfares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained()->cascadeOnDelete();
            $table->foreignId('village_hamlet_id')->nullable()->constrained('village_hamlets')->nullOnDelete();
            $table->unsignedSmallInteger('year');
            $table->string('reference_code', 80);
            $table->enum('decile', ['D1', 'D2', 'D3', 'D4', 'D5'])->nullable();
            $table->enum('head_gender', ['laki_laki', 'perempuan'])->nullable();
            $table->boolean('is_outside_village')->default(false);
            $table->string('source', 120)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->unique(['village_id', 'year', 'reference_code']);
            $table->index(['village_id', 'year', 'is_published']);
            $table->index(['village_hamlet_id', 'year', 'decile']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('village_household_welfares');
    }
};
