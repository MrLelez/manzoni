<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ====================================
        // CREATE PERMISSIONS
        // ====================================
        
        $permissions = [
            // 👑 ADMIN PERMISSIONS
            'manage-users',
            'manage-products',
            'manage-categories',     // ✨ NUOVO
            'manage-tags',           // ✨ NUOVO  
            'manage-images',         // ✨ NUOVO
            'manage-orders',
            'manage-levels',
            'view-analytics',
            'manage-settings',
            'export-data',
            
            // 🛍️ RIVENDITORE PERMISSIONS
            'view-pricing',
            'view-images',           // ✨ Possono vedere le immagini
            'place-orders',
            'view-order-history',
            'download-invoices',
            'manage-profile',
            
            // 📱 AGENTE PERMISSIONS
            'view-catalog',
            'view-images',           // ✨ Possono vedere le immagini
            'download-specs',
            'view-technical-sheets',
            'sync-offline-data',
            'access-mobile-tools',
            
            // 🌐 SHARED PERMISSIONS
            'view-products',
            'search-products',
            'contact-support',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ====================================
        // CREATE ROLES AND ASSIGN PERMISSIONS
        // ====================================
        
        // 👑 ADMIN ROLE - Controllo totale
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all()); // Admin ha tutti i permessi

        // 🛍️ RIVENDITORE ROLE - Ecommerce con livelli 1-5
        $rivenditoreRole = Role::firstOrCreate(['name' => 'rivenditore']);
        $rivenditoreRole->syncPermissions([
            'view-products',
            'search-products',
            'view-pricing',
            'view-images',           // ✨ Possono vedere le immagini prodotti
            'place-orders',
            'view-order-history',
            'download-invoices',
            'manage-profile',
            'contact-support',
        ]);

        // 📱 AGENTE ROLE - Catalogo mobile
        $agenteRole = Role::firstOrCreate(['name' => 'agente']);
        $agenteRole->syncPermissions([
            'view-products',
            'search-products',
            'view-catalog',
            'view-images',           // ✨ Possono vedere le immagini prodotti
            'download-specs',
            'view-technical-sheets',
            'sync-offline-data',
            'access-mobile-tools',
            'manage-profile',
            'contact-support',
        ]);

        // ====================================
        // CREATE TEST USERS
        // ====================================
        
        // 👑 Admin principale
        $admin = User::firstOrCreate(
            ['email' => 'admin@manzoniarredourbano.it'],
            [
                'name' => 'Manzoni Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $admin->assignRole('admin');

        // 🛍️ Rivenditore Livello 1 (5% sconto)
        $rivenditoreLevel1 = User::firstOrCreate(
            ['email' => 'rivenditore1@test.it'],
            [
                'name' => 'Test Rivenditore Livello 1',
                'password' => Hash::make('password'),
                'company_name' => 'Arredo Test SRL',
                'level' => 1,
                'phone' => '+39 123 456 7890',
                'address' => 'Via Test 123, Milano',
                'vat_number' => 'IT12345678901',
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $rivenditoreLevel1->assignRole('rivenditore');

        // 🛍️ Rivenditore Livello 5 (25% sconto)
        $rivenditoreLevel5 = User::firstOrCreate(
            ['email' => 'rivenditore5@test.it'],
            [
                'name' => 'Test Rivenditore Livello 5',
                'password' => Hash::make('password'),
                'company_name' => 'Top Partner SRL',
                'level' => 5,
                'phone' => '+39 987 654 3210',
                'address' => 'Via Premium 456, Roma',
                'vat_number' => 'IT98765432109',
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $rivenditoreLevel5->assignRole('rivenditore');

        // 📱 Agente mobile
        $agente = User::firstOrCreate(
            ['email' => 'agente@test.it'],
            [
                'name' => 'Test Agente Mobile',
                'password' => Hash::make('password'),
                'company_name' => 'Manzoni Sales Team',
                'phone' => '+39 555 666 7777',
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $agente->assignRole('agente');

        // ====================================
        // OUTPUT INFORMAZIONI
        // ====================================
        
        $this->command->info('');
        $this->command->info('✅ SISTEMA RUOLI E PERMESSI CONFIGURATO!');
        $this->command->info('==========================================');
        $this->command->info('');
        $this->command->info('👑 ADMIN ACCOUNT:');
        $this->command->info('📧 Email: admin@manzoniarredourbano.it');
        $this->command->info('🔑 Password: password');
        $this->command->info('🎯 Permessi: TUTTI (' . Permission::count() . ' permessi)');
        $this->command->info('');
        $this->command->info('🛍️ RIVENDITORI TEST:');
        $this->command->info('📧 L1 (5%): rivenditore1@test.it');
        $this->command->info('📧 L5 (25%): rivenditore5@test.it');
        $this->command->info('🔑 Password: password');
        $this->command->info('');
        $this->command->info('📱 AGENTE TEST:');
        $this->command->info('📧 Email: agente@test.it');
        $this->command->info('🔑 Password: password');
        $this->command->info('');
        $this->command->info('🎯 PERMESSI CREATI:');
        foreach (Permission::all()->groupBy(function($permission) {
            if (str_starts_with($permission->name, 'manage-')) return '👑 Admin';
            if (str_contains($permission->name, 'view-') || str_contains($permission->name, 'place-') || str_contains($permission->name, 'download-')) return '🛍️ Rivenditore';
            if (str_contains($permission->name, 'catalog') || str_contains($permission->name, 'sync-') || str_contains($permission->name, 'access-')) return '📱 Agente';
            return '🌐 Shared';
        }) as $group => $perms) {
            $this->command->info($group . ': ' . $perms->pluck('name')->implode(', '));
        }
        $this->command->info('');
        $this->command->info('🚀 Sistema pronto per il login!');
    }
}