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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            
            // Identificazione prodotto
            $table->string('name'); // Nome prodotto (italiano di default)
            $table->string('slug')->unique(); // URL-friendly name
            $table->string('sku')->unique(); // Codice Manzoni (es: MG-001, MGP-001)
            $table->string('sku_base')->nullable(); // SKU base per varianti
            $table->string('model')->nullable(); // Modello/famiglia (es: mediogulliver)
            
            // Descrizioni
            $table->text('short_description')->nullable(); // Descrizione breve
            $table->longText('description')->nullable(); // Descrizione completa
            $table->json('highlights')->nullable(); // Punti salienti (array)
            
            // Tipologia prodotto
            $table->enum('type', ['simple', 'variable', 'accessory'])->default('simple');
            // simple: prodotto singolo
            // variable: prodotto con varianti
            // accessory: accessorio
            
            // Categoria principale
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            
            // Pricing
            $table->decimal('base_price', 10, 2)->default(0); // Prezzo base
            $table->decimal('cost_price', 10, 2)->nullable(); // Prezzo di costo (solo admin)
            $table->string('currency', 3)->default('EUR'); // Valuta
            $table->boolean('show_price')->default(true); // Mostra prezzo pubblico
            $table->boolean('price_on_request')->default(false); // Prezzo su richiesta
            
            // Status
            $table->enum('status', ['draft', 'active', 'discontinued', 'coming_soon'])->default('draft');
            $table->boolean('is_featured')->default(false); // Prodotto in evidenza
            $table->boolean('requires_login')->default(false); // Visibile solo a utenti registrati
            $table->boolean('is_customizable')->default(false); // Prodotto personalizzabile
            
            // Inventory (opzionale per arredo urbano)
            $table->integer('stock_quantity')->nullable(); // Quantità disponibile
            $table->boolean('track_stock')->default(false); // Traccia inventario
            $table->enum('stock_status', ['in_stock', 'out_of_stock', 'on_backorder'])->default('in_stock');
            $table->integer('low_stock_threshold')->nullable(); // Soglia scorte basse
            
            // Attributi fisici
            $table->decimal('weight', 8, 2)->nullable(); // Peso in kg
            $table->decimal('length', 8, 2)->nullable(); // Lunghezza in cm
            $table->decimal('width', 8, 2)->nullable(); // Larghezza in cm
            $table->decimal('height', 8, 2)->nullable(); // Altezza in cm
            $table->string('material')->nullable(); // Materiale principale
            $table->string('finish')->nullable(); // Finitura
            $table->string('color')->nullable(); // Colore principale
            
            // Specifiche tecniche (JSON flessibile)
            $table->json('specifications')->nullable(); // Specifiche dettagliate
            $table->json('certifications')->nullable(); // Certificazioni (CE, ISO, etc.)
            $table->json('features')->nullable(); // Caratteristiche speciali
            
            // Files e documenti
            $table->string('datasheet_pdf')->nullable(); // Scheda tecnica PDF
            $table->string('installation_guide')->nullable(); // Guida installazione
            $table->string('manual_pdf')->nullable(); // Manuale d'uso
            $table->string('warranty_info')->nullable(); // Info garanzia
            $table->json('downloads')->nullable(); // Altri download (BIM, CAD, etc.)
            
            // Business logic
            $table->integer('min_order_quantity')->default(1); // Quantità minima ordine
            $table->integer('max_order_quantity')->nullable(); // Quantità massima ordine
            $table->integer('lead_time_days')->nullable(); // Giorni di consegna
            $table->boolean('shipping_required')->default(true); // Richiede spedizione
            $table->decimal('shipping_weight', 8, 2)->nullable(); // Peso per spedizione
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('canonical_url')->nullable();
            $table->json('structured_data')->nullable(); // Schema.org markup
            
            // Disponibilità temporale
            $table->timestamp('available_from')->nullable(); // Disponibile da
            $table->timestamp('available_until')->nullable(); // Disponibile fino a
            
            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes(); // Soft delete per recupero
            
            // Indices per performance
            $table->index(['status', 'is_featured']); // Prodotti attivi in evidenza
            $table->index(['category_id', 'status']); // Prodotti per categoria
            $table->index(['type', 'status']); // Prodotti per tipo
            $table->index('sku'); // Ricerca per SKU
            $table->index('model'); // Ricerca per modello/famiglia
            $table->index(['requires_login', 'status']); // Prodotti pubblici/privati
            $table->index(['stock_status', 'track_stock']); // Gestione inventario
            $table->index(['available_from', 'available_until']); // Disponibilità temporale
            
            // Full-text search
            $table->fullText(['name', 'short_description', 'description']); // Ricerca testuale
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};