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
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            
            // Identificazione tag
            $table->string('name'); // Nome tag (italiano di default)
            $table->string('slug')->unique(); // URL-friendly name
            $table->text('description')->nullable(); // Descrizione tag
            
            // Categoria tag (per organizzazione)
            $table->enum('category', [
                'material',    // Materiali (acciaio-inox, ghisa, legno-teak)
                'finish',      // Finiture (verniciato, cromato, satinato)
                'function',    // Funzioni (fisso, removibile, con-anelli)
                'context',     // Contesto (esterni, urbano, parco, giardino)
                'sector',      // Settore (pubblico, privato, commerciale)
                'size',        // Dimensioni (piccolo, medio, grande)
                'feature',     // Caratteristiche (illuminato, smart, anti-vandalo)
                'style',       // Stile (moderno, classico, minimal)
                'color',       // Colore (rosso, blu, grigio)
                'brand',       // Brand/Serie (mediogulliver, lario, palo)
                'certification', // Certificazioni (CE, ISO, FSC)
                'other'        // Altri tag
            ])->default('other');
            
            // Visual & UX
            $table->string('color', 7)->nullable(); // Colore hex per UI (#FF0000)
            $table->string('icon')->nullable(); // CSS class o emoji per icona
            $table->string('image')->nullable(); // Immagine rappresentativa
            
            // Comportamento ricerca
            $table->integer('search_weight')->default(5); // Peso nella ricerca (1-10)
            $table->boolean('is_searchable')->default(true); // Usabile in ricerca
            $table->boolean('is_filterable')->default(true); // Usabile come filtro
            $table->boolean('is_featured')->default(false); // Tag in evidenza
            
            // Status
            $table->boolean('is_active')->default(true); // Tag attivo
            $table->boolean('auto_assign')->default(false); // Assegnazione automatica
            $table->json('auto_rules')->nullable(); // Regole assegnazione automatica
            
            // Organizzazione
            $table->integer('sort_order')->default(0); // Ordinamento
            $table->integer('usage_count')->default(0); // Contatore utilizzi
            $table->timestamp('last_used_at')->nullable(); // Ultimo utilizzo
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            
            // Sinonimi e traduzioni
            $table->json('synonyms')->nullable(); // Sinonimi del tag
            $table->json('related_terms')->nullable(); // Termini correlati
            
            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indices per performance
            $table->index(['is_active', 'search_weight']); // Tag attivi per ricerca
            $table->index(['category', 'is_active']); // Tag per categoria
            $table->index(['is_featured', 'sort_order']); // Tag in evidenza
            $table->index(['is_filterable', 'category']); // Tag filtrabili per categoria
            $table->index('usage_count'); // Tag più usati
            $table->index(['last_used_at', 'is_active']); // Tag utilizzati di recente
            
            // Full-text search
            $table->fullText(['name', 'description']); // Ricerca testuale
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};