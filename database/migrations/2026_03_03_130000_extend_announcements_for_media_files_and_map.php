<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('announcements')) {
            Schema::table('announcements', function (Blueprint $table) {
                if (!Schema::hasColumn('announcements', 'location_name')) {
                    $table->string('location_name')->nullable()->after('reference_url');
                }
                if (!Schema::hasColumn('announcements', 'latitude')) {
                    $table->decimal('latitude', 10, 7)->nullable()->after('location_name');
                }
                if (!Schema::hasColumn('announcements', 'longitude')) {
                    $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
                }
                if (!Schema::hasColumn('announcements', 'map_url')) {
                    $table->text('map_url')->nullable()->after('longitude');
                }
                if (!Schema::hasColumn('announcements', 'attachment_path')) {
                    $table->string('attachment_path')->nullable()->after('map_url');
                }
                if (!Schema::hasColumn('announcements', 'attachment_name')) {
                    $table->string('attachment_name')->nullable()->after('attachment_path');
                }
            });
        }

        if (!Schema::hasTable('announcement_images')) {
            Schema::create('announcement_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('announcement_id')->constrained('announcements')->cascadeOnDelete();
                $table->string('image_path');
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();

                $table->index(['announcement_id', 'sort_order'], 'announcement_images_sort_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_images');

        if (Schema::hasTable('announcements')) {
            Schema::table('announcements', function (Blueprint $table) {
                $dropCols = [];
                foreach (['location_name', 'latitude', 'longitude', 'map_url', 'attachment_path', 'attachment_name'] as $col) {
                    if (Schema::hasColumn('announcements', $col)) {
                        $dropCols[] = $col;
                    }
                }
                if ($dropCols !== []) {
                    $table->dropColumn($dropCols);
                }
            });
        }
    }
};

