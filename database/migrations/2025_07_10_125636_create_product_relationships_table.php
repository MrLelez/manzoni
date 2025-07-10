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
        Schema::create('product_relationships', function (Blueprint $table) {
            $table->id();
            
            // Relazione tra prodotti
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('related_product_id')->constrained('products')->onDelete('cascade');
            
            // Tipo di relazione
            $table->enum('relationship_type', [
                'family',      // Stessa famiglia (Mediogulliver, Lario, Palo)
                'brother',     // Prodotti fratelli (Mediogulliver / Mediogulliver Pesante)
                'similar',     // Prodotti simili
                'upgrade',     // Versione superiore
                'downgrade',   // Versione inferiore
                'alternative', // Alternativa
                'complement',  // Complementare
                'accessory',   // Accessorio (diverso da product_accessories)
                'replacement', // Ricambio/sostituzione
                'cross_sell',  // Cross-selling
                'upsell'       // Up-selling
            ])->default('similar');
            
            // Metadati relazione
            $table->string('display_name')->nullable(); // Nome da mostrare (es: "Versione Pesante")
            $table->text('description')->nullable(); // Descrizione relazione
            $table->integer('relationship_strength')->default(5); // Forza relazione (1-10)
            
            // Business logic
            $table->boolean('is_bidirectional')->default(false); // Relazione bidirezionale
            $table->boolean('is_active')->default(true); // Relazione attiva
            $table->boolean('show_in_frontend')->default(true); // Mostra nel frontend
            $table->boolean('show_in_admin')->default(true); // Mostra nell'admin
            
            // Ordinamento e priorità
            $table->integer('sort_order')->default(0); // Ordinamento visualizzazione
            $table->integer('priority')->default(0); // Priorità (maggiore = più importante)
            
            // Context specifico
            $table->json('context_rules')->nullable(); // Regole per quando mostrare
            $table->string('display_context')->nullable(); // Dove mostrare (product_page, cart, etc.)
            
            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indices per performance
            $table->index(['product_id', 'relationship_type'], 'pr_prod_rel_type');
            $table->index(['product_id', 'is_active', 'show_in_frontend'], 'pr_prod_active_frontend');
            $table->index(['related_product_id', 'is_active'], 'pr_related_active');
            $table->index(['relationship_type', 'is_active'], 'pr_rel_type_active');
            $table->index(['product_id', 'sort_order'], 'pr_prod_sort');
            $table->index(['priority', 'relationship_strength'], 'pr_priority_strength');
            
            // Unique constraint: una relazione per coppia prodotti
            $table->unique(['product_id', 'related_product_id'], 'unique_product_relationship');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_relationships');
    }
};