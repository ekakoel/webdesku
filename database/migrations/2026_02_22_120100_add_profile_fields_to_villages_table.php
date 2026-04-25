<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('villages', function (Blueprint $table) {
            $table->string('address')->nullable()->after('head_name');
            $table->string('phone')->nullable()->after('address');
            $table->string('email')->nullable()->after('phone');
            $table->string('website')->nullable()->after('email');
            $table->string('postal_code', 20)->nullable()->after('website');
            $table->string('district')->nullable()->after('postal_code');
            $table->string('city')->nullable()->after('district');
            $table->string('province')->nullable()->after('city');
            $table->string('country')->nullable()->after('province');
            $table->decimal('area_km2', 8, 2)->nullable()->after('country');
            $table->unsignedInteger('population')->nullable()->after('area_km2');
            $table->unsignedInteger('households')->nullable()->after('population');
            $table->unsignedInteger('rt_count')->nullable()->after('households');
            $table->unsignedInteger('rw_count')->nullable()->after('rt_count');
            $table->text('history')->nullable()->after('rw_count');
            $table->text('vision')->nullable()->after('history');
            $table->longText('mission')->nullable()->after('vision');
            $table->text('head_greeting')->nullable()->after('mission');
            $table->json('quick_info')->nullable()->after('head_greeting');
            $table->unsignedBigInteger('apb_income')->nullable()->after('quick_info');
            $table->unsignedBigInteger('apb_expense')->nullable()->after('apb_income');
            $table->unsignedBigInteger('apb_financing')->nullable()->after('apb_expense');
        });
    }

    public function down(): void
    {
        Schema::table('villages', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'phone',
                'email',
                'website',
                'postal_code',
                'district',
                'city',
                'province',
                'country',
                'area_km2',
                'population',
                'households',
                'rt_count',
                'rw_count',
                'history',
                'vision',
                'mission',
                'head_greeting',
                'quick_info',
                'apb_income',
                'apb_expense',
                'apb_financing',
            ]);
        });
    }
};
