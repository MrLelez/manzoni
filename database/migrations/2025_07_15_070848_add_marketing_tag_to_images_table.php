<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMarketingTagToImagesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('images', function (Blueprint $table) {
            // ✨ NUOVO: Tag marketing per beauty
            $table->boolean('is_marketing')->default(false)->after('beauty_category');
            
            // Campi marketing (solo quando is_marketing = true)
            $table->enum('marketing_category', [
                'hero', 'banner', 'social', 'brochure', 'presentation', 
                'web', 'press', 'advertising', 'events', 'catalog'
            ])->nullable()->after('is_marketing');
            
            $table->string('campaign_name')->nullable()->after('marketing_category');
            $table->text('usage_rights')->nullable()->after('campaign_name');
            
            // Indici per performance
            $table->index(['type', 'is_marketing']);
            $table->index(['is_marketing', 'marketing_category']);
            $table->index(['campaign_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropIndex(['type', 'is_marketing']);
            $table->dropIndex(['is_marketing', 'marketing_category']);
            $table->dropIndex(['campaign_name']);
            
            $table->dropColumn([
                'is_marketing',
                'marketing_category',
                'campaign_name', 
                'usage_rights'
            ]);
        });
    }
}