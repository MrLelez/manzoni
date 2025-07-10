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
        Schema::create('product_pricing', function (Blueprint $table) {
            $table->id();
            
            // Relazione prodotto
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->onDelete('cascade');
            
            // Livello utente (sistema esistente Manzoni)
            $table->integer('user_level'); // 1-5 (livelli rivenditori)
            // 1 = 5% sconto, 2 = 10%, 3 = 15%, 4 = 20%, 5 = 25%
            
            // Pricing
            $table->decimal('price', 10, 2); // Prezzo finale per questo livello
            $table->decimal('discount_percentage', 5, 2)->default(0); // Percentuale sconto
            $table->decimal('discount_amount', 10, 2)->default(0); // Sconto fisso
            $table->string('currency', 3)->default('EUR'); // Valuta
            
            // Quantità (per prezzi a scaglioni)
            $table->integer('min_quantity')->default(1); // Quantità minima
            $table->integer('max_quantity')->nullable(); // Quantità massima
            
            // Condizioni speciali
            $table->enum('pricing_type', [
                'fixed',        // Prezzo fisso
                'percentage',   // Sconto percentuale
                'amount',       // Sconto in valore assoluto
                'formula',      // Formula personalizzata
                'tier'          // Prezzo a scaglioni
            ])->default('percentage');
            
            // Validità temporale
            $table->timestamp('valid_from')->nullable(); // Valido da
            $table->timestamp('valid_until')->nullable(); // Valido fino a
            
            // Condizioni aggiuntive
            $table->json('conditions')->nullable(); // Condizioni extra
            $table->text('notes')->nullable(); // Note private
            
            // Geolocalizzazione
            $table->string('country_code', 2)->nullable(); // Codice paese (IT, FR, DE)
            $table->string('region')->nullable(); // Regione specifica
            
            // Business logic
            $table->boolean('is_active')->default(true); // Pricing attivo
            $table->boolean('is_public')->default(false); // Visibile pubblicamente
            $table->boolean('requires_approval')->default(false); // Richiede approvazione
            $table->boolean('is_promotional')->default(false); // Prezzo promozionale
            
            // Organizzazione
            $table->integer('priority')->default(0); // Priorità applicazione
            $table->string('label')->nullable(); // Etichetta (es: "Promo Natale")
            
            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            // Indices per performance
            $table->index(['product_id', 'user_level']); // Prezzo per prodotto e livello
            $table->index(['product_variant_id', 'user_level']); // Prezzo per variante e livello
            $table->index(['user_level', 'is_active']); // Prezzi attivi per livello
            $table->index(['valid_from', 'valid_until']); // Validità temporale
            $table->index(['is_active', 'priority']); // Pricing attivi per priorità
            $table->index(['country_code', 'is_active']); // Pricing per paese
            $table->index(['is_promotional', 'valid_until']); // Promozioni
            $table->index(['min_quantity', 'max_quantity']); // Scaglioni quantità
            
            // Unique constraint: un prezzo per prodotto/variante/livello/quantità
            $table->unique([
                'product_id', 'product_variant_id', 'user_level', 'min_quantity'
            ], 'unique_pricing_rule');
            
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_pricing');
    }
};