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
            // Foreign key per l'immagine principale
            $table->unsignedBigInteger('primary_image_id')->nullable()->after('status');
            
            // Indice per performance
            $table->index('primary_image_id');
            
            // Foreign key constraint (opzionale, da valutare)
            // $table->foreign('primary_image_id')->references('id')->on('images')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // $table->dropForeign(['primary_image_id']); // Se abbiamo la foreign key
            $table->dropIndex(['primary_image_id']);
            $table->dropColumn('primary_image_id');
        });
    }
};