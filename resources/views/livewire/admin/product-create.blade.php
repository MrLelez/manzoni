<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Crea Nuovo Prodotto</h1>
                <p class="mt-2 text-sm text-gray-600">Aggiungi un nuovo prodotto al catalogo Manzoni</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('admin.products.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Torna alla Lista
                </a>
            </div>
        </div>
    </div>

    <!-- Messages -->
    @if($successMessage)
        <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
            <div class="flex">
                <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <p class="ml-3 text-sm text-green-800">{{ $successMessage }}</p>
            </div>
        </div>
    @endif

    @if(!empty($errors))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
                <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <div class="ml-3">
                    <p class="text-sm text-red-800 font-medium">Errori di validazione:</p>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach($errors as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Form -->
    <form wire:submit.prevent="save" class="space-y-6">
        <!-- Main Product Information -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Informazioni Prodotto</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nome Prodotto -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nome Prodotto *</label>
                        <input type="text" wire:model="name" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Es. Panchina Roma Classic">
                    </div>

                    <!-- SKU -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Codice SKU *</label>
                        <div class="flex">
                            <input type="text" wire:model="sku" 
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="ROMA-001">
                            <button type="button" wire:click="generateSku"
                                    class="px-3 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-md hover:bg-gray-200 text-sm">
                                Auto
                            </button>
                        </div>
                    </div>

                    <!-- Categoria - usa il tuo metodo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Categoria *</label>
                        <select wire:model="category_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Seleziona categoria</option>
                            @foreach($formData['categories'] as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Prezzo Base -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Prezzo Base (€) *</label>
                        <input type="number" wire:model="base_price" step="0.01" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                               placeholder="1250.00">
                    </div>

                    <!-- Status - compatibile con il tuo controller -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stato</label>
                        <select wire:model="status" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            @foreach($formData['statuses'] as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Descrizione Breve -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descrizione Breve</label>
                        <textarea wire:model="short_description" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Breve descrizione per catalogo e anteprima..."></textarea>
                    </div>

                    <!-- Descrizione Completa -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descrizione Completa</label>
                        <textarea wire:model="description" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Descrizione dettagliata del prodotto..."></textarea>
                    </div>

                    <!-- Specifiche Tecniche -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Specifiche Tecniche</label>
                        <textarea wire:model="technical_specs" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Specifiche tecniche dettagliate..."></textarea>
                    </div>

                    <!-- Featured -->
                    <div class="md:col-span-2">
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="featured"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Prodotto in evidenza</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Technical Specifications -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Specifiche Tecniche</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Peso -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Peso (kg)</label>
                        <input type="number" wire:model="weight" step="0.1" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Dimensioni -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dimensioni</label>
                        <input type="text" wire:model="dimensions"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Es: {'length': 180, 'width': 60, 'height': 80}">
                    </div>

                    <!-- Materiali -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Materiali</label>
                        <select wire:model="materials" multiple
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            @foreach($formData['materials'] as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Colori -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Colori</label>
                        <select wire:model="colors" multiple
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            @foreach($formData['colors'] as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tipo Installazione -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo Installazione</label>
                        <select wire:model="installation_type" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            @foreach($formData['installation_types'] as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Garanzia -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Garanzia (anni)</label>
                        <input type="number" wire:model="warranty_years" min="1" max="10"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing per Livelli -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Prezzi per Livelli Rivenditori</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($formData['user_levels'] as $level => $data)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-gray-900 mb-2">Livello {{ $level }} - {{ $data['name'] }}</h3>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-500">€</span>
                                <input type="number" wire:model="pricing.{{ $level }}" step="0.01" min="0"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <span class="text-xs text-gray-500">(-{{ $data['discount'] }}%)</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Images -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Immagini</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($images as $index => $image)
                        <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <input type="file" wire:model="images.{{ $index }}.file"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <button type="button" wire:click="removeImage({{ $index }})"
                                    class="px-3 py-2 text-red-600 hover:text-red-800">
                                Rimuovi
                            </button>
                        </div>
                    @endforeach
                    <button type="button" wire:click="addImage"
                            class="w-full px-4 py-2 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-gray-400">
                        + Aggiungi Immagine
                    </button>
                </div>
            </div>
        </div>

        <!-- Tags -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Tags</h2>
            </div>
            <div class="p-6">
                <div class="flex flex-wrap gap-2 mb-4">
                    @foreach($tags as $index => $tag)
                        <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">
                            {{ $tag }}
                            <button type="button" wire:click="removeTag({{ $index }})" class="ml-2 text-blue-600 hover:text-blue-800">
                                ×
                            </button>
                        </span>
                    @endforeach
                </div>
                <div class="flex">
                    <input type="text" wire:model="tagInput" wire:keydown.enter="addTag"
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Aggiungi tag...">
                    <button type="button" wire:click="addTag"
                            class="px-4 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700">
                        Aggiungi
                    </button>
                </div>
            </div>
        </div>

        <!-- Prodotti Correlati -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Prodotti Correlati</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($relationships as $index => $relationship)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-sm font-medium text-gray-900">Relazione #{{ $index + 1 }}</h4>
                                <button type="button" wire:click="removeRelationship({{ $index }})"
                                        class="text-red-600 hover:text-red-800 text-sm">
                                    Rimuovi
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Prodotto</label>
                                    <select wire:model="relationships.{{ $index }}.related_product_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Seleziona prodotto</option>
                                        @foreach($formData['available_products'] as $product)
                                            <option value="{{ $product['id'] }}">
                                                {{ $product['name'] }} ({{ $product['sku'] }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo Relazione</label>
                                    <select wire:model="relationships.{{ $index }}.type"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                        @foreach($formData['relationship_types'] as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <button type="button" wire:click="addRelationship"
                            class="w-full px-4 py-2 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-gray-400">
                        + Aggiungi Relazione
                    </button>
                </div>
            </div>
        </div>

        <!-- Accessori -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Prodotti Accessori</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($accessories as $index => $accessory)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-sm font-medium text-gray-900">Accessorio #{{ $index + 1 }}</h4>
                                <button type="button" wire:click="removeAccessory({{ $index }})"
                                        class="text-red-600 hover:text-red-800 text-sm">
                                    Rimuovi
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Prodotto</label>
                                    <select wire:model="accessories.{{ $index }}.product_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Seleziona prodotto</option>
                                        @foreach($formData['available_products'] as $product)
                                            <option value="{{ $product['id'] }}">
                                                {{ $product['name'] }} ({{ $product['sku'] }}) - {{ $product['category_name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantità</label>
                                    <input type="number" wire:model="accessories.{{ $index }}.quantity" min="1"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Modifica Prezzo (€)</label>
                                    <input type="number" wire:model="accessories.{{ $index }}.price_modifier" step="0.01"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="accessories.{{ $index }}.is_required"
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <span class="ml-2 text-sm text-gray-700">Accessorio indispensabile</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <button type="button" wire:click="addAccessory"
                            class="w-full px-4 py-2 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-gray-400">
                        + Aggiungi Accessorio
                    </button>
                </div>
            </div>
        </div>

        <!-- Submit Actions -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Tutti i campi marcati con * sono obbligatori
                    </div>
                    <div class="flex space-x-4">
                        <button type="button" wire:click="saveDraft"
                                class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50"
                                wire:loading.attr="disabled">
                            <span wire:loading.remove>Salva Bozza</span>
                            <span wire:loading>Salvando...</span>
                        </button>
                        <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                                wire:loading.attr="disabled">
                            <span wire:loading.remove>Crea Prodotto</span>
                            <span wire:loading>Creando...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
</div>