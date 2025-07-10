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
        Schema::table('users', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('email');
            $table->integer('level')->nullable()->after('company_name')->comment('Livello rivenditore 1-5');
            $table->string('phone')->nullable()->after('level');
            $table->text('address')->nullable()->after('phone');
            $table->string('vat_number')->nullable()->after('address');
            $table->boolean('is_active')->default(true)->after('vat_number');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            
            // Indici per performance
            $table->index(['level', 'is_active']);
            $table->index('vat_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'company_name',
                'level',
                'phone',
                'address',
                'vat_number',
                'is_active',
                'last_login_at'
            ]);
        });
    }
};