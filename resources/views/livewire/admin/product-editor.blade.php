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
                                <div class="flex justify-between items-center">
                                    <label class="block text-sm font-medium text-gray-700">Categoria</label>
                                    <button wire:click="$toggle('showCategoryForm')" 
                                            class="text-xs text-blue-600 hover:text-blue-800">
                                        {{ $showCategoryForm ? 'Annulla' : '+ Nuova' }}
                                    </button>
                                </div>
                                
                                {{-- New Category Form --}}
                                @if($showCategoryForm)
                                    <div class="p-3 border border-blue-200 rounded-lg bg-blue-50 space-y-3">
                                        <div>
                                            <input type="text" 
                                                   wire:model="newCategoryName"
                                                   placeholder="Nome categoria"
                                                   class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            @error('newCategoryName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <input type="text" 
                                                   wire:model="newCategoryDescription"
                                                   placeholder="Descrizione (opzionale)"
                                                   class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            @error('newCategoryDescription') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="flex space-x-2">
                                            <button wire:click="createCategory" 
                                                    class="px-3 py-1 bg-green-600 text-white text-xs rounded-md hover:bg-green-700">
                                                Crea Categoria
                                            </button>
                                            <button wire:click="$set('showCategoryForm', false)" 
                                                    class="px-3 py-1 bg-gray-400 text-white text-xs rounded-md hover:bg-gray-500">
                                                Annulla
                                            </button>
                                        </div>
                                    </div>
                                @endif
                                
                                {{-- Category Select --}}
                                <div class="flex space-x-2">
                                    <select wire:model="category_id" 
                                            wire:change="updateCategory"
                                            class="flex-1 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                                        <option value="">Seleziona categoria...</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <a href="{{ route('admin.categories.index') }}" 
                                       class="px-3 py-2 bg-gray-500 text-white text-xs rounded hover:bg-gray-600"
                                       title="Gestisci categorie">
                                        ⚙️
                                    </a>
                                </div>
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

                    {{-- Images Section --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
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
                            
                            {{-- Upload Section --}}
                            @if($showImageUpload)
                                <div class="mb-6 p-4 border-2 border-dashed border-blue-300 rounded-lg">
                                    <div class="text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <div class="mt-4">
                                            <label class="cursor-pointer">
                                                <span class="mt-2 block text-sm font-medium text-gray-900">
                                                    Trascina le immagini qui o clicca per selezionare
                                                </span>
                                                <input type="file" 
                                                       wire:model="uploadedImages" 
                                                       multiple 
                                                       accept="image/*" 
                                                       class="sr-only">
                                            </label>
                                            <p class="mt-1 text-xs text-gray-500">
                                                PNG, JPG, WebP fino a 10MB • Drag & drop per riordinare
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Images Gallery con Drag & Drop --}}
                            @if($product->images && $product->images->count() > 0)
                                <div class="mb-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-sm font-medium text-gray-700">Trascina per riordinare • Clicca ⭐ per immagine principale</span>
                                        <span class="text-xs text-gray-500">{{ $product->images->count() }} immagini</span>
                                    </div>
                                    
                                    <div id="sortable-images" 
                                         class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"
                                         wire:ignore>
                                        @foreach($product->images->sortBy('sort_order') as $image)
                                            <div class="image-item relative group bg-gray-100 rounded-lg overflow-hidden aspect-square border-2 {{ $image->is_primary ? 'border-yellow-400 ring-2 ring-yellow-200' : 'border-gray-200' }}"
                                                 data-image-id="{{ $image->id }}">
                                                
                                                {{-- Drag Handle --}}
                                                <div class="absolute top-2 left-2 cursor-move opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                                                    <div class="bg-black bg-opacity-50 text-white p-1 rounded text-xs">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                
                                                {{-- Primary Image Indicator --}}
                                                @if($image->is_primary)
                                                    <div class="absolute top-2 right-2 z-10">
                                                        <div class="bg-yellow-400 text-yellow-900 px-2 py-1 rounded-full text-xs font-medium flex items-center">
                                                            ⭐ Principale
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                {{-- Image --}}
                                                <img src="{{ $image->aws_url }}" 
                                                     alt="{{ $image->alt_text ?: $product->name }}"
                                                     class="w-full h-full object-cover transition-transform duration-200 group-hover:scale-105">
                                                
                                                {{-- Overlay Controls --}}
                                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-200 flex items-center justify-center">
                                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex space-x-2">
                                                        
                                                        {{-- Set Primary Button --}}
                                                        @if(!$image->is_primary)
                                                            <button wire:click="setPrimaryImage({{ $image->id }})"
                                                                    class="bg-yellow-500 hover:bg-yellow-600 text-white p-2 rounded-full transition-colors duration-200"
                                                                    title="Imposta come principale">
                                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                                                </svg>
                                                            </button>
                                                        @endif
                                                        
                                                        {{-- Delete Button --}}
                                                        <button wire:click="deleteImage({{ $image->id }})"
                                                                wire:confirm="Sei sicuro di voler eliminare questa immagine?"
                                                                class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full transition-colors duration-200"
                                                                title="Elimina immagine">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                                
                                                {{-- Alt Text Input --}}
                                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-2">
                                                    <input type="text" 
                                                           value="{{ $image->alt_text }}"
                                                           placeholder="Alt text per SEO..."
                                                           class="w-full text-xs bg-white bg-opacity-90 border-0 rounded px-2 py-1 text-gray-900 placeholder-gray-600 focus:bg-opacity-100 focus:ring-1 focus:ring-blue-500"
                                                           onchange="updateAltText({{ $image->id }}, this.value)">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                
                                {{-- Sort Instructions --}}
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm text-blue-800">
                                    <div class="flex items-start space-x-2">
                                        <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                        <div>
                                            <strong>Suggerimenti:</strong>
                                            <ul class="mt-1 space-y-1">
                                                <li>• Trascina le immagini per riordinarle</li>
                                                <li>• Clicca ⭐ per impostare l'immagine principale</li>
                                                <li>• Aggiungi alt text per migliorare il SEO</li>
                                                <li>• L'immagine principale appare per prima nel catalogo</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6 6l-1-1m1 1l4 4m-4-4v6m0-6h6m-6 0l-1-1"/>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">Nessuna immagine caricata</p>
                                    <button wire:click="$set('showImageUpload', true)" 
                                            class="mt-2 inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-blue-600 hover:text-blue-500">
                                        Carica prima immagine
                                    </button>
                                </div>
                            @endif
                        </div>
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
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-medium text-gray-900">Tags</h3>
                                <div class="flex space-x-2">
                                    <button wire:click="$toggle('showTagForm')" 
                                            class="px-3 py-1 bg-green-600 text-white text-xs rounded-md hover:bg-green-700">
                                        {{ $showTagForm ? 'Annulla' : '+ Nuovo Tag' }}
                                    </button>
                                    <a href="{{ route('admin.tags.index') }}" 
                                       class="px-3 py-1 bg-gray-500 text-white text-xs rounded-md hover:bg-gray-600"
                                       title="Gestisci tutti i tag">
                                        ⚙️ Gestisci
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            
                            {{-- New Tag Form --}}
                            @if($showTagForm)
                                <div class="mb-4 p-3 border border-green-200 rounded-lg bg-green-50">
                                    <div class="flex space-x-2">
                                        <input type="text" 
                                               wire:model="newTagName"
                                               wire:keydown.enter="createTag"
                                               placeholder="Nome del nuovo tag"
                                               class="flex-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500">
                                        <button wire:click="createTag" 
                                                class="px-3 py-1 bg-green-600 text-white text-xs rounded-md hover:bg-green-700">
                                            Crea
                                        </button>
                                        <button wire:click="$set('showTagForm', false)" 
                                                class="px-3 py-1 bg-gray-400 text-white text-xs rounded-md hover:bg-gray-500">
                                            ✕
                                        </button>
                                    </div>
                                    @error('newTagName') 
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                                    @enderror
                                </div>
                            @endif
                            
                            {{-- Tags List --}}
                            @if($tags && $tags->count() > 0)
                                <div class="space-y-3">
                                    @foreach($tags as $tag)
                                        <div class="flex items-center justify-between group">
                                            <div class="flex items-center flex-1">
                                                <input type="checkbox" 
                                                       wire:model="selectedTags" 
                                                       wire:change="updateTags"
                                                       value="{{ $tag->id }}"
                                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                
                                                @if($editingTag === $tag->id)
                                                    <div class="ml-2 flex items-center space-x-2 flex-1">
                                                        <input type="text" 
                                                               wire:model="editTagName"
                                                               wire:keydown.enter="saveTag"
                                                               wire:keydown.escape="cancelTagEdit"
                                                               class="flex-1 text-sm border-blue-300 rounded border focus:border-blue-500 focus:ring-blue-500"
                                                               autofocus>
                                                        <button wire:click="saveTag" 
                                                                class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                                                            ✓
                                                        </button>
                                                        <button wire:click="cancelTagEdit" 
                                                                class="px-2 py-1 bg-gray-400 text-white text-xs rounded hover:bg-gray-500">
                                                            ✕
                                                        </button>
                                                    </div>
                                                    @error('editTagName') 
                                                        <span class="text-red-500 text-xs ml-2">{{ $message }}</span> 
                                                    @enderror
                                                @else
                                                    <span wire:click="startEditingTag({{ $tag->id }})" 
                                                          class="ml-2 text-sm text-gray-700 cursor-pointer hover:text-blue-600 hover:bg-blue-50 px-2 py-1 rounded transition-colors duration-200"
                                                          title="Clicca per modificare">
                                                        {{ $tag->name }}
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            @if($editingTag !== $tag->id)
                                                <div class="opacity-0 group-hover:opacity-100 flex space-x-1 transition-opacity duration-200">
                                                    <button wire:click="startEditingTag({{ $tag->id }})"
                                                            class="px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600"
                                                            title="Modifica tag">
                                                        ✏️
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <p class="text-sm text-gray-500">
                                        Nessun tag disponibile. 
                                        <button wire:click="$set('showTagForm', true)" 
                                                class="text-blue-600 hover:text-blue-800 underline">
                                            Crea il primo tag
                                        </button>
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
    // Existing events
    window.addEventListener('field-saved', event => {
        showToast('✅ Campo salvato: ' + event.detail.field, 'success');
    });
    
    window.addEventListener('status-changed', event => {
        showToast('🔄 Status cambiato: ' + event.detail.status, 'info');
    });
    
    // New events for tags and categories
    window.addEventListener('tag-created', event => {
        showToast('🏷️ Tag creato: ' + event.detail.name, 'success');
    });
    
    window.addEventListener('tag-deleted', event => {
        showToast('🗑️ Tag eliminato: ' + event.detail.name, 'warning');
    });
    
    window.addEventListener('category-created', event => {
        showToast('📁 Categoria creata: ' + event.detail.name, 'success');
    });
    
    window.addEventListener('category-deleted', event => {
        showToast('🗑️ Categoria eliminata: ' + event.detail.name, 'warning');
    });
    
    window.addEventListener('tag-updated', event => {
        showToast('✏️ Tag aggiornato: ' + event.detail.name, 'success');
    });
    
    // Simple toast function
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500', 
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        };
        
        toast.className = `fixed top-4 right-4 ${colors[type]} text-white px-4 py-2 rounded-md shadow-lg z-50 transform transition-all duration-300 translate-x-full`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        // Slide in
        setTimeout(() => toast.classList.remove('translate-x-full'), 100);
        
        // Slide out and remove
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => document.body.removeChild(toast), 300);
        }, 3000);
    }
</script>