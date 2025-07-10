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

        // Create permissions
        $permissions = [
            // Admin permissions
            'manage-users',
            'manage-products',
            'manage-orders',
            'manage-levels',
            'view-analytics',
            'manage-settings',
            'export-data',
            'manage-categories',
            
            // Rivenditore permissions
            'view-pricing',
            'place-orders',
            'view-order-history',
            'download-invoices',
            'manage-profile',
            
            // Agente permissions
            'view-catalog',
            'download-specs',
            'view-technical-sheets',
            'sync-offline-data',
            'access-mobile-tools',
            
            // Shared permissions
            'view-products',
            'search-products',
            'contact-support',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // 1. ADMIN ROLE - Full control
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // 2. RIVENDITORE ROLE - Ecommerce + levels
        $rivenditoreRole = Role::create(['name' => 'rivenditore']);
        $rivenditoreRole->givePermissionTo([
            'view-products',
            'search-products',
            'view-pricing',
            'place-orders',
            'view-order-history',
            'download-invoices',
            'manage-profile',
            'contact-support',
        ]);

        // 3. AGENTE ROLE - Mobile catalog
        $agenteRole = Role::create(['name' => 'agente']);
        $agenteRole->givePermissionTo([
            'view-products',
            'search-products',
            'view-catalog',
            'download-specs',
            'view-technical-sheets',
            'sync-offline-data',
            'access-mobile-tools',
            'manage-profile',
            'contact-support',
        ]);

        // Create default admin user
        $admin = User::create([
            'name' => 'Manzoni Admin',
            'email' => 'admin@manzoniarredourbano.it',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        // Create sample rivenditore users (different levels)
        $rivenditoreLevel1 = User::create([
            'name' => 'Test Rivenditore Livello 1',
            'email' => 'rivenditore1@test.it',
            'password' => Hash::make('password'),
            'company_name' => 'Arredo Test SRL',
            'level' => 1,
            'phone' => '+39 123 456 7890',
            'address' => 'Via Test 123, Milano',
            'vat_number' => 'IT12345678901',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        $rivenditoreLevel1->assignRole('rivenditore');

        $rivenditoreLevel5 = User::create([
            'name' => 'Test Rivenditore Livello 5',
            'email' => 'rivenditore5@test.it',
            'password' => Hash::make('password'),
            'company_name' => 'Top Partner SRL',
            'level' => 5,
            'phone' => '+39 987 654 3210',
            'address' => 'Via Premium 456, Roma',
            'vat_number' => 'IT98765432109',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        $rivenditoreLevel5->assignRole('rivenditore');

        // Create sample agente user
        $agente = User::create([
            'name' => 'Test Agente Mobile',
            'email' => 'agente@test.it',
            'password' => Hash::make('password'),
            'company_name' => 'Manzoni Sales Team',
            'phone' => '+39 555 666 7777',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        $agente->assignRole('agente');

        $this->command->info('✅ Ruoli e permissions creati con successo!');
        $this->command->info('📧 Admin: admin@manzoniarredourbano.it');
        $this->command->info('📧 Rivenditore L1: rivenditore1@test.it');
        $this->command->info('📧 Rivenditore L5: rivenditore5@test.it');
        $this->command->info('📧 Agente: agente@test.it');
        $this->command->info('🔑 Password per tutti: password');
    }
}