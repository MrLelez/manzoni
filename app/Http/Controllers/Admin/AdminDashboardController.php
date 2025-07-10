<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserRoleService;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'admins' => User::role('admin')->count(),
            'rivenditori' => User::role('rivenditore')->count(),
            'agenti' => User::role('agente')->count(),
            'active_users' => User::where('is_active', true)->count(),
            'inactive_users' => User::where('is_active', false)->count(),
        ];

        $recentUsers = User::with('roles')
            ->latest()
            ->take(5)
            ->get();

        $rivenditoriByLevel = User::role('rivenditore')
            ->selectRaw('level, count(*) as count')
            ->groupBy('level')
            ->orderBy('level')
            ->get()
            ->pluck('count', 'level');

        return view('admin.dashboard', compact('stats', 'recentUsers', 'rivenditoriByLevel'));
    }

    public function users()
    {
        $users = User::with('roles')
            ->when(request('role'), function ($query, $role) {
                return $query->role($role);
            })
            ->when(request('search'), function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('company_name', 'like', "%{$search}%");
                });
            })
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
            'vat_number' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'company_name' => $validated['company_name'],
            'level' => $validated['level'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'vat_number' => $validated['vat_number'],
            'is_active' => $validated['is_active'] ?? true,
            'email_verified_at' => now(),
        ]);

        $user->assignRole($validated['role']);

        return redirect()
            ->route('admin.users')
            ->with('success', "Utente {$user->name} creato con successo!");
    }

    public function toggleUserStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'attivato' : 'disattivato';
        
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

        $user->update(['level' => $validated['level']]);

        return back()->with('success', "Livello aggiornato a {$validated['level']}!");
    }
}