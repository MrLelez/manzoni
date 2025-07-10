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
        Schema::create('locales', function (Blueprint $table) {
            $table->id();
            
            // Identificazione lingua
            $table->string('code', 5)->unique(); // 'it', 'fr', 'de', 'en', 'en-US', 'pt-BR'
            $table->string('name', 50); // 'Italiano', 'Français', 'Deutsch'
            $table->string('native_name', 50); // Nome nella lingua nativa
            $table->string('flag', 10)->nullable(); // Codice flag per icone
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0);
            
            // Formattazione locale
            $table->string('date_format', 20)->default('d/m/Y'); // Formato data
            $table->string('time_format', 20)->default('H:i'); // Formato ora
            $table->string('datetime_format', 30)->default('d/m/Y H:i'); // Formato data-ora
            
            // Valuta
            $table->string('currency_code', 3)->default('EUR'); // 'EUR', 'USD', 'GBP'
            $table->string('currency_symbol', 5)->default('€'); // '€', '$', '£'
            $table->string('currency_position', 10)->default('before'); // 'before', 'after'
            
            // Formattazione numeri
            $table->string('decimal_separator', 1)->default(','); // ',' o '.'
            $table->string('thousands_separator', 1)->default('.'); // '.' o ','
            $table->integer('decimal_places')->default(2); // Decimali per prezzi
            
            // Metadata
            $table->string('direction', 3)->default('ltr'); // 'ltr', 'rtl'
            $table->string('charset', 20)->default('UTF-8');
            
            $table->timestamps();
            
            // Indices
            $table->index(['is_active', 'sort_order']);
            $table->index('is_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locales');
    }
};