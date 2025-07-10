<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Crea Nuovo Utente</h2>
                <p class="mt-1 text-sm text-gray-600">Aggiungi un nuovo utente al sistema Manzoni</p>
            </div>
            <a href="{{ route('admin.users') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Torna alla Lista
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Flash Messages -->
            @if($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Ci sono alcuni errori da correggere:</h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-8">
                @csrf

                <!-- Informazioni Base -->
                <div class="bg-white shadow-lg rounded-xl border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-xl">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                            Informazioni Base
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nome -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-900 mb-2">Nome Completo *</label>
                                <input type="text" 
                                       name="name" 
                                       id="name"
                                       value="{{ old('name') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('name') border-red-500 ring-red-200 @enderror"
                                       placeholder="Es. Mario Rossi">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-900 mb-2">Email *</label>
                                <input type="email" 
                                       name="email" 
                                       id="email"
                                       value="{{ old('email') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('email') border-red-500 ring-red-200 @enderror"
                                       placeholder="mario.rossi@esempio.com">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-900 mb-2">Password *</label>
                                <input type="password" 
                                       name="password" 
                                       id="password"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('password') border-red-500 ring-red-200 @enderror"
                                       placeholder="Minimo 8 caratteri">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Conferma Password -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-900 mb-2">Conferma Password *</label>
                                <input type="password" 
                                       name="password_confirmation" 
                                       id="password_confirmation"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                       placeholder="Ripeti la password">
                            </div>

                            <!-- Telefono -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-900 mb-2">Telefono</label>
                                <input type="tel" 
                                       name="phone" 
                                       id="phone"
                                       value="{{ old('phone') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('phone') border-red-500 ring-red-200 @enderror"
                                       placeholder="+39 123 456 7890">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="flex items-center pt-8">
                                <input type="checkbox" 
                                       name="is_active" 
                                       id="is_active"
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-3 text-sm font-medium text-gray-900">
                                    Utente attivo
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ruolo -->
                <div class="bg-white shadow-lg rounded-xl border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-xl">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Selezione Ruolo
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($roles as $roleKey => $roleInfo)
                                <label class="cursor-pointer">
                                    <input type="radio" 
                                           name="role" 
                                           value="{{ $roleKey }}"
                                           {{ old('role') == $roleKey ? 'checked' : '' }}
                                           class="sr-only role-radio"
                                           onchange="handleRoleChange('{{ $roleKey }}')">
                                    <div class="role-card border-2 border-gray-200 rounded-xl p-4 hover:border-blue-300 transition-all duration-200 hover:shadow-md">
                                        <div class="text-center">
                                            <div class="w-12 h-12 mx-auto mb-3 rounded-full flex items-center justify-center
                                                @if($roleInfo['color'] === 'red') bg-red-100
                                                @elseif($roleInfo['color'] === 'blue') bg-blue-100
                                                @elseif($roleInfo['color'] === 'green') bg-green-100
                                                @endif">
                                                <svg class="w-6 h-6 
                                                    @if($roleInfo['color'] === 'red') text-red-600
                                                    @elseif($roleInfo['color'] === 'blue') text-blue-600
                                                    @elseif($roleInfo['color'] === 'green') text-green-600
                                                    @endif" 
                                                     fill="currentColor" viewBox="0 0 20 20">
                                                    @if($roleKey === 'admin')
                                                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    @elseif($roleKey === 'rivenditore')
                                                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                                    @else
                                                        <path d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H5V5h10v7H8.771z"></path>
                                                    @endif
                                                </svg>
                                            </div>
                                            <h4 class="font-semibold text-gray-900 mb-1">{{ $roleInfo['name'] }}</h4>
                                            <p class="text-sm text-gray-600">{{ $roleInfo['description'] }}</p>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('role')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Livelli Rivenditore (conditional) -->
                <div id="rivenditore-level" class="hidden bg-white shadow-lg rounded-xl border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-blue-50 rounded-t-xl">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            Livello Rivenditore
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Seleziona il livello di sconto per questo rivenditore</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-5 gap-3">
                            @foreach($levels as $levelNum => $levelInfo)
                                <label class="cursor-pointer">
                                    <input type="radio" 
                                           name="level" 
                                           value="{{ $levelNum }}"
                                           {{ old('level') == $levelNum ? 'checked' : '' }}
                                           class="sr-only level-radio">
                                    <div class="level-card border-2 border-gray-200 rounded-lg p-4 text-center hover:border-blue-300 transition-all duration-200">
                                        <div class="font-bold text-xl text-gray-900 mb-1">L{{ $levelNum }}</div>
                                        <div class="text-sm font-medium text-gray-700 mb-1">{{ $levelInfo['name'] }}</div>
                                        <div class="text-xs text-blue-600 font-semibold">{{ $levelInfo['discount'] }}% sconto</div>
                                        <div class="mt-2">
                                            <div class="w-3 h-3 rounded-full mx-auto" style="background-color: 
                                                @if($levelInfo['color'] === 'gray') #6B7280
                                                @elseif($levelInfo['color'] === 'blue') #3B82F6
                                                @elseif($levelInfo['color'] === 'green') #10B981
                                                @elseif($levelInfo['color'] === 'purple') #8B5CF6
                                                @elseif($levelInfo['color'] === 'gold') #F59E0B
                                                @endif">
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('level')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Informazioni Azienda (conditional) -->
                <div id="company-info" class="hidden bg-white shadow-lg rounded-xl border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-green-50 rounded-t-xl">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-6a1 1 0 00-1-1H9a1 1 0 00-1 1v6a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 8a1 1 0 011-1h1a1 1 0 011 1v1a1 1 0 01-1 1H8a1 1 0 01-1-1v-1zm1-3a1 1 0 011-1h1a1 1 0 011 1v1a1 1 0 01-1 1H9a1 1 0 01-1-1V9zm4-1a1 1 0 00-1 1v1a1 1 0 001 1h1a1 1 0 001-1V9a1 1 0 00-1-1h-1z" clip-rule="evenodd"></path>
                            </svg>
                            Informazioni Azienda
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nome Azienda -->
                            <div>
                                <label for="company_name" class="block text-sm font-medium text-gray-900 mb-2">Nome Azienda</label>
                                <input type="text" 
                                       name="company_name" 
                                       id="company_name"
                                       value="{{ old('company_name') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('company_name') border-red-500 ring-red-200 @enderror"
                                       placeholder="Es. Arredo Solutions SRL">
                                @error('company_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Partita IVA -->
                            <div>
                                <label for="vat_number" class="block text-sm font-medium text-gray-900 mb-2">Partita IVA</label>
                                <input type="text" 
                                       name="vat_number" 
                                       id="vat_number"
                                       value="{{ old('vat_number') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('vat_number') border-red-500 ring-red-200 @enderror"
                                       placeholder="IT12345678901">
                                @error('vat_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Indirizzo -->
                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-900 mb-2">Indirizzo</label>
                                <textarea name="address" 
                                          id="address"
                                          rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('address') border-red-500 ring-red-200 @enderror"
                                          placeholder="Via, Città, CAP, Provincia">{{ old('address') }}</textarea>
                                @error('address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Actions -->
                <div class="bg-white shadow-lg rounded-xl border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Conferma Creazione</h3>
                            <p class="text-sm text-gray-600">Verifica i dati e crea il nuovo utente</p>
                        </div>
                        <div class="flex space-x-4">
                            <a href="{{ route('admin.users') }}" 
                               class="px-6 py-3 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200">
                                Annulla
                            </a>
                            <button type="submit" 
                                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Crea Utente
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function handleRoleChange(role) {
            // Update visual selection for roles
            document.querySelectorAll('.role-card').forEach(card => {
                card.classList.remove('border-blue-500', 'bg-blue-50', 'shadow-md');
                card.classList.add('border-gray-200');
            });
            
            const selectedCard = document.querySelector(`input[value="${role}"]`).closest('label').querySelector('.role-card');
            selectedCard.classList.remove('border-gray-200');
            selectedCard.classList.add('border-blue-500', 'bg-blue-50', 'shadow-md');
            
            // Show/hide conditional sections
            const levelSection = document.getElementById('rivenditore-level');
            const companySection = document.getElementById('company-info');
            
            if (role === 'rivenditore') {
                levelSection.classList.remove('hidden');
                companySection.classList.remove('hidden');
            } else if (role === 'agente') {
                levelSection.classList.add('hidden');
                companySection.classList.remove('hidden');
                // Clear level selection
                document.querySelectorAll('.level-radio').forEach(input => {
                    input.checked = false;
                });
                updateLevelSelection();
            } else {
                levelSection.classList.add('hidden');
                companySection.classList.add('hidden');
                // Clear level selection
                document.querySelectorAll('.level-radio').forEach(input => {
                    input.checked = false;
                });
                updateLevelSelection();
            }
        }
        
        function updateLevelSelection() {
            document.querySelectorAll('.level-card').forEach(card => {
                card.classList.remove('border-blue-500', 'bg-blue-50');
                card.classList.add('border-gray-200');
            });
        }
        
        // Handle level selection
        document.querySelectorAll('.level-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                updateLevelSelection();
                if (this.checked) {
                    const card = this.closest('label').querySelector('.level-card');
                    card.classList.remove('border-gray-200');
                    card.classList.add('border-blue-500', 'bg-blue-50');
                }
            });
        });
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const checkedRole = document.querySelector('.role-radio:checked');
            if (checkedRole) {
                handleRoleChange(checkedRole.value);
            }
            
            const checkedLevel = document.querySelector('.level-radio:checked');
            if (checkedLevel) {
                const card = checkedLevel.closest('label').querySelector('.level-card');
                card.classList.remove('border-gray-200');
                card.classList.add('border-blue-500', 'bg-blue-50');
            }
        });
    </script>
</x-app-layout>