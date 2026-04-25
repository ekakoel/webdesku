<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('villages', function (Blueprint $table) {
            $table->unsignedInteger('population_male')->nullable()->after('population');
            $table->unsignedInteger('population_female')->nullable()->after('population_male');
        });
    }

    public function down(): void
    {
        Schema::table('villages', function (Blueprint $table) {
            $table->dropColumn(['population_male', 'population_female']);
        });
    }
};

