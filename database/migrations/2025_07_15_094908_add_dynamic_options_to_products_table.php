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
        Schema::table('products', function (Blueprint $table) {
            // Aggiungi flags per gestire opzioni dinamiche
            $table->boolean('has_material_options')->default(false);
            $table->boolean('has_color_options')->default(false);
            $table->boolean('has_finish_options')->default(false);
            
            // Aggiungi riferimenti ai tag di default (mantieni i vecchi campi per compatibilità)
            $table->foreignId('default_material_tag_id')->nullable();
            $table->foreignId('default_color_tag_id')->nullable();
            $table->foreignId('default_finish_tag_id')->nullable();
            
            // Aggiungi foreign key constraints
            $table->foreign('default_material_tag_id')->references('id')->on('tags')->onDelete('set null');
            $table->foreign('default_color_tag_id')->references('id')->on('tags')->onDelete('set null');
            $table->foreign('default_finish_tag_id')->references('id')->on('tags')->onDelete('set null');
            
            // Aggiungi indici per performance
            $table->index(['has_material_options', 'is_active']);
            $table->index(['has_color_options', 'is_active']);
            $table->index(['has_finish_options', 'is_active']);
            
            // NOTA: Manteniamo i vecchi campi material, finish, color per compatibilità
            // Potranno essere rimossi in futuro quando tutto sarà migrato ai tag
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Rimuovi foreign key constraints
            $table->dropForeign(['default_material_tag_id']);
            $table->dropForeign(['default_color_tag_id']);
            $table->dropForeign(['default_finish_tag_id']);
            
            // Rimuovi indici
            $table->dropIndex(['has_material_options', 'is_active']);
            $table->dropIndex(['has_color_options', 'is_active']);
            $table->dropIndex(['has_finish_options', 'is_active']);
            
            // Rimuovi colonne
            $table->dropColumn([
                'has_material_options',
                'has_color_options',
                'has_finish_options',
                'default_material_tag_id',
                'default_color_tag_id',
                'default_finish_tag_id'
            ]);
        });
    }
};