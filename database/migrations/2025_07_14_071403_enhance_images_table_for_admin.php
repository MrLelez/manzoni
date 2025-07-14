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
        Schema::table('images', function (Blueprint $table) {
            // Aggiungi solo indici per performance (se non esistono)
            try {
                $table->index(['type', 'status'], 'idx_images_type_status');
            } catch (\Exception $e) {
                // Index already exists, skip
            }
            
            try {
                $table->index(['is_optimized', 'file_size'], 'idx_images_optimization');
            } catch (\Exception $e) {
                // Index already exists, skip
            }
            
            try {
                $table->index('usage_count', 'idx_images_usage');
            } catch (\Exception $e) {
                // Index already exists, skip
            }
            
            try {
                $table->index('file_hash', 'idx_images_hash');
            } catch (\Exception $e) {
                // Index already exists, skip
            }
            
            try {
                $table->index('beauty_category', 'idx_images_beauty_category');
            } catch (\Exception $e) {
                // Index already exists, skip
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            try { $table->dropIndex('idx_images_type_status'); } catch (\Exception $e) {}
            try { $table->dropIndex('idx_images_optimization'); } catch (\Exception $e) {}
            try { $table->dropIndex('idx_images_usage'); } catch (\Exception $e) {}
            try { $table->dropIndex('idx_images_hash'); } catch (\Exception $e) {}
            try { $table->dropIndex('idx_images_beauty_category'); } catch (\Exception $e) {}
        });
    }
};