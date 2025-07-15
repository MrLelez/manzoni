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
            // Campi che mancano esattamente dal tuo errore SQL
            if (!Schema::hasColumn('products', 'dimensions')) {
                $table->string('dimensions', 255)->nullable()->after('weight');
            }
            if (!Schema::hasColumn('products', 'installation_type')) {
                $table->string('installation_type', 50)->nullable()->after('dimensions');
            }
            if (!Schema::hasColumn('products', 'warranty_years')) {
                $table->integer('warranty_years')->nullable()->after('installation_type');
            }
            if (!Schema::hasColumn('products', 'technical_specs')) {
                $table->text('technical_specs')->nullable()->after('warranty_years');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'dimensions',
                'installation_type', 
                'warranty_years',
                'technical_specs'
            ]);
        });
    }
};