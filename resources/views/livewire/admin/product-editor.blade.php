{{-- resources/views/livewire/admin/product-editor.blade.php --}}
{{-- CLEAN DESIGN SYSTEM - TALL STACK --}}

<div class="min-h-screen bg-gray-50">
    
    {{-- Toast Notifications Container --}}
    <div id="toast-container" class="fixed top-6 right-6 z-50 space-y-3"></div>

    {{-- Clean Header --}}
    <div class="bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-semibold text-gray-900">Editor Prodotto</h1>
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
                       class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-50 transition-colors">
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
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
            <div class="p-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-3">
                        {{-- Status Toggle --}}
                        <button wire:click="toggleStatus" 
                                class="group flex items-center space-x-3 px-4 py-2.5 bg-gray-50 hover:bg-blue-50 rounded-lg border border-gray-200 hover:border-blue-300 transition-colors">
                            <span class="text-sm font-medium text-gray-700 group-hover:text-blue-700">Status:</span>
                            <div>{!! $this->statusBadge !!}</div>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>

                        {{-- Featured Toggle --}}
                        <button wire:click="toggleFeatured" 
                                class="group flex items-center space-x-3 px-4 py-2.5 bg-gray-50 hover:bg-yellow-50 rounded-lg border border-gray-200 hover:border-yellow-300 transition-colors">
                            <span class="text-sm font-medium text-gray-700 group-hover:text-yellow-700">In Evidenza:</span>
                            @if($is_featured)
                                <span class="px-2.5 py-1 text-xs font-semibold bg-yellow-500 text-white rounded-full">SI</span>
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
        <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
            
            {{-- Main Content Area (3/4) --}}
            <div class="xl:col-span-3 space-y-8">
                
                {{-- Basic Information Card --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h3 class="text-lg font-medium text-gray-900">Informazioni Base</h3>
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
                                           class="flex-1 border-blue-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg"
                                           autofocus>
                                    <button wire:click="saveField('name')" 
                                            class="px-3 py-2 bg-green-500 text-white text-sm rounded-lg hover:bg-green-600 transition-colors">
                                        Salva
                                    </button>
                                    <button wire:click="stopEditing" 
                                            class="px-3 py-2 bg-gray-400 text-white text-sm rounded-lg hover:bg-gray-500 transition-colors">
                                        Annulla
                                    </button>
                                </div>
                                @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            @else
                                <div wire:click="startEditing('name')" 
                                     class="text-xl font-semibold text-gray-900 hover:bg-blue-50 hover:text-blue-700 p-3 rounded-lg cursor-pointer transition-colors border border-transparent hover:border-blue-200">
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
                                               class="flex-1 border-blue-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg"
                                               autofocus>
                                        <button wire:click="saveField('sku')" 
                                                class="px-3 py-2 bg-green-500 text-white text-sm rounded-lg hover:bg-green-600 transition-colors">
                                            Salva
                                        </button>
                                        <button wire:click="stopEditing" 
                                                class="px-3 py-2 bg-gray-400 text-white text-sm rounded-lg hover:bg-gray-500 transition-colors">
                                            Annulla
                                        </button>
                                    </div>
                                    @error('sku') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                @else
                                    <div wire:click="startEditing('sku')" 
                                         class="font-mono text-gray-600 hover:bg-blue-50 hover:text-blue-700 p-3 rounded-lg cursor-pointer transition-colors border border-transparent hover:border-blue-200 bg-gray-50">
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
                                                   class="pl-8 border-blue-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg w-full"
                                                   autofocus>
                                        </div>
                                        <button wire:click="saveField('base_price')" 
                                                class="px-3 py-2 bg-green-500 text-white text-sm rounded-lg hover:bg-green-600 transition-colors">
                                            Salva
                                        </button>
                                        <button wire:click="stopEditing" 
                                                class="px-3 py-2 bg-gray-400 text-white text-sm rounded-lg hover:bg-gray-500 transition-colors">
                                            Annulla
                                        </button>
                                    </div>
                                    @error('base_price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                @else
                                    <div wire:click="startEditing('base_price')" 
                                         class="text-2xl font-semibold text-blue-600 hover:bg-blue-50 p-3 rounded-lg cursor-pointer transition-colors border border-transparent hover:border-blue-200 bg-blue-50">
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
                                    class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg">
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
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h3 class="text-lg font-medium text-gray-900">Descrizione</h3>
                    </div>
                    
                    <div class="p-6">
                        @if($editingField === 'description')
                            <div class="space-y-3">
                                <textarea wire:model="description"
                                          wire:keydown.escape="stopEditing"
                                          rows="8"
                                          class="w-full border-blue-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg"
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
                                 class="min-h-[120px] prose max-w-none hover:bg-blue-50 p-4 rounded-lg cursor-pointer transition-colors border border-transparent hover:border-blue-200">
                                @if($description)
                                    {!! nl2br(e($description)) !!}
                                @else
                                    <span class="text-gray-400 italic">Clicca per aggiungere una descrizione...</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Image Management System --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900">Gestione Immagini</h3>
                            <div class="flex flex-wrap gap-3">
                                {{-- Upload Buttons --}}
                                <button wire:click="openUploadModal('gallery')" 
                                        class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Upload Immagine Prodotto
                                </button>
                                
                                <button wire:click="openUploadModal('beauty')" 
                                        class="inline-flex items-center px-4 py-2 bg-purple-500 text-white text-sm rounded-lg hover:bg-purple-600 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Upload Immagine Sfondo
                                </button>

                                {{-- Picker Buttons --}}
                                <button wire:click="openImagePicker('gallery')" 
                                        class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 text-sm rounded-lg hover:bg-blue-200 transition-colors border border-blue-300">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6 6h.01M6 20h.01M18 20h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Seleziona Immagine
                                </button>
                                
                                <button wire:click="openImagePicker('beauty')" 
                                        class="inline-flex items-center px-4 py-2 bg-purple-100 text-purple-700 text-sm rounded-lg hover:bg-purple-200 transition-colors border border-purple-300">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6 6h.01M6 20h.01M18 20h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Seleziona Sfondo
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6 space-y-8">
                        
                        {{-- Gallery Images Section --}}
                        <div>
                            <div class="flex justify-between items-center mb-6">
                                <div class="flex items-center space-x-3">
                                    <h4 class="text-lg font-medium text-gray-900">Immagini Prodotto</h4>
                                    <span class="px-3 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                        {{ $product->gallery_count }} immagini
                                    </span>
                                    @if($product->hasPrimaryImage())
                                        <span class="px-3 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                            Primary impostata
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            @if($product->galleryImages && $product->galleryImages->count() > 0)
                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                                    @foreach($product->galleryImages as $image)
                                        @php $isPrimary = $product->isPrimaryImage($image); @endphp
                                        
                                        <div class="group relative bg-white border rounded-lg overflow-hidden {{ $isPrimary ? 'ring-2 ring-yellow-400' : 'border-gray-200 hover:border-blue-300' }} transition-all">
                                            {{-- Primary Badge --}}
                                            @if($isPrimary)
                                                <div class="absolute top-2 left-2 z-20 px-2 py-1 bg-yellow-500 text-white text-xs rounded-full font-medium">
                                                    PRIMARY
                                                </div>
                                            @endif
                                            
                                            <div class="aspect-square relative overflow-hidden">
                                                <img src="{{ $image->url }}" 
                                                     alt="{{ $image->alt_text ?: $product->name }}"
                                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                
                                                {{-- Hover Actions --}}
                                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300">
                                                    <div class="absolute bottom-3 right-3 flex space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                                        @if(!$isPrimary)
                                                            <button wire:click="setPrimaryImage({{ $image->id }})"
                                                                    class="p-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors"
                                                                    title="Imposta come PRIMARY">
                                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                                                </svg>
                                                            </button>
                                                        @else
                                                            <button wire:click="clearPrimaryImage"
                                                                    class="p-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
                                                                    title="Rimuovi PRIMARY">
                                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                                                </svg>
                                                            </button>
                                                        @endif
                                                        
                                                        <button wire:click="deleteGalleryImage({{ $image->id }})"
                                                                wire:confirm="Eliminare questa immagine?"
                                                                class="p-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors"
                                                                title="Elimina">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
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
                                <div class="text-center py-12 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50">
                                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6 6l-1-1m1 1l4 4m-4-4v6m0-6h6m-6 0l-1-1"/>
                                    </svg>
                                    <h3 class="mt-4 text-lg font-medium text-gray-900">Nessuna immagine</h3>
                                    <p class="mt-2 text-sm text-gray-500">Carica le prime immagini del prodotto</p>
                                    <div class="mt-4 flex justify-center space-x-3">
                                        <button wire:click="openUploadModal('gallery')" 
                                                class="px-6 py-3 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 transition-colors">
                                            Upload Immagine
                                        </button>
                                        <button wire:click="openImagePicker('gallery')" 
                                                class="px-6 py-3 bg-blue-100 text-blue-700 text-sm rounded-lg hover:bg-blue-200 transition-colors border border-blue-300">
                                            Seleziona da Libreria
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Beauty System with Categories --}}
                        <div>
                            <div class="flex justify-between items-center mb-6">
                                <div class="flex items-center space-x-3">
                                    <h4 class="text-lg font-medium text-gray-900">Immagini Sfondo/Marketing</h4>
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
                                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-6">
                                        <div class="flex justify-between items-center mb-4">
                                            <h5 class="font-medium text-orange-900 flex items-center">
                                                Sfondo Principale
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
                                                             class="w-full h-20 object-cover rounded-lg border border-orange-300">
                                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all rounded-lg">
                                                            <div class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                                <button wire:click="removeFromBeautyCategory({{ $mainImage->id }})"
                                                                        class="p-1 bg-red-500 text-white rounded text-xs hover:bg-red-600"
                                                                        title="Rimuovi da Sfondo Principale">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-6">
                                                <div class="text-sm text-orange-600 font-medium">Nessuno sfondo principale</div>
                                                <div class="text-xs text-orange-500 mt-1 mb-3">Hover sulle immagini sotto per aggiungere</div>
                                                <button wire:click="openImagePicker('beauty')" 
                                                        class="px-3 py-1.5 bg-orange-100 text-orange-700 text-xs rounded-lg hover:bg-orange-200 transition-colors border border-orange-300">
                                                    Seleziona da Libreria
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    {{-- Slideshow --}}
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                                        <div class="flex justify-between items-center mb-4">
                                            <h5 class="font-medium text-blue-900 flex items-center">
                                                Slideshow
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
                                                             class="w-full h-20 object-cover rounded-lg border border-blue-300">
                                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all rounded-lg">
                                                            <div class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                                <button wire:click="removeFromBeautyCategory({{ $slideImage->id }})"
                                                                        class="p-1 bg-red-500 text-white rounded text-xs hover:bg-red-600"
                                                                        title="Rimuovi da Slideshow">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-6">
                                                <div class="text-sm text-gray-600 font-medium">Nessuna immagine slideshow</div>
                                                <div class="text-xs text-gray-500 mt-1 mb-3">Hover sulle immagini sotto per aggiungere</div>
                                                <button wire:click="openImagePicker('beauty')" 
                                                        class="px-3 py-1.5 bg-blue-100 text-blue-700 text-xs rounded-lg hover:bg-blue-200 transition-colors border border-blue-300">
                                                    Seleziona da Libreria
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    {{-- Header --}}
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                                        <div class="flex justify-between items-center mb-4">
                                            <h5 class="font-medium text-green-900 flex items-center">
                                                Header
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
                                                             class="w-full h-20 object-cover rounded-lg border border-green-300">
                                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all rounded-lg">
                                                            <div class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                                <button wire:click="removeFromBeautyCategory({{ $headerImage->id }})"
                                                                        class="p-1 bg-red-500 text-white rounded text-xs hover:bg-red-600"
                                                                        title="Rimuovi da Header">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-6">
                                                <div class="text-sm text-gray-600 font-medium">Nessuna immagine header</div>
                                                <div class="text-xs text-gray-500 mt-1 mb-3">Hover sulle immagini sotto per aggiungere</div>
                                                <button wire:click="openImagePicker('beauty')" 
                                                        class="px-3 py-1.5 bg-green-100 text-green-700 text-xs rounded-lg hover:bg-green-200 transition-colors border border-green-300">
                                                    Seleziona da Libreria
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            
                            {{-- All Beauty Images with Category Assignment --}}
                            @if($product->beautyImages && $product->beautyImages->count() > 0)
                                <div>
                                    <h5 class="font-medium text-gray-900 mb-4">Tutte le Immagini Sfondo</h5>
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                                        @foreach($product->beautyImages as $beautyImage)
                                            <div class="group relative bg-white border border-purple-200 hover:border-purple-400 rounded-lg overflow-hidden transition-all">
                                                {{-- Category Badge --}}
                                                <div class="absolute top-3 left-3 z-20">
                                                    @if($beautyImage->beauty_category === 'main')
                                                        <span class="px-2 py-1 bg-orange-500 text-white text-xs rounded-full font-medium">Main</span>
                                                    @elseif($beautyImage->beauty_category === 'slideshow')
                                                        <span class="px-2 py-1 bg-blue-500 text-white text-xs rounded-full font-medium">Slide</span>
                                                    @elseif($beautyImage->beauty_category === 'header')
                                                        <span class="px-2 py-1 bg-green-500 text-white text-xs rounded-full font-medium">Header</span>
                                                    @else
                                                        <span class="px-2 py-1 bg-gray-500 text-white text-xs rounded-full font-medium">Nessuna</span>
                                                    @endif
                                                </div>
                                                
                                                <div class="aspect-video relative overflow-hidden">
                                                    <img src="{{ $beautyImage->url }}" 
                                                         alt="{{ $beautyImage->alt_text ?: $product->name . ' - Sfondo' }}"
                                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                    
                                                    {{-- Category Assignment Actions --}}
                                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-60 transition-all duration-300">
                                                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                                            <div class="flex flex-col space-y-2">
                                                                <button wire:click="assignBeautyCategory({{ $beautyImage->id }}, 'main')"
                                                                        class="px-3 py-1.5 bg-orange-500 text-white rounded-lg text-xs hover:bg-orange-600 transition-colors">
                                                                    Principale
                                                                </button>
                                                                
                                                                <button wire:click="assignBeautyCategory({{ $beautyImage->id }}, 'slideshow')"
                                                                        class="px-3 py-1.5 bg-blue-500 text-white rounded-lg text-xs hover:bg-blue-600 transition-colors">
                                                                    Slideshow
                                                                </button>
                                                                
                                                                <button wire:click="assignBeautyCategory({{ $beautyImage->id }}, 'header')"
                                                                        class="px-3 py-1.5 bg-green-500 text-white rounded-lg text-xs hover:bg-green-600 transition-colors">
                                                                    Header
                                                                </button>
                                                                
                                                                @if($beautyImage->beauty_category)
                                                                    <button wire:click="removeFromBeautyCategory({{ $beautyImage->id }})"
                                                                            class="px-3 py-1.5 bg-gray-500 text-white rounded-lg text-xs hover:bg-gray-600 transition-colors">
                                                                        Rimuovi
                                                                    </button>
                                                                @endif
                                                                
                                                                <button wire:click="deleteBeautyImage({{ $beautyImage->id }})"
                                                                        wire:confirm="Eliminare questo sfondo?"
                                                                        class="px-3 py-1.5 bg-red-500 text-white rounded-lg text-xs hover:bg-red-600 transition-colors">
                                                                    Elimina
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
                                <div class="text-center py-12 border-2 border-dashed border-purple-300 rounded-lg bg-purple-50">
                                    <svg class="mx-auto h-16 w-16 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 011 1v1a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1h3z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8H5z"/>
                                    </svg>
                                    <h3 class="mt-4 text-lg font-medium text-gray-900">Nessuna immagine sfondo</h3>
                                    <p class="mt-2 text-sm text-gray-500">Aggiungi immagini per sfondi, slideshow, header</p>
                                    <div class="mt-4 flex justify-center space-x-3">
                                        <button wire:click="openUploadModal('beauty')" 
                                                class="px-6 py-3 bg-purple-500 text-white text-sm rounded-lg hover:bg-purple-600 transition-colors">
                                            Upload Sfondo
                                        </button>
                                        <button wire:click="openImagePicker('beauty')" 
                                                class="px-6 py-3 bg-purple-100 text-purple-700 text-sm rounded-lg hover:bg-purple-200 transition-colors border border-purple-300">
                                            Seleziona da Libreria
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar (1/4) --}}
            <div class="xl:col-span-1 space-y-6">

                {{-- Tags Management --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h3 class="text-lg font-medium text-gray-900">Materiali & Colori</h3>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        {{-- MATERIALE --}}
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Materiale</h4>
                            @livewire('admin.tag-manager-component', [
                                'category' => 'material', 
                                'productId' => $product->id
                            ], key('material-manager-' . $product->id))
                        </div>

                        {{-- COLORE --}}
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Colore</h4>
                            @livewire('admin.tag-manager-component', [
                                'category' => 'color', 
                                'productId' => $product->id
                            ], key('color-manager-' . $product->id))
                        </div>

                        {{-- FINITURA --}}
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Finitura</h4>
                            @livewire('admin.tag-manager-component', [
                                'category' => 'finish', 
                                'productId' => $product->id
                            ], key('finish-manager-' . $product->id))
                        </div>
                    </div>
                </div>

                {{-- Stile & Caratteristiche --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h3 class="text-lg font-medium text-gray-900">Stile & Caratteristiche</h3>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        {{-- STILE --}}
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Stile</h4>
                            @livewire('admin.tag-manager-component', [
                                'category' => 'style', 
                                'productId' => $product->id
                            ], key('style-manager-' . $product->id))
                        </div>

                        {{-- CARATTERISTICHE --}}
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Caratteristiche</h4>
                            @livewire('admin.tag-manager-component', [
                                'category' => 'feature', 
                                'productId' => $product->id
                            ], key('feature-manager-' . $product->id))
                        </div>

                        {{-- CERTIFICAZIONE --}}
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Certificazioni</h4>
                            @livewire('admin.tag-manager-component', [
                                'category' => 'certification', 
                                'productId' => $product->id
                            ], key('certification-manager-' . $product->id))
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Upload Modal --}}
    @if($showUploadModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
                <div class="border-b border-gray-100 px-6 py-4">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-{{ $uploadMode === 'gallery' ? 'blue' : 'purple' }}-500 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6 6h.01M6 20h.01M18 20h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">
                                    @if($uploadMode === 'gallery')
                                        Carica Immagini Prodotto
                                    @else
                                        Carica Immagini Sfondo
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
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 hover:bg-blue-50 transition-colors">
                        <svg class="mx-auto h-16 w-16 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        
                        <div class="mt-6">
                            <label class="cursor-pointer">
                                <span class="block text-lg font-medium text-gray-900 mb-2">
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
                                       class="block w-full mt-3 text-sm text-gray-500 file:mr-4 file:py-3 file:px-6 file:rounded-full file:border-0 file:text-sm file:font-medium file:bg-{{ $uploadMode === 'gallery' ? 'blue' : 'purple' }}-50 file:text-{{ $uploadMode === 'gallery' ? 'blue' : 'purple' }}-700 hover:file:bg-{{ $uploadMode === 'gallery' ? 'blue' : 'purple' }}-100 file:cursor-pointer file:transition-colors">
                            </label>
                            
                            <div class="mt-4 space-y-2 text-sm text-gray-500">
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
                    <div wire:loading wire:target="uploadedImages" class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
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
                            class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Chiudi
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Loading Overlay --}}
    <div wire:loading.flex wire:target="saveField,updateCategory,updateTags,toggleStatus,toggleFeatured,markAsBeauty,markAsGallery,setPrimaryImage,clearPrimaryImage" 
         class="fixed inset-0 bg-black bg-opacity-30 z-50 items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl p-8 max-w-sm w-full mx-4">
            <div class="flex flex-col items-center space-y-4">
                <div class="relative">
                    <div class="w-16 h-16 border-4 border-blue-200 rounded-full"></div>
                    <div class="w-16 h-16 border-4 border-blue-600 rounded-full border-t-transparent animate-spin absolute top-0 left-0"></div>
                </div>
                <div class="text-center">
                    <h3 class="text-lg font-medium text-gray-900">Salvando...</h3>
                    <p class="text-sm text-gray-500 mt-1">Le modifiche vengono applicate</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Image Picker Component --}}
    @livewire('admin.image-picker-component', [
        'productId' => $product->id,
        'eventName' => 'image-selected'
    ], key('image-picker-' . $product->id))
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        // Event Listeners
        Livewire.on('toast', (event) => {
            const { message, type = 'success' } = event;
            showToast(message, type);
        });
        
        Livewire.on('field-saved', (event) => {
            console.log('Campo salvato:', event.field);
            showToast(`Campo "${event.field}" salvato con successo!`, 'success');
        });
        
        Livewire.on('status-changed', (event) => {
            console.log('Status cambiato:', event.status);
            showToast(`Status cambiato in: ${event.status}`, 'info');
        });

        // Beauty System Events
        Livewire.on('beauty-marked', (event) => {
            console.log('Beauty image set:', event.imageId);
            showToast('Immagine marcata come Beauty!', 'success');
        });
        
        Livewire.on('beauty-cleared', (event) => {
            console.log('Beauty cleared:', event.imageId);
            showToast('Beauty rimossa!', 'info');
        });

        Livewire.on('category-assigned', (event) => {
            console.log('Category assigned:', event);
            showToast(`Categoria "${event.category}" assegnata!`, 'success');
        });

        Livewire.on('images-optimized', (event) => {
            console.log('Images optimized:', event);
            showToast('Immagini ottimizzate con successo!', 'success');
        });

        // Image Picker Events
        Livewire.on('image-selected', (event) => {
            console.log('Images selected from picker:', event);
            const count = event.imageIds ? event.imageIds.length : 1;
            const mode = event.mode === 'gallery' ? 'Gallery' : 'Sfondi';
            showToast(`${count} immagini aggiunte a ${mode}!`, 'success');
        });

        Livewire.on('picker-opened', () => {
            console.log('Image picker opened');
        });

        Livewire.on('picker-closed', () => {
            console.log('Image picker closed');
        });
    });

    function showToast(message, type = 'success') {
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            info: 'bg-blue-500',
            warning: 'bg-yellow-500'
        };

        const icons = {
            success: '✓',
            error: '✗', 
            info: 'i',
            warning: '!'
        };
        
        const toast = document.createElement('div');
        toast.className = `${colors[type]} text-white px-6 py-4 rounded-lg shadow-lg mb-3 transform transition-all duration-500 translate-x-full border border-white border-opacity-20`;
        
        toast.innerHTML = `
            <div class="flex items-center space-x-3">
                <span class="text-lg font-bold">${icons[type]}</span>
                <span class="font-medium">${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-white text-opacity-80 hover:text-opacity-100 transition-colors">
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

    document.addEventListener('livewire:init', () => {
    Livewire.on('image-picker-selection', (event) => {
        console.log('🎯 Image picker selection received:', event);
        
        // Passa i parametri separatamente, non come oggetto
        @this.call('handleImageSelection', 
            event.imageIds, 
            event.images, 
            event.mode, 
            event.productId
        ).then(() => {
            console.log('✅ Images processed successfully');
        }).catch((error) => {
            console.error('❌ Error processing images:', error);
        });
    });
});

    // Smooth scroll behavior
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
</script>
@endpush



@push('styles')
<style>
    /* Smooth transitions */
    * {
        transition: all 0.2s ease;
    }

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
        background: #3b82f6;
        border-radius: 3px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #1d4ed8;
    }

    /* Focus states */
    input:focus, textarea:focus, select:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Loading states */
    [wire\:loading] {
        opacity: 0.7;
        pointer-events: none;
    }

    [wire\:loading\.delay] {
        opacity: 1;
    }

    /* Card shadows */
    .card-shadow {
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }

    .card-shadow:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
</style>
@endpush

