<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserRoleService;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminDashboardController extends Controller
{
    /**
     * Dashboard principale admin con contenuti recenti
     */
    public function index()
    {
        // Statistiche principali
        $stats = [
            'total_users' => User::count(),
            'total_products' => \App\Models\Product::count(),
            'total_images' => \App\Models\Image::count(),
            'active_users' => User::where('is_active', true)->count(),
            'rivenditori' => User::role('rivenditore')->count(),
            'agenti' => User::role('agente')->count(),
        ];

        // Ultimo prodotto inserito - SENZA primaryImage nel with()
        $latestProduct = \App\Models\Product::with(['category'])
                                          ->withCount('images')
                                          ->latest()
                                          ->first();

        // Ultima immagine caricata
        $latestImage = \App\Models\Image::with('imageable')
                                      ->latest()
                                      ->first();

        // Rivenditori per livello
        $rivenditoriByLevel = [];
        for ($level = 1; $level <= 5; $level++) {
            $rivenditoriByLevel[$level] = User::role('rivenditore')
                                            ->where('level', $level)
                                            ->count();
        }

        // Utenti recenti (per eventuale sezione aggiuntiva)
        $recentUsers = User::with('roles')
                          ->latest()
                          ->limit(5)
                          ->get();

        return view('admin.dashboard', compact(
            'stats', 
            'latestProduct', 
            'latestImage', 
            'rivenditoriByLevel', 
            'recentUsers'
        ));
    }

    public function users()
    {
        $users = User::with('roles')
            ->when(request('role'), function ($query, $role) {
                return $query->role($role);
            })
            ->when(request('level'), function ($query, $level) {
                return $query->where('level', $level);
            })
            ->when(request('status'), function ($query, $status) {
                return $query->where('is_active', $status === 'active');
            })
            ->when(request('search'), function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('company_name', 'like', "%{$search}%")
                      ->orWhere('vat_number', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function createUser()
    {
        $roles = UserRoleService::getAssignableRoles();
        $levels = UserRoleService::getRivenditoriLevels();

        return view('admin.users.create', compact('roles', 'levels'));
    }

    public function storeUser()
    {
        $validated = request()->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name',
            'company_name' => 'nullable|string|max:255',
            'level' => 'nullable|integer|min:1|max:5',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'vat_number' => 'nullable|string|max:255|unique:users,vat_number',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Il nome è obbligatorio',
            'email.required' => 'L\'email è obbligatoria',
            'email.unique' => 'Questa email è già in uso',
            'password.required' => 'La password è obbligatoria',
            'password.min' => 'La password deve essere di almeno 8 caratteri',
            'password.confirmed' => 'Le password non coincidono',
            'role.required' => 'Seleziona un ruolo',
            'vat_number.unique' => 'Questa partita IVA è già in uso',
        ]);

        // Validazione condizionale per rivenditori
        if ($validated['role'] === 'rivenditore' && empty($validated['level'])) {
            return back()->withErrors(['level' => 'Il livello è obbligatorio per i rivenditori'])
                        ->withInput();
        }

        // Validazione aziendale per rivenditori e agenti
        if (in_array($validated['role'], ['rivenditore', 'agente']) && empty($validated['company_name'])) {
            return back()->withErrors(['company_name' => 'Il nome azienda è obbligatorio per rivenditori e agenti'])
                        ->withInput();
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'company_name' => $validated['company_name'],
            'level' => $validated['role'] === 'rivenditore' ? $validated['level'] : null,
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'vat_number' => $validated['vat_number'],
            'is_active' => $validated['is_active'] ?? true,
            'email_verified_at' => now(),
        ]);

        $user->assignRole($validated['role']);

        // Log dell'azione di creazione utente
        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->withProperties([
                'role' => $validated['role'],
                'level' => $user->level,
                'company_name' => $user->company_name,
                'created_by_admin' => auth()->user()->name
            ])
            ->log("Utente creato con ruolo {$validated['role']}");

        return redirect()
            ->route('admin.users')
            ->with('success', "✅ Utente {$user->name} creato con successo! Ruolo: " . ucfirst($validated['role']) . 
                   ($user->level ? " (Livello {$user->level})" : ''));
    }

    public function toggleUserStatus(User $user)
    {
        $oldStatus = $user->is_active;
        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'attivato' : 'disattivato';
        
        // Log dell'azione
        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->withProperties([
                'old_status' => $oldStatus,
                'new_status' => $user->is_active,
                'changed_by_admin' => auth()->user()->name
            ])
            ->log("Utente {$status}");
        
        return back()->with('success', "Utente {$status} con successo!");
    }

    public function updateUserLevel(User $user)
    {
        $validated = request()->validate([
            'level' => 'required|integer|min:1|max:5'
        ]);

        if (!$user->hasRole('rivenditore')) {
            return back()->with('error', 'Solo i rivenditori possono avere un livello!');
        }

        $oldLevel = $user->level;
        $user->update(['level' => $validated['level']]);

        // Log dell'azione
        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->withProperties([
                'old_level' => $oldLevel,
                'new_level' => $validated['level'],
                'changed_by_admin' => auth()->user()->name
            ])
            ->log("Livello rivenditore aggiornato da L{$oldLevel} a L{$validated['level']}");

        return back()->with('success', "Livello aggiornato a {$validated['level']}!");
    }

    /**
     * Dashboard principale admin con contenuti recenti
     */
    
}