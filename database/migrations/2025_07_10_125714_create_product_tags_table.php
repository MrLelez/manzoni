<?php
// Sostituisci il contenuto della migrazione product_tags esistente con questo:

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
        Schema::create('product_tags', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys per la relazione many-to-many
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('tag_id')->constrained('tags')->onDelete('cascade');
            
            $table->timestamps();
            
            // Indici per performance
            $table->index(['product_id', 'tag_id']);
            $table->index(['tag_id', 'product_id']);
            
            // Evitare tag duplicati per lo stesso prodotto
            $table->unique(['product_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_tags');
    }
};