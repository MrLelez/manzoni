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
            // Aggiungi solo i campi necessari per l'Advanced Image Gallery
            // mantenendo il tuo schema esistente
            
            // Campo per ordinamento drag & drop
            $table->integer('sort_order')->default(0)->after('tags');
            
            // Campo per designare immagine principale (gestito dal Product model)
            $table->boolean('is_primary')->default(false)->after('sort_order');
            
            // Campi per metadati avanzati gallery
            $table->string('caption')->nullable()->after('alt_text'); // Didascalia estesa
            $table->string('dominant_color', 7)->nullable()->after('caption'); // Colore dominante #RRGGBB
            
            // Campo per stato elaborazione/ottimizzazione
            $table->enum('processing_status', ['pending', 'processing', 'completed', 'failed'])
                  ->default('completed')->after('status');
            
            // Indici per performance gallery
            $table->index(['imageable_type', 'imageable_id', 'sort_order'], 'images_sortable_index');
            $table->index(['imageable_type', 'imageable_id', 'is_primary'], 'images_primary_index');
            $table->index(['type', 'processing_status'], 'images_processing_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            // Rimuovi gli indici
            $table->dropIndex('images_sortable_index');
            $table->dropIndex('images_primary_index');
            $table->dropIndex('images_processing_index');
            
            // Rimuovi i campi aggiunti
            $table->dropColumn([
                'sort_order',
                'is_primary',
                'caption',
                'dominant_color',
                'processing_status'
            ]);
        });
    }
};