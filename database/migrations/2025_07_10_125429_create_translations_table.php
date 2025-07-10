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
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            
            // Polymorphic relation - può tradurre qualsiasi model
            $table->string('translatable_type'); // 'App\Models\Product', 'App\Models\Category', etc.
            $table->unsignedBigInteger('translatable_id'); // ID del model da tradurre
            
            // Identificazione traduzione
            $table->string('locale', 5); // 'it', 'fr', 'de', 'en', etc.
            $table->string('field', 50); // 'name', 'description', 'short_description', etc.
            $table->text('value'); // Contenuto tradotto
            
            // Metadata
            $table->boolean('is_active')->default(true);
            $table->boolean('is_machine_translated')->default(false); // Se tradotta automaticamente
            $table->boolean('needs_review')->default(false); // Se richiede revisione
            
            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Indices per performance
            $table->index(['translatable_type', 'translatable_id', 'locale']); // Query principali
            $table->index(['locale', 'field']); // Ricerca per lingua e campo
            $table->index(['is_active', 'locale']); // Traduzioni attive per lingua
            $table->index(['translatable_type', 'field']); // Traduzioni per tipo e campo
            
            // Unique constraint: una traduzione per model/campo/lingua
            $table->unique(['translatable_type', 'translatable_id', 'locale', 'field'], 'unique_translation');
            
            // Foreign key polymorphic (commented perché polymorphic)
            // $table->foreign(['translatable_type', 'translatable_id'])->references(['type', 'id'])->on('models');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};