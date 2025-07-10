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
        Schema::create('product_accessories', function (Blueprint $table) {
            $table->id();
            
            // Relazioni (bidirezionale)
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('accessory_id')->constrained('products')->onDelete('cascade');
            
            // Tipo di relazione accessorio
            $table->enum('relationship_type', [
                'required',     // Accessorio obbligatorio
                'optional',     // Accessorio opzionale
                'recommended',  // Accessorio consigliato
                'alternative',  // Accessorio alternativo
                'upgrade',      // Upgrade/potenziamento
                'replacement',  // Pezzo di ricambio
                'maintenance'   // Manutenzione
            ])->default('optional');
            
            // Compatibilità
            $table->enum('compatibility_type', [
                'universal',    // Compatibile con tutte le varianti
                'conditional',  // Compatibile con alcune varianti (regole)
                'exclusive',    // Compatibile solo con varianti specifiche
                'incompatible'  // Non compatibile (per esclusioni)
            ])->default('universal');
            
            // Regole di compatibilità (JSON)
            $table->json('compatibility_rules')->nullable();
            // Esempio: {"installation": ["removibile"], "rings": ["con_anelli"]}
            
            // Regole di incompatibilità
            $table->json('incompatibility_rules')->nullable();
            // Esempio: {"installation": ["fisso"], "material": ["legno"]}
            
            // Quantità
            $table->integer('quantity_min')->default(1); // Quantità minima
            $table->integer('quantity_max')->nullable(); // Quantità massima
            $table->integer('quantity_default')->default(1); // Quantità default
            $table->boolean('quantity_fixed')->default(false); // Quantità fissa
            
            // Pricing
            $table->decimal('bundle_discount_percentage', 5, 2)->default(0); // Sconto bundle
            $table->decimal('bundle_discount_amount', 10, 2)->default(0); // Sconto fisso
            $table->boolean('free_with_main')->default(false); // Gratuito con prodotto principale
            
            // Business logic
            $table->boolean('auto_add_to_cart')->default(false); // Aggiungi automaticamente
            $table->boolean('show_in_product_page')->default(true); // Mostra nella pagina prodotto
            $table->boolean('show_in_cart')->default(true); // Mostra nel carrello
            $table->boolean('show_in_checkout')->default(true); // Mostra nel checkout
            
            // Presentazione
            $table->string('display_name')->nullable(); // Nome da mostrare
            $table->text('description')->nullable(); // Descrizione relazione
            $table->string('badge_text')->nullable(); // Badge (es: "Consigliato", "Necessario")
            $table->string('badge_color')->nullable(); // Colore badge
            
            // Organizzazione
            $table->integer('sort_order')->default(0); // Ordinamento
            $table->integer('priority')->default(0); // Priorità visualizzazione
            $table->string('group_name')->nullable(); // Gruppo accessori
            
            // Status
            $table->boolean('is_active')->default(true); // Relazione attiva
            $table->boolean('is_featured')->default(false); // Accessorio in evidenza
            $table->timestamp('valid_from')->nullable(); // Valido da
            $table->timestamp('valid_until')->nullable(); // Valido fino a
            
            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indices per performance
            $table->index(['product_id', 'is_active']); // Accessori attivi per prodotto
            $table->index(['accessory_id', 'is_active']); // Prodotti che usano accessorio
            $table->index(['relationship_type', 'is_active']); // Accessori per tipo
            $table->index(['compatibility_type', 'is_active']); // Compatibilità
            $table->index(['product_id', 'sort_order']); // Ordinamento accessori
            $table->index(['is_featured', 'priority']); // Accessori in evidenza
            $table->index(['valid_from', 'valid_until']); // Validità temporale
            $table->index(['group_name', 'sort_order']); // Gruppi di accessori
            
            // Unique constraint: una relazione per coppia prodotto-accessorio
            $table->unique(['product_id', 'accessory_id'], 'unique_product_accessory');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_accessories');
    }
};