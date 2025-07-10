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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            
            // Relazione al prodotto principale
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            
            // Identificazione variante
            $table->string('sku')->unique(); // SKU completo (es: MG-001-AC, MG-002-SA)
            $table->string('name'); // Nome variante (es: "Fisso con anelli")
            $table->string('slug'); // URL-friendly name
            $table->text('description')->nullable(); // Descrizione specifica variante
            
            // Attributi variante (JSON flessibile per Manzoni)
            $table->json('attributes'); // {installation: "fisso", rings: "con_anelli"}
            $table->string('attribute_hash')->nullable(); // Hash per ricerca veloce
            
            // Pricing
            $table->decimal('price_modifier', 10, 2)->default(0); // Modificatore prezzo (+50, -20)
            $table->decimal('final_price', 10, 2)->nullable(); // Prezzo finale calcolato
            $table->boolean('override_base_price')->default(false); // Sovrascrive prezzo base
            
            // Attributi fisici specifici
            $table->decimal('weight_modifier', 8, 2)->default(0); // Modificatore peso
            $table->decimal('length_modifier', 8, 2)->default(0); // Modificatore lunghezza
            $table->decimal('width_modifier', 8, 2)->default(0); // Modificatore larghezza
            $table->decimal('height_modifier', 8, 2)->default(0); // Modificatore altezza
            
            // Inventory specifico per variante
            $table->integer('stock_quantity')->default(0); // Stock specifico
            $table->integer('reserved_quantity')->default(0); // Quantità riservata
            $table->boolean('track_stock')->default(true); // Traccia stock per variante
            $table->enum('stock_status', ['in_stock', 'out_of_stock', 'on_backorder'])->default('in_stock');
            
            // Visual
            $table->foreignId('image_id')->nullable()->constrained('images')->onDelete('set null');
            $table->json('gallery_images')->nullable(); // Array di image_id per gallery
            $table->string('color_hex')->nullable(); // Colore esadecimale per UI
            
            // Business logic
            $table->boolean('is_default')->default(false); // Variante di default
            $table->boolean('is_active')->default(true); // Variante attiva
            $table->integer('sort_order')->default(0); // Ordinamento
            $table->integer('min_order_quantity')->default(1); // Quantità minima
            $table->integer('max_order_quantity')->nullable(); // Quantità massima
            
            // Lead time specifico
            $table->integer('lead_time_days')->nullable(); // Giorni consegna specifici
            $table->boolean('made_to_order')->default(false); // Prodotto su ordine
            
            // Disponibilità
            $table->timestamp('available_from')->nullable();
            $table->timestamp('available_until')->nullable();
            
            // Audit
            $table->timestamps();
            
            // Indices per performance
            $table->index(['product_id', 'is_active']); // Varianti attive per prodotto
            $table->index(['product_id', 'is_default']); // Variante default
            $table->index(['product_id', 'sort_order']); // Ordinamento varianti
            $table->index('sku'); // Ricerca per SKU
            $table->index('attribute_hash'); // Ricerca per combinazione attributi
            $table->index(['stock_status', 'track_stock']); // Gestione inventario
            $table->index(['is_active', 'available_from', 'available_until']); // Disponibilità
            
            // Unique constraint: un solo default per prodotto
            $table->unique(['product_id', 'is_default'], 'unique_default_variant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};