<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class UserRoleService
{
    /**
     * Get all available roles for assignment
     */
    public static function getAssignableRoles(): Collection
    {
        return collect([
            'admin' => [
                'name' => 'Admin',
                'description' => 'Controllo totale sistema',
                'icon' => 'shield-check',
                'color' => 'red'
            ],
            'rivenditore' => [
                'name' => 'Rivenditore',
                'description' => 'Accesso ecommerce con livelli sconto',
                'icon' => 'shopping-cart',
                'color' => 'blue'
            ],
            'agente' => [
                'name' => 'Agente/Venditore',
                'description' => 'Catalogo mobile e strumenti vendita',
                'icon' => 'device-mobile',
                'color' => 'green'
            ]
        ]);
    }

    /**
     * Get level information for rivenditori
     */
    public static function getRivenditoriLevels(): Collection
    {
        return collect([
            1 => [
                'name' => 'Nuovo',
                'discount' => 5,
                'description' => 'Rivenditore in fase di avvio',
                'color' => 'gray',
                'min_orders' => 0
            ],
            2 => [
                'name' => 'Consolidato',
                'discount' => 10,
                'description' => 'Rivenditore con esperienza',
                'color' => 'blue',
                'min_orders' => 10
            ],
            3 => [
                'name' => 'Fedele',
                'discount' => 15,
                'description' => 'Rivenditore consolidato e fedele',
                'color' => 'green',
                'min_orders' => 25
            ],
            4 => [
                'name' => 'Premium',
                'discount' => 20,
                'description' => 'Rivenditore premium ad alto volume',
                'color' => 'purple',
                'min_orders' => 50
            ],
            5 => [
                'name' => 'Top Partner',
                'discount' => 25,
                'description' => 'Partner strategico massimo livello',
                'color' => 'gold',
                'min_orders' => 100
            ]
        ]);
    }

    /**
     * Calculate price with user discount
     */
    public static function calculateUserPrice(float $basePrice, User $user): float
    {
        if (!$user->hasRole('rivenditore')) {
            return $basePrice;
        }

        $discount = $user->discount;
        return $basePrice * (1 - $discount / 100);
    }

    /**
     * Get user dashboard route based on role
     */
    public static function getUserDashboardRoute(User $user): string
    {
        if ($user->hasRole('admin')) {
            return '/admin/dashboard';  // URL diretto invece di route()
        }

        if ($user->hasRole('rivenditore')) {
            return '/rivenditore/dashboard';  // URL diretto
        }

        if ($user->hasRole('agente')) {
            return '/agente/dashboard';  // URL diretto  
        }

        return '/dashboard'; // URL diretto
    }

    /**
     * Check if user can view pricing
     */
    public static function canViewPricing(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'rivenditore', 'agente']);
    }

    /**
     * Check if user can place orders
     */
    public static function canPlaceOrders(User $user): bool
    {
        return $user->hasRole('rivenditore') && $user->is_active;
    }
}