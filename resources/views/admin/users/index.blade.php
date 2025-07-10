<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                👥 Gestione Utenti - Manzoni Admin
            </h2>
            <a href="{{ route('admin.users.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                </svg>
                Nuovo Utente
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
@endif
            
            <!-- Filtri e Search -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">🔍 Filtri e Ricerca</h3>
                    
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cerca</label>
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Nome, email, azienda..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Role Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ruolo</label>
                            <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Tutti i ruoli</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Level Filter (for rivenditori) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Livello</label>
                            <select name="level" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Tutti i livelli</option>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ request('level') == $i ? 'selected' : '' }}>
                                        Livello {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Tutti</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Attivi</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inattivi</option>
                            </select>
                        </div>

                        <!-- Submit -->
                        <div class="md:col-span-4 flex space-x-2">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring ring-gray-300 transition">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                                </svg>
                                Filtra
                            </button>
                            <a href="{{ route('admin.users') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:ring ring-gray-300 transition">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Stats Summary -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white p-4 rounded-lg shadow text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $users->total() }}</div>
                    <div class="text-sm text-gray-500">Risultati</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow text-center">
                    <div class="text-2xl font-bold text-red-600">{{ \App\Models\User::role('admin')->count() }}</div>
                    <div class="text-sm text-gray-500">Admin</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ \App\Models\User::role('rivenditore')->count() }}</div>
                    <div class="text-sm text-gray-500">Rivenditori</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow text-center">
                    <div class="text-2xl font-bold text-green-600">{{ \App\Models\User::role('agente')->count() }}</div>
                    <div class="text-sm text-gray-500">Agenti</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow text-center">
                    <div class="text-2xl font-bold text-gray-600">{{ \App\Models\User::where('is_active', true)->count() }}</div>
                    <div class="text-sm text-gray-500">Attivi</div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ruolo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Livello</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ultimo accesso</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Azioni</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" name="selected_users[]" value="{{ $user->id }}" class="user-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <img class="h-10 w-10 rounded-full" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                                @if($user->company_name)
                                                    <div class="text-xs text-gray-400">{{ $user->company_name }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user->roles->first())
                                            @php $role = $user->roles->first()->name; @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($role === 'admin') bg-red-100 text-red-800
                                                @elseif($role === 'rivenditore') bg-blue-100 text-blue-800
                                                @elseif($role === 'agente') bg-green-100 text-green-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($role) }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Pubblico
                                            </span>
                                        @endif
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user->hasRole('rivenditore') && $user->level)
                                            <div class="flex items-center space-x-2">
                                                <span class="text-sm font-medium">L{{ $user->level }}</span>
                                                <span class="text-xs text-gray-500">({{ $user->discount }}% sconto)</span>
                                                
                                                <!-- Quick Level Change -->
                                                <form method="POST" action="{{ route('admin.users.level', $user) }}" class="inline-flex items-center space-x-1">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="level" onchange="this.form.submit()" class="text-xs border-gray-300 rounded">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <option value="{{ $i }}" {{ $user->level == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user->is_active)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></div>
                                                Attivo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <div class="w-1.5 h-1.5 bg-red-400 rounded-full mr-1"></div>
                                                Inattivo
                                            </span>
                                        @endif
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Mai' }}
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-1">
                                        <!-- Toggle Status -->
                                        <form method="POST" action="{{ route('admin.users.toggle', $user) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="text-indigo-600 hover:text-indigo-900"
                                                    title="{{ $user->is_active ? 'Disattiva' : 'Attiva' }}">
                                                @if($user->is_active)
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                @endif
                                            </button>
                                        </form>
                                        
                                        <!-- Edit -->
                                        <a href="#" class="text-blue-600 hover:text-blue-900" title="Modifica">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                            </svg>
                                        </a>
                                        
                                        <!-- Delete -->
                                        <button class="text-red-600 hover:text-red-900" title="Elimina" onclick="confirmDelete({{ $user->id }})">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9zM4 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 012 0v4a1 1 0 11-2 0V9zm4 0a1 1 0 012 0v4a1 1 0 11-2 0V9z" clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        Nessun utente trovato con i filtri applicati.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-3 border-t border-gray-200">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            </div>

            <!-- Bulk Actions -->
            <div id="bulk-actions" class="hidden mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center space-x-4">
                    <span class="text-sm font-medium text-blue-800">
                        <span id="selected-count">0</span> utenti selezionati
                    </span>
                    <button class="text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                        Attiva selezionati
                    </button>
                    <button class="text-sm bg-gray-600 text-white px-3 py-1 rounded hover:bg-gray-700">
                        Disattiva selezionati
                    </button>
                    <button class="text-sm bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                        Esporta selezionati
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Bulk selection
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.user-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });

        document.querySelectorAll('.user-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkActions);
        });

        function updateBulkActions() {
            const selected = document.querySelectorAll('.user-checkbox:checked');
            const bulkActions = document.getElementById('bulk-actions');
            const selectedCount = document.getElementById('selected-count');
            
            if (selected.length > 0) {
                bulkActions.classList.remove('hidden');
                selectedCount.textContent = selected.length;
            } else {
                bulkActions.classList.add('hidden');
            }
        }

        function confirmDelete(userId) {
            if (confirm('Sei sicuro di voler eliminare questo utente?')) {
                // Implement delete functionality
                console.log('Delete user:', userId);
            }
        }
    </script>
</x-app-layout>