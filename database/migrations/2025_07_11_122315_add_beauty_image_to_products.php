<?php
// database/migrations/2025_07_11_extend_image_types.php

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
        // ✨ SIMPLE: Just extend the image type enum to include 'beauty'
        // No need for extra foreign keys in products table
        
        Schema::table('images', function (Blueprint $table) {
            // Extend existing type enum to include 'beauty'
            $table->enum('type', [
                'primary',      // Main product images (legacy, now we use primary_image_id)
                'gallery',      // Regular gallery images  
                'beauty',       // ✨ NEW: Special images for design/marketing
                'product',      // Legacy product images
                'category',     // Category images
                'user_avatar',  // User avatars
                'content',      // Content images
                'temp'          // Temporary images
            ])->change();
        });
        
        // Optional: Update existing images to use new system
        // All existing 'product' type images become 'gallery'
        DB::table('images')
            ->where('type', 'product')
            ->update(['type' => 'gallery']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            // Revert to original enum (remove 'beauty')
            $table->enum('type', [
                'primary',
                'gallery', 
                'product',
                'category',
                'user_avatar',
                'content',
                'temp'
            ])->change();
        });
        
        // Convert any 'beauty' images back to 'product'
        DB::table('images')
            ->where('type', 'beauty')
            ->update(['type' => 'product']);
    }
};