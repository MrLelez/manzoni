<?php
// database/migrations/xxxx_xx_xx_add_beauty_category_to_images_table.php

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
        Schema::table('images', function (Blueprint $table) {
            $table->string('beauty_category')->nullable()->after('type');
            $table->index(['imageable_type', 'imageable_id', 'beauty_category'], 'images_beauty_category_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropIndex('images_beauty_category_index');
            $table->dropColumn('beauty_category');
        });
    }
};