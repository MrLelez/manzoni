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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            
            // Identificazione categoria
            $table->string('name'); // Nome categoria (italiano di default)
            $table->string('slug')->unique(); // URL-friendly name
            $table->text('description')->nullable(); // Descrizione dettagliata
            $table->text('short_description')->nullable(); // Descrizione breve
            
            // Gerarchia (Self-referencing)
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade');
            
            // Visual & UX
            $table->string('icon')->nullable(); // CSS class o path icona
            $table->string('color', 7)->default('#3B82F6'); // Colore hex per UI
            $table->string('banner_image')->nullable(); // Path immagine banner
            $table->integer('sort_order')->default(0); // Ordinamento custom
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false); // Categoria in evidenza
            $table->boolean('show_in_menu')->default(true); // Mostra in navigazione
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('canonical_url')->nullable();
            
            // Business Logic
            $table->integer('min_products')->default(0); // Minimo prodotti per essere visibile
            $table->json('filters')->nullable(); // Filtri specifici per categoria
            
            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indices per performance
            $table->index(['is_active', 'sort_order']); // Categorie attive ordinate
            $table->index(['parent_id', 'sort_order']); // Sottocategorie ordinate
            $table->index('slug'); // Ricerca per slug
            $table->index(['is_featured', 'is_active']); // Categorie in evidenza
            $table->index('show_in_menu'); // Navigazione menu
            
            // Full-text search
            $table->fullText(['name', 'description']); // Ricerca testuale
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};