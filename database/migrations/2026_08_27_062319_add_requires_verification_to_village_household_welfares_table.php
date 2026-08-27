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
        Schema::table('village_household_welfares', function (Blueprint $table) {
            $table->boolean('requires_verification')->default(false)->after('is_outside_village');
            $table->index(['village_id', 'year', 'requires_verification'], 'village_household_welfare_verification_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('village_household_welfares', function (Blueprint $table) {
            $table->dropIndex('village_household_welfare_verification_idx');
            $table->dropColumn('requires_verification');
        });
    }
};
