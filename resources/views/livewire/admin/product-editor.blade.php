{{-- resources/views/livewire/admin/product-editor.blade.php --}}
{{-- ✨ MODERN BEAUTY SYSTEM - TALL STACK ✨ --}}

<div class="min-h-screen bg-gray-50">
    {{-- Toast Notifications Container --}}
    <div id="toast-container" class="fixed top-6 right-6 z-50 space-y-3"></div>

    {{-- Modern Header with Glass Effect --}}
    <div class="bg-white/80 backdrop-blur-sm border-b border-gray-200/60 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">Editor Prodotto</h1>
                            <p class="text-sm text-gray-500">Clicca su qualsiasi campo per modificarlo</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    {{-- Product Status Indicator --}}
                    <div class="flex items-center space-x-2 px-3 py-1.5 bg-gray-50 rounded-full">
                        <div class="w-2 h-2 rounded-full {{ $status === 'active' ? 'bg-green-400' : 'bg-gray-400' }}"></div>
                        <span class="text-xs font-medium text-gray-600">{{ ucfirst($status) }}</span>
                    </div>

                    {{-- Back Button --}}
                    <a href="{{ route('admin.products.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Torna alla Lista
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Quick Actions Toolbar --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="p-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-3">
                        {{-- Status Toggle --}}
                        <button wire:click="toggleStatus" 
                                class="group flex items-center space-x-3 px-4 py-2.5 bg-gradient-to-r from-gray-50 to-gray-100 hover:from-blue-50 hover:to-blue-100 rounded-lg border border-gray-200 hover:border-blue-300 transition-all duration-300">
                            <span class="text-sm font-medium text-gray-700 group-hover:text-blue-700">Status:</span>
                            <div>{!! $this->statusBadge !!}</div>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>

                        {{-- Featured Toggle --}}
                        <button wire:click="toggleFeatured" 
                                class="group flex items-center space-x-3 px-4 py-2.5 bg-gradient-to-r from-gray-50 to-gray-100 hover:from-yellow-50 hover:to-yellow-100 rounded-lg border border-gray-200 hover:border-yellow-300 transition-all duration-300">
                            <span class="text-sm font-medium text-gray-700 group-hover:text-yellow-700">In Evidenza:</span>
                            @if($is_featured)
                                <span class="px-2.5 py-1 text-xs font-semibold bg-gradient-to-r from-yellow-400 to-orange-400 text-white rounded-full shadow-sm">✨ SÌ</span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-medium bg-gray-200 text-gray-600 rounded-full">NO</span>
                            @endif
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                        <div class="flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            <span>ID: {{ $product->id }}</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ $product->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Grid Layout --}}
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
            
            {{-- Main Content Area (8/12) --}}
            <div class="xl:col-span-8 space-y-8">
                
                {{-- Basic Information Card --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Informazioni Base</h3>
                                <p class="text-sm text-gray-500">Dati principali del prodotto</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        {{-- Product Name --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Nome Prodotto</label>
                            @if($editingField === 'name')
                                <div class="flex items-center space-x-2">
                                    <input type="text" 
                                           wire:model="name"
                                           wire:keydown.enter="saveField('name')"
                                           wire:keydown.escape="stopEditing"
                                           class="flex-1 border-blue-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm"
                                           autofocus>
                                    <button wire:click="saveField('name')" 
                                            class="px-3 py-2 bg-green-500 text-white text-sm rounded-lg hover:bg-green-600 transition-colors">
                                        ✓
                                    </button>
                                    <button wire:click="stopEditing" 
                                            class="px-3 py-2 bg-gray-400 text-white text-sm rounded-lg hover:bg-gray-500 transition-colors">
                                        ✕
                                    </button>
                                </div>
                                @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            @else
                                <div wire:click="startEditing('name')" 
                                     class="text-xl font-bold text-gray-900 hover:bg-blue-50 hover:text-blue-700 p-3 rounded-lg cursor-pointer transition-all duration-200 border-2 border-transparent hover:border-blue-200">
                                    {{ $name ?: 'Clicca per aggiungere nome...' }}
                                </div>
                            @endif
                        </div>

                        {{-- SKU and Price Row --}}
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            {{-- SKU Field --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Codice SKU</label>
                                @if($editingField === 'sku')
                                    <div class="flex items-center space-x-2">
                                        <input type="text" 
                                               wire:model="sku"
                                               wire:keydown.enter="saveField('sku')"
                                               wire:keydown.escape="stopEditing"
                                               class="flex-1 border-blue-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm"
                                               autofocus>
                                        <button wire:click="saveField('sku')" 
                                                class="px-3 py-2 bg-green-500 text-white text-sm rounded-lg hover:bg-green-600 transition-colors">
                                            ✓
                                        </button>
                                        <button wire:click="stopEditing" 
                                                class="px-3 py-2 bg-gray-400 text-white text-sm rounded-lg hover:bg-gray-500 transition-colors">
                                            ✕
                                        </button>
                                    </div>
                                    @error('sku') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                @else
                                    <div wire:click="startEditing('sku')" 
                                         class="font-mono text-gray-600 hover:bg-blue-50 hover:text-blue-700 p-3 rounded-lg cursor-pointer transition-all duration-200 border-2 border-transparent hover:border-blue-200 bg-gray-50">
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
                                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">€</span>
                                            <input type="number" 
                                                   step="0.01"
                                                   wire:model="base_price"
                                                   wire:keydown.enter="saveField('base_price')"
                                                   wire:keydown.escape="stopEditing"
                                                   class="pl-8 border-blue-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm w-full"
                                                   autofocus>
                                        </div>
                                        <button wire:click="saveField('base_price')" 
                                                class="px-3 py-2 bg-green-500 text-white text-sm rounded-lg hover:bg-green-600 transition-colors">
                                            ✓
                                        </button>
                                        <button wire:click="stopEditing" 
                                                class="px-3 py-2 bg-gray-400 text-white text-sm rounded-lg hover:bg-gray-500 transition-colors">
                                            ✕
                                        </button>
                                    </div>
                                    @error('base_price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                @else
                                    <div wire:click="startEditing('base_price')" 
                                         class="text-2xl font-bold text-blue-600 hover:bg-blue-50 p-3 rounded-lg cursor-pointer transition-all duration-200 border-2 border-transparent hover:border-blue-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                                        €{{ number_format($base_price, 2, ',', '.') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Category Field --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Categoria</label>
                            <select wire:model="category_id" 
                                    wire:change="updateCategory"
                                    class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm">
                                <option value="">Seleziona categoria...</option>
                                @if($categories && $categories->count() > 0)
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('category_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Description Card --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Descrizione</h3>
                                <p class="text-sm text-gray-500">Descrizione dettagliata del prodotto</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        @if($editingField === 'description')
                            <div class="space-y-3">
                                <textarea wire:model="description"
                                          wire:keydown.escape="stopEditing"
                                          rows="8"
                                          class="w-full border-blue-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm"
                                          placeholder="Inserisci la descrizione del prodotto..."
                                          autofocus></textarea>
                                <div class="flex space-x-2">
                                    <button wire:click="saveField('description')" 
                                            class="px-4 py-2 bg-green-500 text-white text-sm rounded-lg hover:bg-green-600 transition-colors">
                                        Salva Descrizione
                                    </button>
                                    <button wire:click="stopEditing" 
                                            class="px-4 py-2 bg-gray-400 text-white text-sm rounded-lg hover:bg-gray-500 transition-colors">
                                        Annulla
                                    </button>
                                </div>
                                @error('description') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        @else
                            <div wire:click="startEditing('description')" 
                                 class="min-h-[120px] prose max-w-none hover:bg-blue-50 p-4 rounded-lg cursor-pointer transition-all duration-200 border-2 border-transparent hover:border-blue-200">
                                @if($description)
                                    {!! nl2br(e($description)) !!}
                                @else
                                    <span class="text-gray-400 italic">Clicca per aggiungere una descrizione...</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ✨ MODERN IMAGE MANAGEMENT SYSTEM ✨ --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6 6h.01M6 20h.01M18 20h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Gestione Immagini</h3>
                                    <p class="text-sm text-gray-500">
                                        {{ $product->image_count + $product->beauty_count }} immagini totali
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex space-x-3">
                                <button wire:click="openUploadModal('gallery')" 
                                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Immagine Prodotto
                                </button>
                                
                                <button wire:click="openUploadModal('beauty')" 
                                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white text-sm rounded-lg hover:from-purple-600 hover:to-pink-600 transition-all duration-200 shadow-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Immagine Sfondo
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6 space-y-8">
                        
                        {{-- Gallery Images Section --}}
                        <div>
                            <div class="flex justify-between items-center mb-6">
                                <div class="flex items-center space-x-3">
                                    <h4 class="text-lg font-semibold text-gray-900">🖼️ Immagini Prodotto</h4>
                                    <span class="px-3 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                        {{ $product->gallery_count }} immagini
                                    </span>
                                    @if($product->hasPrimaryImage())
                                        <span class="px-3 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                            ⭐ Primary impostata
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            @if($product->galleryImages && $product->galleryImages->count() > 0)
                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                                    @foreach($product->galleryImages as $image)
                                        @php $isPrimary = $product->isPrimaryImage($image); @endphp
                                        
                                        <div class="group relative bg-white border-2 rounded-xl overflow-hidden {{ $isPrimary ? 'border-yellow-400 ring-2 ring-yellow-200' : 'border-gray-200 hover:border-blue-300' }} transition-all duration-300">
                                            {{-- Primary Badge --}}
                                            @if($isPrimary)
                                                <div class="absolute top-3 left-3 z-20 px-2 py-1 bg-gradient-to-r from-yellow-400 to-orange-400 text-white text-xs rounded-full font-semibold shadow-lg">
                                                    ⭐ PRIMARY
                                                </div>
                                            @endif
                                            
                                            <div class="aspect-square relative overflow-hidden">
                                                <img src="{{ $image->url }}" 
                                                     alt="{{ $image->alt_text ?: $product->name }}"
                                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                
                                                {{-- Hover Actions --}}
                                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all duration-300">
                                                    <div class="absolute bottom-3 right-3 flex space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                                        @if(!$isPrimary)
                                                            <button wire:click="setPrimaryImage({{ $image->id }})"
                                                                    class="p-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors shadow-lg"
                                                                    title="Imposta come PRIMARY">
                                                                ⭐
                                                            </button>
                                                        @else
                                                            <button wire:click="clearPrimaryImage"
                                                                    class="p-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors shadow-lg"
                                                                    title="Rimuovi PRIMARY">
                                                                ⭐
                                                            </button>
                                                        @endif
                                                        
                                                        <button wire:click="deleteGalleryImage({{ $image->id }})"
                                                                wire:confirm="Eliminare questa immagine?"
                                                                class="p-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors shadow-lg"
                                                                title="Elimina">
                                                            🗑️
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            {{-- Image Info --}}
                                            <div class="p-3 bg-gray-50">
                                                <div class="text-xs font-medium text-gray-700 truncate">{{ $image->clean_name }}</div>
                                                <div class="text-xs text-gray-500 mt-1">{{ $image->formatted_size }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12 border-2 border-dashed border-gray-300 rounded-xl bg-gray-50">
                                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6 6l-1-1m1 1l4 4m-4-4v6m0-6h6m-6 0l-1-1"/>
                                    </svg>
                                    <h3 class="mt-4 text-lg font-medium text-gray-900">Nessuna immagine</h3>
                                    <p class="mt-2 text-sm text-gray-500">Carica le prime immagini del prodotto</p>
                                    <button wire:click="openUploadModal('gallery')" 
                                            class="mt-4 px-6 py-3 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 transition-colors">
                                        Aggiungi Prima Immagine
                                    </button>
                                </div>
                            @endif
                        </div>
                        
                        {{-- ✨ MODERN BEAUTY SYSTEM WITH CATEGORIES ✨ --}}
                        <div>
                            <div class="flex justify-between items-center mb-6">
                                <div class="flex items-center space-x-3">
                                    <h4 class="text-lg font-semibold text-gray-900">🎨 Immagini Sfondo/Marketing</h4>
                                    <span class="px-3 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded-full">
                                        {{ $product->beauty_count }} totali
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500">Organizza per categorie: Principale, Slideshow, Header</p>
                            </div>
                            
                            {{-- Beauty Categories Dashboard --}}
                            @if($product->beautyImages && $product->beauty_count > 0)
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                                    {{-- Main Background --}}
                                    <div class="bg-gradient-to-br from-orange-50 to-red-50 border-2 border-orange-200 rounded-xl p-6">
                                        <div class="flex justify-between items-center mb-4">
                                            <h5 class="font-semibold text-orange-900 flex items-center">
                                                🌅 Sfondo Principale
                                                <span class="ml-3 px-2 py-1 text-xs bg-orange-200 text-orange-800 rounded-full">
                                                    {{ $product->getBeautyByCategory('main')->count() }}
                                                </span>
                                            </h5>
                                        </div>
                                        
                                        @if($product->getBeautyByCategory('main')->count() > 0)
                                            <div class="grid grid-cols-2 gap-3">
                                                @foreach($product->getBeautyByCategory('main')->take(4) as $mainImage)
                                                    <div class="relative group">
                                                        <img src="{{ $mainImage->url }}" 
                                                             alt="{{ $mainImage->alt_text }}"
                                                             class="w-full h-20 object-cover rounded-lg border-2 border-orange-300">
                                                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all rounded-lg">
                                                            <div class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                                <button wire:click="removeFromBeautyCategory({{ $mainImage->id }})"
                                                                        class="p-1 bg-red-500 text-white rounded text-xs hover:bg-red-600"
                                                                        title="Rimuovi da Sfondo Principale">
                                                                    ✗
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-6">
                                                <div class="text-sm text-orange-600 font-medium">Nessuno sfondo principale</div>
                                                <div class="text-xs text-orange-500 mt-1">Hover sulle immagini sotto per aggiungere</div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    {{-- Slideshow --}}
                                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl p-6">
                                        <div class="flex justify-between items-center mb-4">
                                            <h5 class="font-semibold text-blue-900 flex items-center">
                                                🎬 Slideshow
                                                <span class="ml-3 px-2 py-1 text-xs bg-blue-200 text-blue-800 rounded-full">
                                                    {{ $product->getBeautyByCategory('slideshow')->count() }}
                                                </span>
                                            </h5>
                                        </div>
                                        
                                        @if($product->getBeautyByCategory('slideshow')->count() > 0)
                                            <div class="grid grid-cols-2 gap-3">
                                                @foreach($product->getBeautyByCategory('slideshow')->take(4) as $slideImage)
                                                    <div class="relative group">
                                                        <img src="{{ $slideImage->url }}" 
                                                             alt="{{ $slideImage->alt_text }}"
                                                             class="w-full h-20 object-cover rounded-lg border-2 border-blue-300">
                                                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all rounded-lg">
                                                            <div class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                                <button wire:click="removeFromBeautyCategory({{ $slideImage->id }})"
                                                                        class="p-1 bg-red-500 text-white rounded text-xs hover:bg-red-600"
                                                                        title="Rimuovi da Slideshow">
                                                                    ✗
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-6">
                                                <div class="text-sm text-blue-600 font-medium">Nessuna immagine slideshow</div>
                                                <div class="text-xs text-blue-500 mt-1">Hover sulle immagini sotto per aggiungere</div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    {{-- Header --}}
                                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl p-6">
                                        <div class="flex justify-between items-center mb-4">
                                            <h5 class="font-semibold text-green-900 flex items-center">
                                                📄 Header
                                                <span class="ml-3 px-2 py-1 text-xs bg-green-200 text-green-800 rounded-full">
                                                    {{ $product->getBeautyByCategory('header')->count() }}
                                                </span>
                                            </h5>
                                        </div>
                                        
                                        @if($product->getBeautyByCategory('header')->count() > 0)
                                            <div class="grid grid-cols-2 gap-3">
                                                @foreach($product->getBeautyByCategory('header')->take(4) as $headerImage)
                                                    <div class="relative group">
                                                        <img src="{{ $headerImage->url }}" 
                                                             alt="{{ $headerImage->alt_text }}"
                                                             class="w-full h-20 object-cover rounded-lg border-2 border-green-300">
                                                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all rounded-lg">
                                                            <div class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                                <button wire:click="removeFromBeautyCategory({{ $headerImage->id }})"
                                                                        class="p-1 bg-red-500 text-white rounded text-xs hover:bg-red-600"
                                                                        title="Rimuovi da Header">
                                                                    ✗
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-6">
                                                <div class="text-sm text-green-600 font-medium">Nessuna immagine header</div>
                                                <div class="text-xs text-green-500 mt-1">Hover sulle immagini sotto per aggiungere</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            
                            {{-- All Beauty Images with Category Assignment --}}
                            @if($product->beautyImages && $product->beautyImages->count() > 0)
                                <div>
                                    <h5 class="font-semibold text-gray-900 mb-4">🗂️ Tutte le Immagini Sfondo</h5>
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                                        @foreach($product->beautyImages as $beautyImage)
                                            <div class="group relative bg-white border-2 border-purple-200 hover:border-purple-400 rounded-xl overflow-hidden transition-all duration-300">
                                                {{-- Category Badge --}}
                                                <div class="absolute top-3 left-3 z-20">
                                                    @if($beautyImage->beauty_category === 'main')
                                                        <span class="px-2 py-1 bg-orange-500 text-white text-xs rounded-full font-semibold">🌅</span>
                                                    @elseif($beautyImage->beauty_category === 'slideshow')
                                                        <span class="px-2 py-1 bg-blue-500 text-white text-xs rounded-full font-semibold">🎬</span>
                                                    @elseif($beautyImage->beauty_category === 'header')
                                                        <span class="px-2 py-1 bg-green-500 text-white text-xs rounded-full font-semibold">📄</span>
                                                    @else
                                                        <span class="px-2 py-1 bg-gray-500 text-white text-xs rounded-full font-semibold">⚪</span>
                                                    @endif
                                                </div>
                                                
                                                <div class="aspect-video relative overflow-hidden">
                                                    <img src="{{ $beautyImage->url }}" 
                                                         alt="{{ $beautyImage->alt_text ?: $product->name . ' - Sfondo' }}"
                                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                    
                                                    {{-- Category Assignment Actions --}}
                                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/60 transition-all duration-300">
                                                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                                            <div class="flex flex-col space-y-2">
                                                                <button wire:click="assignBeautyCategory({{ $beautyImage->id }}, 'main')"
                                                                        class="px-3 py-1.5 bg-orange-500 text-white rounded-lg text-xs hover:bg-orange-600 flex items-center transition-colors">
                                                                    🌅 Principale
                                                                </button>
                                                                
                                                                <button wire:click="assignBeautyCategory({{ $beautyImage->id }}, 'slideshow')"
                                                                        class="px-3 py-1.5 bg-blue-500 text-white rounded-lg text-xs hover:bg-blue-600 flex items-center transition-colors">
                                                                    🎬 Slideshow
                                                                </button>
                                                                
                                                                <button wire:click="assignBeautyCategory({{ $beautyImage->id }}, 'header')"
                                                                        class="px-3 py-1.5 bg-green-500 text-white rounded-lg text-xs hover:bg-green-600 flex items-center transition-colors">
                                                                    📄 Header
                                                                </button>
                                                                
                                                                @if($beautyImage->beauty_category)
                                                                    <button wire:click="removeFromBeautyCategory({{ $beautyImage->id }})"
                                                                            class="px-3 py-1.5 bg-gray-500 text-white rounded-lg text-xs hover:bg-gray-600 flex items-center transition-colors">
                                                                        ⚪ Rimuovi
                                                                    </button>
                                                                @endif
                                                                
                                                                <button wire:click="deleteBeautyImage({{ $beautyImage->id }})"
                                                                        wire:confirm="Eliminare questo sfondo?"
                                                                        class="px-3 py-1.5 bg-red-500 text-white rounded-lg text-xs hover:bg-red-600 flex items-center transition-colors">
                                                                    🗑️ Elimina
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                {{-- Image Info --}}
                                                <div class="p-3 bg-gray-50">
                                                    <div class="text-xs font-medium text-gray-700 truncate">{{ $beautyImage->clean_name }}</div>
                                                    <div class="text-xs text-gray-500 mt-1">{{ $beautyImage->formatted_size }}</div>
                                                    @if($beautyImage->beauty_category)
                                                        <div class="text-xs text-purple-600 font-semibold mt-1">
                                                            {{ ucfirst($beautyImage->beauty_category) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-12 border-2 border-dashed border-purple-300 rounded-xl bg-purple-50">
                                    <svg class="mx-auto h-16 w-16 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 011 1v1a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1h3z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8H5z"/>
                                    </svg>
                                    <h3 class="mt-4 text-lg font-medium text-gray-900">Nessuna immagine sfondo</h3>
                                    <p class="mt-2 text-sm text-gray-500">Aggiungi immagini per sfondi, slideshow, header</p>
                                    <button wire:click="openUploadModal('beauty')" 
                                            class="mt-4 px-6 py-3 bg-purple-500 text-white text-sm rounded-lg hover:bg-purple-600 transition-colors">
                                        Aggiungi Primo Sfondo
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar (4/12) --}}
            <div class="xl:col-span-4 space-y-6">
                
                {{-- Statistics Card --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Statistiche</h3>
                                <p class="text-sm text-gray-500">Dati di riepilogo</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between items-center py-2">
                            <span class="text-sm font-medium text-gray-600">Immagini Totali</span>
                            <span class="text-lg font-bold text-gray-900">{{ $product->image_count }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-sm font-medium text-gray-600">⭐ Primary</span>
                            <span class="text-sm font-semibold {{ $product->hasPrimaryImage() ? 'text-green-600' : 'text-red-600' }}">
                                {{ $product->hasPrimaryImage() ? '✓ Impostata' : '✗ Mancante' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-sm font-medium text-gray-600">💄 Beauty</span>
                            <span class="text-lg font-bold text-purple-600">{{ $product->beauty_count }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-sm font-medium text-gray-600">🖼️ Gallery</span>
                            <span class="text-lg font-bold text-blue-600">{{ $product->gallery_count }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-sm font-medium text-gray-600">Tags</span>
                            <span class="text-lg font-bold text-gray-900">{{ $product->tags()->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-t border-gray-100 pt-4">
                            <span class="text-sm font-medium text-gray-600">Ultima modifica</span>
                            <span class="text-sm text-gray-500">{{ $product->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>

                {{-- Tags Management --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-teal-600 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Tags</h3>
                                <p class="text-sm text-gray-500">Etichette del prodotto</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        @if($tags && $tags->count() > 0)
                            <div class="space-y-3">
                                @foreach($tags as $tag)
                                    <label class="flex items-center group cursor-pointer">
                                        <input type="checkbox" 
                                               wire:model="selectedTags" 
                                               wire:change="updateTags"
                                               value="{{ $tag->id }}"
                                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-3 text-sm font-medium text-gray-700 group-hover:text-blue-600 transition-colors">{{ $tag->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <p class="mt-2 text-sm font-medium text-gray-500">Nessun tag disponibile</p>
                                <p class="text-xs text-gray-400 mt-1">Crea dei tag per organizzare i prodotti</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ✨ Beauty Quick Actions --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gradient-to-br from-pink-500 to-rose-600 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">💄 Azioni Rapide</h3>
                                <p class="text-sm text-gray-500">Strumenti per gestione beauty</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6 space-y-3">
                        @if($product->image_count > 0)
                            <button wire:click="setPrimaryToFirst" 
                                    class="w-full text-left px-4 py-3 text-sm font-medium text-gray-700 hover:text-yellow-700 hover:bg-yellow-50 rounded-lg border border-yellow-200 hover:border-yellow-300 transition-all duration-200">
                                ⭐ Prima → Primary
                            </button>
                            
                            <button wire:click="setBeautyToSecond" 
                                    class="w-full text-left px-4 py-3 text-sm font-medium text-gray-700 hover:text-purple-700 hover:bg-purple-50 rounded-lg border border-purple-200 hover:border-purple-300 transition-all duration-200">
                                💄 Seconda → Beauty
                            </button>
                            
                            @if($product->beauty_count > 0)
                                <button wire:click="clearAllBeauty" 
                                        class="w-full text-left px-4 py-3 text-sm font-medium text-gray-700 hover:text-red-700 hover:bg-red-50 rounded-lg border border-red-200 hover:border-red-300 transition-all duration-200">
                                    🗑️ Clear All Beauty
                                </button>
                            @endif
                            
                            <button wire:click="generateAltTexts" 
                                    class="w-full text-left px-4 py-3 text-sm font-medium text-gray-700 hover:text-blue-700 hover:bg-blue-50 rounded-lg border border-blue-200 hover:border-blue-300 transition-all duration-200">
                                🏷️ Genera Alt Text
                            </button>
                        @else
                            <div class="text-center py-6">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6 6h.01M6 20h.01M18 20h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="mt-2 text-sm font-medium text-gray-500">Carica delle immagini</p>
                                <p class="text-xs text-gray-400 mt-1">Per vedere le azioni disponibili</p>
                            </div>
                        @endif
                        
                        <button wire:click="optimizeAllImages" 
                                class="w-full text-left px-4 py-3 text-sm font-medium text-gray-700 hover:text-green-700 hover:bg-green-50 rounded-lg border border-green-200 hover:border-green-300 transition-all duration-200">
                            🚀 Ottimizza Immagini
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ✨ MODERN UPLOAD MODAL ✨ --}}
    @if($showUploadModal)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden">
                <div class="border-b border-gray-100 px-6 py-4">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br {{ $uploadMode === 'gallery' ? 'from-blue-500 to-blue-600' : 'from-purple-500 to-pink-500' }} rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6 6h.01M6 20h.01M18 20h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    @if($uploadMode === 'gallery')
                                        🖼️ Carica Immagini Prodotto
                                    @else
                                        🎨 Carica Immagini Sfondo
                                    @endif
                                </h3>
                                <p class="text-sm text-gray-500">
                                    @if($uploadMode === 'gallery')
                                        Immagini che mostrano il prodotto
                                    @else
                                        Per sfondi, slideshow e header
                                    @endif
                                </p>
                            </div>
                        </div>
                        <button wire:click="closeUploadModal" 
                                class="text-gray-400 hover:text-gray-600 p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-blue-400 hover:bg-blue-50/50 transition-all duration-200">
                        <svg class="mx-auto h-16 w-16 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        
                        <div class="mt-6">
                            <label class="cursor-pointer">
                                <span class="block text-lg font-semibold text-gray-900 mb-2">
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
                                       class="block w-full mt-3 text-sm text-gray-500 file:mr-4 file:py-3 file:px-6 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r {{ $uploadMode === 'gallery' ? 'file:from-blue-500 file:to-blue-600 file:text-white hover:file:from-blue-600 hover:file:to-blue-700' : 'file:from-purple-500 file:to-pink-500 file:text-white hover:file:from-purple-600 hover:file:to-pink-600' }} file:cursor-pointer file:transition-all file:duration-200">
                            </label>
                            
                            <div class="mt-4 space-y-2 text-sm text-gray-500">
                                @if($uploadMode === 'gallery')
                                    <p class="flex items-center justify-center"><span class="w-2 h-2 bg-blue-400 rounded-full mr-2"></span>Immagini che mostrano il prodotto</p>
                                    <p class="flex items-center justify-center"><span class="w-2 h-2 bg-yellow-400 rounded-full mr-2"></span>La prima diventerà automaticamente PRIMARY</p>
                                @else
                                    <p class="flex items-center justify-center"><span class="w-2 h-2 bg-purple-400 rounded-full mr-2"></span>Immagini per sfondi, slideshow, header</p>
                                    <p class="flex items-center justify-center"><span class="w-2 h-2 bg-pink-400 rounded-full mr-2"></span>Formato landscape consigliato</p>
                                    <p class="flex items-center justify-center"><span class="w-2 h-2 bg-indigo-400 rounded-full mr-2"></span>Assegna categorie dopo l'upload</p>
                                @endif
                                <p class="flex items-center justify-center"><span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>PNG, JPG, WebP fino a 10MB</p>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Loading State --}}
                    <div wire:loading wire:target="uploadedImages" class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl">
                        <div class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 text-blue-600 mr-3" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span class="text-blue-800 font-medium">Caricamento in corso...</span>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                    <button wire:click="closeUploadModal" 
                            class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200">
                        Chiudi
                    </button>
                </div>
            </div>
        </div>
    @endif


    {{-- ✨ MODERN LOADING OVERLAY ✨ --}}
    <div wire:loading.flex wire:target="saveField,updateCategory,updateTags,toggleStatus,toggleFeatured,markAsBeauty,markAsGallery,setPrimaryImage,clearPrimaryImage" 
         class="fixed inset-0 bg-black/30 backdrop-blur-sm z-50 items-center justify-center">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm w-full mx-4">
            <div class="flex flex-col items-center space-y-4">
                <div class="relative">
                    <div class="w-16 h-16 border-4 border-blue-200 rounded-full"></div>
                    <div class="w-16 h-16 border-4 border-blue-600 rounded-full border-t-transparent animate-spin absolute top-0 left-0"></div>
                </div>
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-900">Salvando...</h3>
                    <p class="text-sm text-gray-500 mt-1">Le modifiche vengono applicate</p>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')

{{-- ✨ MODERN JAVASCRIPT WITH ENHANCED TOAST SYSTEM ✨ --}}
<script>
    document.addEventListener('livewire:init', () => {
        // Event Listeners
        Livewire.on('toast', (event) => {
            const { message, type = 'success' } = event;
            showModernToast(message, type);
        });
        
        Livewire.on('field-saved', (event) => {
            console.log('Campo salvato:', event.field);
            showModernToast(`Campo "${event.field}" salvato con successo!`, 'success');
        });
        
        Livewire.on('status-changed', (event) => {
            console.log('Status cambiato:', event.status);
            showModernToast(`Status cambiato in: ${event.status}`, 'info');
        });

        // ✨ Beauty System Events ✨
        Livewire.on('beauty-marked', (event) => {
            console.log('Beauty image set:', event.imageId);
            showModernToast('Immagine marcata come Beauty! 💄', 'success');
        });
        
        Livewire.on('beauty-cleared', (event) => {
            console.log('Beauty cleared:', event.imageId);
            showModernToast('Beauty rimossa! 🗑️', 'info');
        });

        Livewire.on('category-assigned', (event) => {
            console.log('Category assigned:', event);
            showModernToast(`Categoria "${event.category}" assegnata! 🎯`, 'success');
        });

        Livewire.on('images-optimized', (event) => {
            console.log('Images optimized:', event);
            showModernToast('Immagini ottimizzate con successo! 🚀', 'success');
        });
    });

    function showModernToast(message, type = 'success') {
        const colors = {
            success: 'from-green-500 to-emerald-500',
            error: 'from-red-500 to-rose-500',
            info: 'from-blue-500 to-indigo-500',
            warning: 'from-yellow-500 to-orange-500'
        };

        const icons = {
            success: '✅',
            error: '❌', 
            info: 'ℹ️',
            warning: '⚠️'
        };
        
        const toast = document.createElement('div');
        toast.className = `bg-gradient-to-r ${colors[type]} text-white px-6 py-4 rounded-xl shadow-2xl mb-3 transform transition-all duration-500 translate-x-full border border-white/20 backdrop-blur-sm`;
        
        toast.innerHTML = `
            <div class="flex items-center space-x-3">
                <span class="text-lg">${icons[type]}</span>
                <span class="font-medium">${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-white/80 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
        
        const container = document.getElementById('toast-container');
        if (container) {
            container.appendChild(toast);
            
            // Animate in
            setTimeout(() => toast.classList.remove('translate-x-full'), 100);
            
            // Auto remove
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 500);
            }, 4000);
        }
    }

    // Enhanced smooth scroll behavior
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Auto-save indication
    let saveTimeout;
    document.addEventListener('input', function(e) {
        if (e.target.matches('[wire\\:model]')) {
            clearTimeout(saveTimeout);
            
            // Show save indicator
            const indicator = e.target.parentElement.querySelector('.save-indicator');
            if (indicator) {
                indicator.textContent = 'Salvando...';
                indicator.className = 'save-indicator text-xs text-blue-600';
            }
            
            saveTimeout = setTimeout(() => {
                if (indicator) {
                    indicator.textContent = 'Salvato ✓';
                    indicator.className = 'save-indicator text-xs text-green-600';
                    setTimeout(() => {
                        indicator.textContent = '';
                    }, 2000);
                }
            }, 1000);
        }
    });
</script>
@endpush


@push('styles')
{{-- ✨ MODERN STYLESHEET ✨ --}}
{{-- ✨ MODERN CSS ENHANCEMENTS ✨ --}}
<style>
    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: linear-gradient(to bottom, #3b82f6, #1d4ed8);
        border-radius: 3px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(to bottom, #1d4ed8, #1e40af);
    }

    /* Smooth focus transitions */
    input:focus, textarea:focus, select:focus {
        transition: all 0.2s ease-in-out;
    }

    /* Custom file input styling */
    input[type="file"]::-webkit-file-upload-button {
        transition: all 0.2s ease-in-out;
    }

    /* Enhanced hover effects */
    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }

    /* Glassmorphism effect */
    .glass {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    /* Loading animation */
    @keyframes shimmer {
        0% { background-position: -200px 0; }
        100% { background-position: calc(200px + 100%) 0; }
    }

    .shimmer {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200px 100%;
        animation: shimmer 1.5s infinite;
    }

    /* Modern gradient borders */
    .gradient-border {
        position: relative;
        background: linear-gradient(45deg, #3b82f6, #8b5cf6, #ec4899);
        padding: 2px;
        border-radius: 12px;
    }
    
    .gradient-border::before {
        content: '';
        position: absolute;
        inset: 2px;
        background: white;
        border-radius: 10px;
    }

    /* Enhanced focus states */
    .focus-ring:focus {
        outline: 2px solid transparent;
        outline-offset: 2px;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        border-color: #3b82f6;
    }

    /* Smooth transitions for all interactive elements */
    button, input, select, textarea, a {
        transition: all 0.2s ease-in-out;
    }

    /* Enhanced loading states */
    [wire\:loading] {
        opacity: 0.7;
        pointer-events: none;
    }

    [wire\:loading\.delay] {
        opacity: 1;
    }

    /* Modern card shadows */
    .card-shadow {
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }

    .card-shadow:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
</style>
@endpush

