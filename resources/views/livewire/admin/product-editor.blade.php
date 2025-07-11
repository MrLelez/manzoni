{{-- resources/views/livewire/admin/product-editor.blade.php --}}

<div>
    {{-- Header --}}
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Editor Prodotto
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Clicca su qualsiasi campo per modificarlo
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.products.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring ring-gray-300 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Torna alla Lista
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Quick Actions Bar --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            {{-- Status Toggle --}}
                            <button wire:click="toggleStatus" 
                                    class="inline-flex items-center space-x-2 hover:bg-white hover:shadow-sm px-3 py-2 rounded-lg transition-all duration-200">
                                <span class="text-sm font-medium text-gray-700">Status:</span>
                                <div>{!! $this->statusBadge !!}</div>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                            </button>

                            {{-- Featured Toggle --}}
                            <button wire:click="toggleFeatured" 
                                    class="inline-flex items-center space-x-2 hover:bg-white hover:shadow-sm px-3 py-2 rounded-lg transition-all duration-200">
                                <span class="text-sm font-medium text-gray-700">In Evidenza:</span>
                                @if($is_featured)
                                    <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">SÌ</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">NO</span>
                                @endif
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="text-sm text-gray-500">
                            ID: {{ $product->id }} • 
                            Creato: {{ $product->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Main Content --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- Basic Info --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Informazioni Base</h3>
                            <p class="text-sm text-gray-500 mt-1">Clicca per modificare</p>
                        </div>
                        <div class="p-6 space-y-6">
                            
                            {{-- Name Field --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Nome Prodotto</label>
                                @if($editingField === 'name')
                                    <div class="flex items-center space-x-2">
                                        <input type="text" 
                                               wire:model="name"
                                               wire:keydown.enter="saveField('name')"
                                               wire:keydown.escape="stopEditing"
                                               class="flex-1 border-blue-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                                               autofocus>
                                        <button wire:click="saveField('name')" 
                                                class="px-3 py-2 bg-green-600 text-white text-xs rounded-md hover:bg-green-700">
                                            ✓
                                        </button>
                                        <button wire:click="stopEditing" 
                                                class="px-3 py-2 bg-gray-400 text-white text-xs rounded-md hover:bg-gray-500">
                                            ✕
                                        </button>
                                    </div>
                                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                @else
                                    <div wire:click="startEditing('name')" 
                                         class="text-lg font-semibold text-gray-900 hover:bg-blue-50 hover:text-blue-700 p-2 rounded cursor-pointer transition-all duration-200">
                                        {{ $name ?: 'Clicca per aggiungere nome...' }}
                                    </div>
                                @endif
                            </div>

                            {{-- SKU Field --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Codice SKU</label>
                                @if($editingField === 'sku')
                                    <div class="flex items-center space-x-2">
                                        <input type="text" 
                                               wire:model="sku"
                                               wire:keydown.enter="saveField('sku')"
                                               wire:keydown.escape="stopEditing"
                                               class="flex-1 border-blue-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                                               autofocus>
                                        <button wire:click="saveField('sku')" 
                                                class="px-3 py-2 bg-green-600 text-white text-xs rounded-md hover:bg-green-700">
                                            ✓
                                        </button>
                                        <button wire:click="stopEditing" 
                                                class="px-3 py-2 bg-gray-400 text-white text-xs rounded-md hover:bg-gray-500">
                                            ✕
                                        </button>
                                    </div>
                                    @error('sku') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                @else
                                    <div wire:click="startEditing('sku')" 
                                         class="font-mono text-gray-600 hover:bg-blue-50 hover:text-blue-700 p-2 rounded cursor-pointer transition-all duration-200">
                                        {{ $sku ?: 'Clicca per aggiungere SKU...' }}
                                    </div>
                                @endif
                            </div>

                            {{-- Price Field --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Prezzo Base</label>
                                @if($editingField === 'base_price')
                                    <div class="flex items-center space-x-2">
                                        <div class="relative flex-1">
                                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">€</span>
                                            <input type="number" 
                                                   step="0.01"
                                                   wire:model="base_price"
                                                   wire:keydown.enter="saveField('base_price')"
                                                   wire:keydown.escape="stopEditing"
                                                   class="pl-8 border-blue-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full"
                                                   autofocus>
                                        </div>
                                        <button wire:click="saveField('base_price')" 
                                                class="px-3 py-2 bg-green-600 text-white text-xs rounded-md hover:bg-green-700">
                                            ✓
                                        </button>
                                        <button wire:click="stopEditing" 
                                                class="px-3 py-2 bg-gray-400 text-white text-xs rounded-md hover:bg-gray-500">
                                            ✕
                                        </button>
                                    </div>
                                    @error('base_price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                @else
                                    <div wire:click="startEditing('base_price')" 
                                         class="text-xl font-bold text-blue-600 hover:bg-blue-50 p-2 rounded cursor-pointer transition-all duration-200">
                                        €{{ number_format($base_price, 2, ',', '.') }}
                                    </div>
                                @endif
                            </div>

                            {{-- Category Field --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Categoria</label>
                                <select wire:model="category_id" 
                                        wire:change="updateCategory"
                                        class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                                    <option value="">Seleziona categoria...</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Descrizione</h3>
                        </div>
                        <div class="p-6">
                            @if($editingField === 'description')
                                <div class="space-y-3">
                                    <textarea wire:model="description"
                                              wire:keydown.escape="stopEditing"
                                              rows="8"
                                              class="w-full border-blue-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                                              placeholder="Inserisci la descrizione del prodotto..."
                                              autofocus></textarea>
                                    <div class="flex space-x-2">
                                        <button wire:click="saveField('description')" 
                                                class="px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">
                                            Salva Descrizione
                                        </button>
                                        <button wire:click="stopEditing" 
                                                class="px-4 py-2 bg-gray-400 text-white text-sm rounded-md hover:bg-gray-500">
                                            Annulla
                                        </button>
                                    </div>
                                    @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            @else
                                <div wire:click="startEditing('description')" 
                                     class="min-h-[100px] prose max-w-none hover:bg-blue-50 p-3 rounded cursor-pointer transition-all duration-200">
                                    @if($description)
                                        {!! nl2br(e($description)) !!}
                                    @else
                                        <span class="text-gray-400 italic">Clicca per aggiungere una descrizione...</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Images Section - WORKING VERSION --}}
                    
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg ">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">
                Immagini ({{ $product->images ? $product->images->count() : 0 }})
            </h3>
            <button wire:click="$toggle('showImageUpload')" 
                    class="px-3 py-1 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                {{ $showImageUpload ? 'Chiudi' : 'Aggiungi' }}
            </button>
        </div>
    </div>
    <div class="p-6">
        
        {{-- Messages --}}
        @if (session('message'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('message') }}
            </div>
        @endif
        
        @if (session('error'))
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif
        
        {{-- Upload Section --}}
        @if($showImageUpload)
            <div class="mb-6 p-4 border-2 border-dashed border-blue-300 rounded-lg">
                <div class="text-center mb-4">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <div class="mt-4">
                        <label class="cursor-pointer">
                            <span class="mt-2 block text-sm font-medium text-gray-900">
                                Seleziona immagini da caricare
                            </span>
                            <input type="file" 
                                   wire:model="uploadedImages" 
                                   multiple 
                                   accept="image/*" 
                                   class="block w-full mt-2 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </label>
                        <p class="mt-1 text-xs text-gray-500">
                            PNG, JPG, WebP fino a 10MB
                        </p>
                    </div>
                </div>
                
                {{-- Upload Button - QUESTO ERA IL PEZZO MANCANTE --}}
                @if($uploadedImages)
                    <div class="text-center">
                        <button wire:click="uploadImages" 
                                wire:loading.attr="disabled"
                                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="uploadImages">
                                📤 Carica {{ count($uploadedImages) }} Immagini
                            </span>
                            <span wire:loading wire:target="uploadImages">
                                ⏳ Caricamento in corso...
                            </span>
                        </button>
                        <p class="mt-2 text-xs text-gray-600">
                            Clicca per caricare le immagini selezionate
                        </p>
                    </div>
                @endif
            </div>
        @endif

        {{-- Images Grid with Drag & Drop --}}
        @if($product->images && $product->images->count() > 0)
            
            {{-- Drag & Drop Container --}}
            <div id="sortable-images" class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($product->images->sortBy('sort_order') as $image)
                    <div class="image-item border-2 rounded-lg overflow-hidden bg-white shadow hover:shadow-md transition-all {{ $image->is_primary ? 'border-yellow-400 ring-2 ring-yellow-200' : 'border-gray-200' }}"
                         data-image-id="{{ $image->id }}">
                        
                        {{-- Drag Handle --}}
                        <div class="drag-handle bg-gray-100 hover:bg-blue-100 p-2 cursor-move text-center border-b transition-colors">
                            <div class="flex items-center justify-center space-x-2">
                                <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2zM3 16a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2z"></path>
                                </svg>
                                <span class="text-xs font-medium text-gray-600">TRASCINA</span>
                                @if($image->is_primary)
                                    <span class="text-xs bg-yellow-100 text-yellow-800 px-1 py-0.5 rounded font-bold">⭐</span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500 mt-1">ID: {{ $image->id }}</div>
                        </div>
                        
                        {{-- Image --}}
                        <div class="relative aspect-square group">
                            <img src="{{ $image->aws_url }}" 
                                 alt="{{ $image->alt_text ?: $product->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                            
                            {{-- Action Buttons --}}
                            <div class="absolute top-2 right-2 flex space-x-1">
                                @if(!$image->is_primary)
                                    <button wire:click="setPrimaryImage({{ $image->id }})"
                                            class="bg-yellow-500 text-white p-1 rounded text-xs hover:bg-yellow-600 opacity-0 group-hover:opacity-100 transition-opacity"
                                            title="Imposta come principale">
                                        ⭐
                                    </button>
                                @endif
                                <button wire:click="deleteImage({{ $image->id }})"
                                        wire:confirm="Sei sicuro di voler eliminare questa immagine?"
                                        class="bg-red-600 text-white p-1 rounded text-xs hover:bg-red-700 opacity-0 group-hover:opacity-100 transition-opacity"
                                        title="Elimina">
                                    🗑️
                                </button>
                            </div>
                        </div>
                        
                        {{-- Alt Text --}}
                        <div class="p-2">
                            <input type="text" 
                                   value="{{ $image->alt_text ?? '' }}"
                                   placeholder="Alt text per SEO..."
                                   class="w-full text-xs border rounded px-2 py-1 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                   wire:change="updateImageAltText({{ $image->id }}, $event.target.value)">
                        </div>
                    </div>
                @endforeach
            </div>
            
        @else
            {{-- Empty State --}}
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6 6l-1-1m1 1l4 4m-4-4v6m0-6h6m-6 0l-1-1"/>
                </svg>
                <p class="mt-2 text-sm text-gray-500 mb-4">Nessuna immagine caricata</p>
                <button wire:click="$set('showImageUpload', true)" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    📤 Carica prima immagine
                </button>
            </div>
        @endif
    </div>
</div>


                {{-- Sidebar --}}
                <div class="space-y-6">
                    
                    {{-- Quick Stats --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Statistiche</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Immagini</span>
                                <span class="font-semibold">{{ $product->images ? $product->images->count() : 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Tags</span>
                                <span class="font-semibold">{{ $product->tags ? $product->tags->count() : 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Ultima modifica</span>
                                <span class="text-sm">{{ $product->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Tags Management --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Tags</h3>
                        </div>
                        <div class="p-6">
                            @if($tags && $tags->count() > 0)
                                <div class="space-y-3">
                                    @foreach($tags as $tag)
                                        <label class="flex items-center">
                                            <input type="checkbox" 
                                                   wire:model="selectedTags" 
                                                   wire:change="updateTags"
                                                   value="{{ $tag->id }}"
                                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-700">{{ $tag->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <p class="text-sm text-gray-500">
                                        Nessun tag disponibile. 
                                        <span class="text-xs block mt-1">Crea dei tag per organizzare i prodotti.</span>
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Loading States --}}
    <div wire:loading.flex wire:target="saveField,updateCategory,updateTags,toggleStatus,toggleFeatured" 
         class="fixed inset-0 bg-gray-500 bg-opacity-50 z-50 items-center justify-center">
        <div class="bg-white p-4 rounded-lg shadow-lg">
            <div class="flex items-center space-x-3">
                <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm font-medium text-gray-700">Salvando...</span>
            </div>
        </div>
    </div>
</div>

{{-- Toast Notifications --}}
<script>
    window.addEventListener('field-saved', event => {
        // Potresti aggiungere notifiche toast qui
        console.log('Campo salvato:', event.detail.field);
    });
    
    window.addEventListener('status-changed', event => {
        console.log('Status cambiato:', event.detail.status);
    });
</script>