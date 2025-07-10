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
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            
            // IDENTIFICATIVI
            $table->string('clean_name')->unique(); // Nome pulito: "cestino-roma-blue"
            $table->string('original_filename'); // Nome file originale
            $table->string('aws_key'); // Chiave AWS S3 completa
            $table->string('aws_url'); // URL AWS sporco completo
            
            // METADATI FILE
            $table->string('mime_type'); // image/jpeg, image/png, etc.
            $table->unsignedBigInteger('file_size'); // Bytes
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('hash_md5'); // Per controllo duplicati
            
            // CATEGORIZZAZIONE
            $table->enum('type', ['product', 'category', 'user_avatar', 'content', 'temp'])->default('product');
            $table->string('alt_text')->nullable();
            $table->json('tags')->nullable(); // Array di tag per ricerca
            
            // RELAZIONI OPZIONALI (polymorphic) - Laravel crea automaticamente l'indice
            $table->nullableMorphs('imageable'); // product_id, category_id, etc.
            
            // STATUS E SICUREZZA
            $table->enum('status', ['active', 'processing', 'failed', 'deleted'])->default('processing');
            $table->boolean('is_public')->default(true);
            $table->timestamp('processed_at')->nullable();
            
            // VARIANTI (thumbnails, etc.)
            $table->json('variants')->nullable(); // Array di varianti generate
            
            // AUDIT
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes(); // Soft delete per recupero
            
            // INDICES per performance (RIMOSSO quello duplicato)
            $table->index('clean_name'); // Per URL routing veloce
            $table->index(['type', 'status']);
            $table->index('hash_md5'); // Per controllo duplicati
            $table->index(['status', 'is_public']);
            // RIMOSSO: $table->index(['imageable_type', 'imageable_id']); perché Laravel lo crea automaticamente con nullableMorphs()
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};