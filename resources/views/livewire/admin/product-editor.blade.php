{{-- resources/views/livewire/admin/product-editor.blade.php --}}
{{-- ✨ BEAUTY SYSTEM COMPLETE VERSION ✨ --}}

<div>
    {{-- Toast Notifications Container --}}
    <div id="toast-container" class="fixed top-4 right-4 z-50"></div>

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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Quick Actions Bar --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
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

            {{-- Main Layout: Content + Sidebar --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                {{-- Main Content (2/3 width) --}}
                <div class="lg:col-span-8 space-y-6">
                    
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

                            {{-- Price and Category Row --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                                        @if($categories && $categories->count() > 0)
                            @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                        @endif
                                    </select>
                                    @error('category_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
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

                   {{-- SISTEMA BEAUTY CON CATEGORIE ✨ --}}

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                Gestione Immagini
                <span class="inline-flex items-center justify-center w-6 h-6 ml-2 text-xs font-bold text-white bg-blue-600 rounded-full">
                    {{ $product->image_count + $product->beauty_count }}
                </span>
            </h3>
            
            <div class="flex space-x-2">
                {{-- Bottone Aggiungi Immagine Normale --}}
                <button wire:click="openUploadModal('gallery')" 
                        class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Aggiungi Immagine
                </button>
                
                {{-- Bottone Aggiungi Beauty/Sfondo --}}
                <button wire:click="openUploadModal('beauty')" 
                        class="px-4 py-2 bg-purple-600 text-white text-sm rounded-md hover:bg-purple-700 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Aggiungi Sfondo
                </button>
            </div>
        </div>
    </div>
    
    <div class="p-6 space-y-8">
        
        {{-- GALLERY IMMAGINI NORMALI --}}
        <div>
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-lg font-medium text-gray-900 flex items-center">
                    🖼️ Immagini Prodotto
                    <span class="ml-2 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                        {{ $product->gallery_count }} immagini
                    </span>
                    @if($product->hasPrimaryImage())
                        <span class="ml-2 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">
                            ⭐ Primary impostata
                        </span>
                    @endif
                </h4>
            </div>
            
            @if($product->galleryImages && $product->galleryImages->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($product->galleryImages as $image)
                        @php $isPrimary = $product->isPrimaryImage($image); @endphp
                        
                        <div class="relative group border-2 rounded-lg overflow-hidden {{ $isPrimary ? 'border-yellow-400' : 'border-gray-200' }}">
                            {{-- Primary Badge --}}
                            @if($isPrimary)
                                <div class="absolute top-2 left-2 z-10 px-2 py-1 bg-yellow-500 text-white text-xs rounded-full font-medium">
                                    ⭐ PRIMARY
                                </div>
                            @endif
                            
                            <div class="aspect-square">
                                <img src="{{ $image->url }}" 
                                     alt="{{ $image->alt_text ?: $product->name }}"
                                     class="w-full h-full object-cover">
                            </div>
                            
                            {{-- Actions on Hover --}}
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all">
                                <div class="absolute bottom-2 right-2 flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    {{-- Set Primary --}}
                                    @if(!$isPrimary)
                                        <button wire:click="setPrimaryImage({{ $image->id }})"
                                                class="p-2 bg-yellow-500 text-white rounded hover:bg-yellow-600"
                                                title="Imposta come PRIMARY">
                                            ⭐
                                        </button>
                                    @else
                                        <button wire:click="clearPrimaryImage"
                                                class="p-2 bg-gray-500 text-white rounded hover:bg-gray-600"
                                                title="Rimuovi PRIMARY">
                                            ⭐
                                        </button>
                                    @endif
                                    
                                    {{-- Delete --}}
                                    <button wire:click="deleteGalleryImage({{ $image->id }})"
                                            wire:confirm="Eliminare questa immagine?"
                                            class="p-2 bg-red-600 text-white rounded hover:bg-red-700"
                                            title="Elimina">
                                        🗑️
                                    </button>
                                </div>
                            </div>
                            
                            {{-- Image Info --}}
                            <div class="p-2 bg-white">
                                <div class="text-xs text-gray-600 truncate">{{ $image->clean_name }}</div>
                                <div class="text-xs text-gray-500">{{ $image->formatted_size }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 border-2 border-dashed border-gray-300 rounded-lg">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6 6l-1-1m1 1l4 4m-4-4v6m0-6h6m-6 0l-1-1"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Nessuna immagine</h3>
                    <p class="mt-1 text-sm text-gray-500">Carica le prime immagini del prodotto</p>
                    <button wire:click="openUploadModal('gallery')" 
                            class="mt-3 px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                        Aggiungi Prima Immagine
                    </button>
                </div>
            @endif
        </div>
        
        {{-- ✨ BEAUTY CON CATEGORIE ✨ --}}
        <div>
            <div class="flex justify-between items-center mb-6">
                <h4 class="text-lg font-medium text-gray-900 flex items-center">
                    🎨 Immagini Sfondo/Marketing
                    <span class="ml-2 px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded-full">
                        {{ $product->beauty_count }} totali
                    </span>
                </h4>
                <p class="text-xs text-gray-500">Organizza per: Sfondo Principale, Slideshow, Header</p>
            </div>
            
            {{-- Beauty Categories Dashboard --}}
            @if($product->beautyImages && $product->beauty_count > 0)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    {{-- Sfondo Principale --}}
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 border border-orange-200 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-3">
                            <h5 class="font-medium text-orange-900 flex items-center">
                                🌅 Sfondo Principale
                                <span class="ml-2 px-2 py-1 text-xs bg-orange-100 text-orange-800 rounded-full">
                                    {{ $product->getBeautyByCategory('main')->count() }}
                                </span>
                            </h5>
                        </div>
                        
                        @if($product->getBeautyByCategory('main')->count() > 0)
                            <div class="grid grid-cols-2 gap-2">
                                @foreach($product->getBeautyByCategory('main')->take(4) as $mainImage)
                                    <div class="relative group">
                                        <img src="{{ $mainImage->url }}" 
                                             alt="{{ $mainImage->alt_text }}"
                                             class="w-full h-16 object-cover rounded border border-orange-300">
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all rounded">
                                            <div class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button wire:click="removeFromBeautyCategory({{ $mainImage->id }})"
                                                        class="p-1 bg-red-600 text-white rounded text-xs hover:bg-red-700"
                                                        title="Rimuovi da Sfondo Principale">
                                                    ✗
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="text-xs text-orange-600">Nessuno sfondo principale</div>
                                <div class="text-xs text-orange-500 mt-1">Hover sulle immagini sotto per aggiungere</div>
                            </div>
                        @endif
                    </div>
                    
                    {{-- Slideshow --}}
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-3">
                            <h5 class="font-medium text-blue-900 flex items-center">
                                🎬 Slideshow
                                <span class="ml-2 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                                    {{ $product->getBeautyByCategory('slideshow')->count() }}
                                </span>
                            </h5>
                        </div>
                        
                        @if($product->getBeautyByCategory('slideshow')->count() > 0)
                            <div class="grid grid-cols-2 gap-2">
                                @foreach($product->getBeautyByCategory('slideshow')->take(4) as $slideImage)
                                    <div class="relative group">
                                        <img src="{{ $slideImage->url }}" 
                                             alt="{{ $slideImage->alt_text }}"
                                             class="w-full h-16 object-cover rounded border border-blue-300">
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all rounded">
                                            <div class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button wire:click="removeFromBeautyCategory({{ $slideImage->id }})"
                                                        class="p-1 bg-red-600 text-white rounded text-xs hover:bg-red-700"
                                                        title="Rimuovi da Slideshow">
                                                    ✗
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="text-xs text-blue-600">Nessuna immagine slideshow</div>
                                <div class="text-xs text-blue-500 mt-1">Hover sulle immagini sotto per aggiungere</div>
                            </div>
                        @endif
                    </div>
                    
                    {{-- Header --}}
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-3">
                            <h5 class="font-medium text-green-900 flex items-center">
                                📄 Header
                                <span class="ml-2 px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                                    {{ $product->getBeautyByCategory('header')->count() }}
                                </span>
                            </h5>
                        </div>
                        
                        @if($product->getBeautyByCategory('header')->count() > 0)
                            <div class="grid grid-cols-2 gap-2">
                                @foreach($product->getBeautyByCategory('header')->take(4) as $headerImage)
                                    <div class="relative group">
                                        <img src="{{ $headerImage->url }}" 
                                             alt="{{ $headerImage->alt_text }}"
                                             class="w-full h-16 object-cover rounded border border-green-300">
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all rounded">
                                            <div class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button wire:click="removeFromBeautyCategory({{ $headerImage->id }})"
                                                        class="p-1 bg-red-600 text-white rounded text-xs hover:bg-red-700"
                                                        title="Rimuovi da Header">
                                                    ✗
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="text-xs text-green-600">Nessuna immagine header</div>
                                <div class="text-xs text-green-500 mt-1">Hover sulle immagini sotto per aggiungere</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
            
            {{-- Tutte le Beauty Images con Assegnazione Categorie --}}
            @if($product->beautyImages && $product->beautyImages->count() > 0)
                <div>
                    <h5 class="font-medium text-gray-900 mb-3">🗂️ Tutte le Immagini Sfondo (hover per assegnare categorie)</h5>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($product->beautyImages as $beautyImage)
                            <div class="relative group border-2 border-purple-200 rounded-lg overflow-hidden">
                                {{-- Category Badges --}}
                                <div class="absolute top-2 left-2 z-10 flex flex-col space-y-1">
                                    @if($beautyImage->beauty_category === 'main')
                                        <span class="px-2 py-1 bg-orange-500 text-white text-xs rounded-full font-medium">🌅</span>
                                    @endif
                                    @if($beautyImage->beauty_category === 'slideshow')
                                        <span class="px-2 py-1 bg-blue-500 text-white text-xs rounded-full font-medium">🎬</span>
                                    @endif
                                    @if($beautyImage->beauty_category === 'header')
                                        <span class="px-2 py-1 bg-green-500 text-white text-xs rounded-full font-medium">📄</span>
                                    @endif
                                    @if(!$beautyImage->beauty_category)
                                        <span class="px-2 py-1 bg-gray-500 text-white text-xs rounded-full font-medium">⚪</span>
                                    @endif
                                </div>
                                
                                <div class="aspect-video">
                                    <img src="{{ $beautyImage->url }}" 
                                         alt="{{ $beautyImage->alt_text ?: $product->name . ' - Sfondo' }}"
                                         class="w-full h-full object-cover">
                                </div>
                                
                                {{-- Category Assignment Actions on Hover --}}
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-60 transition-all">
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <div class="flex flex-col space-y-2">
                                            {{-- Assign to Main --}}
                                            <button wire:click="assignBeautyCategory({{ $beautyImage->id }}, 'main')"
                                                    class="px-3 py-2 bg-orange-500 text-white rounded text-xs hover:bg-orange-600 flex items-center"
                                                    title="Assegna a Sfondo Principale">
                                                🌅 Principale
                                            </button>
                                            
                                            {{-- Assign to Slideshow --}}
                                            <button wire:click="assignBeautyCategory({{ $beautyImage->id }}, 'slideshow')"
                                                    class="px-3 py-2 bg-blue-500 text-white rounded text-xs hover:bg-blue-600 flex items-center"
                                                    title="Assegna a Slideshow">
                                                🎬 Slideshow
                                            </button>
                                            
                                            {{-- Assign to Header --}}
                                            <button wire:click="assignBeautyCategory({{ $beautyImage->id }}, 'header')"
                                                    class="px-3 py-2 bg-green-500 text-white rounded text-xs hover:bg-green-600 flex items-center"
                                                    title="Assegna a Header">
                                                📄 Header
                                            </button>
                                            
                                            {{-- Remove Category --}}
                                            @if($beautyImage->beauty_category)
                                                <button wire:click="removeFromBeautyCategory({{ $beautyImage->id }})"
                                                        class="px-3 py-2 bg-gray-500 text-white rounded text-xs hover:bg-gray-600 flex items-center"
                                                        title="Rimuovi Categoria">
                                                    ⚪ Rimuovi
                                                </button>
                                            @endif
                                            
                                            {{-- Delete --}}
                                            <button wire:click="deleteBeautyImage({{ $beautyImage->id }})"
                                                    wire:confirm="Eliminare questo sfondo?"
                                                    class="px-3 py-2 bg-red-600 text-white rounded text-xs hover:bg-red-700 flex items-center"
                                                    title="Elimina">
                                                🗑️ Elimina
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Image Info --}}
                                <div class="p-2 bg-white">
                                    <div class="text-xs text-gray-600 truncate">{{ $beautyImage->clean_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $beautyImage->formatted_size }}</div>
                                    @if($beautyImage->beauty_category)
                                        <div class="text-xs text-purple-600 font-medium mt-1">
                                            {{ ucfirst($beautyImage->beauty_category) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-center py-8 border-2 border-dashed border-purple-300 rounded-lg bg-purple-50">
                    <svg class="mx-auto h-12 w-12 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 011 1v1a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1h3z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8H5z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Nessuna immagine sfondo</h3>
                    <p class="mt-1 text-sm text-gray-500">Aggiungi immagini per sfondi, slideshow, header</p>
                    <button wire:click="openUploadModal('beauty')" 
                            class="mt-3 px-4 py-2 bg-purple-600 text-white text-sm rounded-md hover:bg-purple-700">
                        Aggiungi Primo Sfondo
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- MODAL UPLOAD (uguale a prima) --}}
@if($showUploadModal)
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full m-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">
                        @if($uploadMode === 'gallery')
                            🖼️ Carica Immagini Prodotto
                        @else
                            🎨 Carica Immagini Sfondo
                        @endif
                    </h3>
                    <button wire:click="closeUploadModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    
                    <div class="mt-4">
                        <label class="cursor-pointer">
                            <span class="mt-2 block text-sm font-medium text-gray-900">
                                @if($uploadMode === 'gallery')
                                    Seleziona immagini del prodotto
                                @else
                                    Seleziona immagini per sfondi/marketing
                                @endif
                            </span>
                            <input type="file" 
                                   wire:model="uploadedImages" 
                                   multiple 
                                   accept="image/*" 
                                   class="block w-full mt-2 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200">
                        </label>
                        
                        <div class="mt-3 text-xs text-gray-500">
                            @if($uploadMode === 'gallery')
                                <p>• Immagini che mostrano il prodotto</p>
                                <p>• La prima diventerà automaticamente PRIMARY</p>
                            @else
                                <p>• Immagini per sfondi, slideshow, header</p>
                                <p>• Formato landscape consigliato</p>
                                <p>• Assegna categorie dopo l'upload</p>
                            @endif
                            <p>• PNG, JPG, WebP fino a 10MB</p>
                        </div>
                    </div>
                </div>
                
                {{-- Loading State --}}
                <div wire:loading wire:target="uploadedImages" class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center">
                        <svg class="animate-spin h-4 w-4 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span class="text-blue-800 text-sm">Caricamento in corso...</span>
                    </div>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                <button wire:click="closeUploadModal" 
                        class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Chiudi
                </button>
            </div>
        </div>
    </div>
@endif


<button wire:click="debugImage(13)" class="bg-red-500 text-white p-2">
    DEBUG IMAGE 13
</button>

                {{-- Sidebar (1/3 width) --}}
                <div class="lg:col-span-4 space-y-6">
                    
                    {{-- Quick Stats --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Statistiche</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Immagini Totali</span>
                                <span class="font-semibold">{{ $product->image_count }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">⭐ Primary</span>
                                <span class="font-semibold {{ $product->hasPrimaryImage() ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $product->hasPrimaryImage() ? '✓ Impostata' : '✗ Mancante' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">💄 Beauty</span>
                                <span class="font-semibold text-purple-600">{{ $product->beauty_count }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">🖼️ Gallery</span>
                                <span class="font-semibold text-blue-600">{{ $product->gallery_count }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Tags</span>
                                <span class="font-semibold">{{ $product->tags()->count() }}</span>
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

                    {{-- ✨ BEAUTY QUICK ACTIONS SIDEBAR ✨ --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">💄 Beauty Actions</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            @if($product->image_count > 0)
                                <button wire:click="setPrimaryToFirst" 
                                        class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-yellow-50 rounded border border-yellow-200">
                                    ⭐ Prima → Primary
                                </button>
                                <button wire:click="setBeautyToSecond" 
                                        class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-purple-50 rounded border border-purple-200">
                                    💄 Seconda → Beauty
                                </button>
                                @if($product->beauty_count > 0)
                                    <button wire:click="clearAllBeauty" 
                                            class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-red-50 rounded border border-red-200">
                                        🗑️ Clear All Beauty
                                    </button>
                                @endif
                                <button wire:click="generateAltTexts" 
                                        class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-blue-50 rounded border border-blue-200">
                                    🏷️ Genera Alt Text
                                </button>
                            @else
                                <p class="text-sm text-gray-500 text-center py-4">
                                    Carica delle immagini per vedere le azioni disponibili
                                </p>
                            @endif
                            <button wire:click="optimizeAllImages" 
                                    class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-green-50 rounded border border-green-200">
                                🚀 Ottimizza Immagini
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Loading States --}}
    <div wire:loading.flex wire:target="saveField,updateCategory,updateTags,toggleStatus,toggleFeatured,markAsBeauty,markAsGallery,setPrimaryImage,clearPrimaryImage" 
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

{{-- JavaScript --}}
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('toast', (event) => {
            const { message, type = 'success' } = event;
            showToast(message, type);
        });
        
        Livewire.on('field-saved', (event) => {
            console.log('Campo salvato:', event.field);
        });
        
        Livewire.on('status-changed', (event) => {
            console.log('Status cambiato:', event.status);
        });

        // ✨ Beauty System Events ✨
        Livewire.on('beauty-marked', (event) => {
            console.log('Beauty image set:', event.imageId);
            showToast('Immagine marcata come Beauty!', 'success');
        });
        
        Livewire.on('beauty-cleared', (event) => {
            console.log('Beauty cleared:', event.imageId);
            showToast('Beauty rimossa!', 'info');
        });
    });

    function showToast(message, type = 'success') {
        const colors = {
            success: 'bg-green-600',
            error: 'bg-red-600',
            info: 'bg-blue-600',
            warning: 'bg-yellow-600'
        };
        
        const toast = document.createElement('div');
        toast.className = `${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg mb-2 transform transition-all duration-300 translate-x-full`;
        toast.textContent = message;
        
        const container = document.getElementById('toast-container');
        if (container) {
            container.appendChild(toast);
            
            setTimeout(() => toast.classList.remove('translate-x-full'), 100);
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    }
</script>