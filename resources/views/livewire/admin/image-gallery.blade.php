<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div class="flex items-center">
            <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20" preserveAspectRatio="xMidYMid meet">
                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Gestione Immagini</h2>
                <p class="text-gray-600">{{ $this->stats['total_images'] }} immagini totali</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <button wire:click="openUploadModal" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>Carica</span>
            </button>
            
            <button wire:click="toggleBulkMode" 
                    class="@if($bulkMode) bg-red-600 hover:bg-red-700 @else bg-gray-600 hover:bg-gray-700 @endif text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center space-x-2">
                @if($bulkMode) 
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" preserveAspectRatio="xMidYMid meet">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Esci</span>
                @else 
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" preserveAspectRatio="xMidYMid meet">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V8zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Bulk</span>
                @endif
            </button>
        </div>
    </div>

    <!-- ✨ AGGIORNATO: Quick Stats con Beauty/Marketing separati -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $this->stats['total_images'] }}</div>
            <div class="text-xs text-gray-600">Totali</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $this->stats['gallery_images'] }}</div>
            <div class="text-xs text-gray-600">Gallery</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-2xl font-bold text-purple-600">{{ $this->stats['beauty_images'] }}</div>
            <div class="text-xs text-gray-600">Beauty</div>
        </div>
        <!-- ✨ NUOVO: Statistiche Marketing separate -->
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-2xl font-bold text-orange-600">{{ $this->stats['marketing_images'] }}</div>
            <div class="text-xs text-gray-600">Marketing</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-2xl font-bold text-red-600">{{ $this->stats['orphan_images'] }}</div>
            <div class="text-xs text-gray-600">Orfane</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-2xl font-bold text-yellow-600">{{ $this->stats['unoptimized_images'] }}</div>
            <div class="text-xs text-gray-600">Non Opt</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-2xl font-bold text-indigo-600">{{ $this->stats['total_size_mb'] }}MB</div>
            <div class="text-xs text-gray-600">Storage</div>
        </div>
    </div>

    <!-- ✨ AGGIORNATO: Filters con Marketing separato -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-7 gap-4">
            <!-- Search -->
            <div class="xl:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Cerca</label>
                <input wire:model.live.debounce.300ms="search" 
                       type="text" 
                       placeholder="Nome, campaign, alt text..." 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>

            <!-- Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                <select wire:model.live="type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">Tutti</option>
                    <option value="gallery">Gallery</option>
                    <option value="beauty">Beauty + Marketing</option>
                    <option value="product">Product</option>
                    <option value="category">Category</option>
                    <option value="content">Content</option>
                </select>
            </div>

            <!-- ✨ NUOVO: Marketing Filter (per beauty) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Beauty/Marketing</label>
                <select wire:model.live="marketingFilter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">Tutte Beauty</option>
                    <option value="product_beauty">💄 Beauty Prodotto</option>
                    <option value="marketing">🎯 Marketing</option>
                </select>
            </div>

            <!-- Beauty Category -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Beauty Category</label>
                <select wire:model.live="beautyCategory" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">Tutte</option>
                    <option value="main">Main</option>
                    <option value="slideshow">Slideshow</option>
                    <option value="header">Header</option>
                </select>
            </div>

            <!-- ✨ Marketing Category (solo se marketing filter attivo) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Marketing Category</label>
                <select wire:model.live="marketingCategory" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">Tutte</option>
                    <option value="hero">🎭 Hero Image</option>
                    <option value="banner">📢 Banner</option>
                    <option value="social">📱 Social Media</option>
                    <option value="brochure">📄 Brochure</option>
                    <option value="presentation">🎤 Presentazione</option>
                    <option value="web">🌐 Web Content</option>
                    <option value="press">📰 Press Kit</option>
                    <option value="advertising">📺 Advertising</option>
                    <option value="events">🎪 Eventi</option>
                    <option value="catalog">📚 Catalogo</option>
                </select>
            </div>

            <!-- Usage -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Utilizzo</label>
                <select wire:model.live="usage" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">Tutte</option>
                    <option value="used">🟢 Usate</option>
                    <option value="unused">🔴 Orfane (escluso marketing)</option>
                </select>
                @if($usage === 'unused')
                    <p class="text-xs text-gray-500 mt-1">Marketing non considerate orfane</p>
                @endif
            </div>

            <!-- Optimized -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ottimizzazione</label>
                <select wire:model.live="optimized" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">Tutte</option>
                    <option value="1">Ottimizzate</option>
                    <option value="0">Non Ottimizzate</option>
                </select>
            </div>
        </div>

        <div class="flex justify-between items-center mt-4">
            <button wire:click="resetFilters" class="text-gray-600 hover:text-gray-800 text-sm inline-flex items-center">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" preserveAspectRatio="xMidYMid meet">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                Reset Filtri
            </button>
            <div class="flex items-center space-x-2 text-sm">
                <span class="text-gray-600">Ordina:</span>
                <button wire:click="sortBy('created_at')" class="text-blue-600 hover:text-blue-800">
                    Data @if($sortBy === 'created_at') @if($sortDir === 'asc') ↑ @else ↓ @endif @endif
                </button>
                <button wire:click="sortBy('usage_count')" class="text-blue-600 hover:text-blue-800">
                    Uso @if($sortBy === 'usage_count') @if($sortDir === 'asc') ↑ @else ↓ @endif @endif
                </button>
                <button wire:click="sortBy('file_size')" class="text-blue-600 hover:text-blue-800">
                    Size @if($sortBy === 'file_size') @if($sortDir === 'asc') ↑ @else ↓ @endif @endif
                </button>
            </div>
        </div>
    </div>

    <!-- Bulk Actions Bar -->
    @if($bulkMode)
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-3 sm:space-y-0">
            <div class="flex items-center space-x-4">
                <span class="text-sm font-medium text-yellow-800">
                    {{ count($selectedImages) }} immagini selezionate
                </span>
                <button wire:click="selectAll" class="text-blue-600 hover:text-blue-800 text-sm">Tutte</button>
                <button wire:click="deselectAll" class="text-gray-600 hover:text-gray-800 text-sm">Nessuna</button>
            </div>
            <div class="flex space-x-2">
                <button wire:click="bulkOptimize" 
                        wire:confirm="Ottimizzare le immagini selezionate?"
                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm inline-flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                    </svg>
                    Ottimizza
                </button>
                <button wire:click="bulkDelete" 
                        wire:confirm="Eliminare definitivamente le immagini selezionate?"
                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm inline-flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    Elimina
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- ✨ AGGIORNATO: Images Grid con badge Marketing per Beauty -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
        @forelse($this->images as $image)
            <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200 relative group">
                
                @if($bulkMode)
                <!-- Bulk Selection Checkbox -->
                <div class="absolute top-2 left-2 z-10">
                    <input type="checkbox" 
                           wire:model.live="selectedImages" 
                           value="{{ $image->id }}" 
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>
                @endif

                <!-- Image -->
                <div class="aspect-square relative cursor-pointer" 
                     wire:click="openImageDetail({{ $image->id }})">
                    <img src="{{ $image->url }}" 
                         alt="{{ $image->alt_text }}" 
                         class="w-full h-full object-cover rounded-t-lg"
                         loading="lazy">
                    
                    <!-- ✨ AGGIORNATO: Type Badge con Marketing per Beauty -->
                    <div class="absolute top-2 right-2">
                        @if($image->type === 'beauty')
                            @if($image->is_marketing)
                                <!-- Beauty con tag Marketing = Badge arancione -->
                                <span class="bg-orange-600 text-white text-xs px-2 py-2 rounded-full shadow inline-flex items-center">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                    </svg>
                                </span>
                            @else
                                <!-- Beauty normale = Badge purple -->
                                <span class="bg-purple-600 text-white text-xs px-2 py-2 rounded-full shadow inline-flex items-center">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        <circle cx="16" cy="4" r="1" opacity="0.8"/>
                                        <circle cx="3" cy="6" r="0.5" opacity="0.6"/>
                                        <circle cx="17" cy="14" r="0.8" opacity="0.7"/>
                                    </svg>
                                </span>
                            @endif
                        @elseif($image->type === 'gallery')
                            <span class="bg-green-600 text-white text-xs px-2 py-2 rounded-full shadow inline-flex items-center">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                </svg>
                            </span>
                        @else
                            <span class="bg-blue-600 text-white text-xs px-2 py-2 rounded-full shadow">{{ substr($image->type, 0, 1) }}</span>
                        @endif
                    </div>

                    <!-- Primary Badge -->
                    @if($image->isPrimaryForProduct())
                        <div class="absolute bottom-2 left-2">
                            <span class="bg-yellow-500 text-white text-xs px-2 py-1 rounded-full shadow inline-flex items-center">
                                <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            </span>
                        </div>
                    @endif

                    <!-- Usage Count -->
                    @if($image->usage_count > 0)
                        <div class="absolute bottom-2 right-2">
                            <span class="bg-gray-800 bg-opacity-75 text-white text-xs px-2 py-1 rounded-full">
                                {{ $image->usage_count }}x
                            </span>
                        </div>
                    @endif

                    <!-- ✨ AGGIORNATO: Hover Overlay con azione Marketing Toggle -->
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 rounded-t-lg flex items-center justify-center opacity-0 group-hover:opacity-100">
                        <div class="flex space-x-2">
                            @if($image->type === 'beauty')
                            <!-- Toggle Marketing per Beauty -->
                            <button wire:click.stop="toggleMarketing({{ $image->id }})" 
                                    class="@if($image->is_marketing) bg-orange-600 hover:bg-orange-700 @else bg-gray-600 hover:bg-gray-700 @endif text-white p-2 rounded-full shadow-lg">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                            @endif
                            
                            @if(!$image->is_optimized)
                            <button wire:click.stop="optimizeImage({{ $image->id }})" 
                                    class="bg-green-600 hover:bg-green-700 text-white p-2 rounded-full shadow-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </button>
                            @endif
                            
                            <button wire:click.stop="openAssociateModal({{ $image->id }})" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-full shadow-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ✨ AGGIORNATO: Info con Marketing Category -->
                <div class="p-3">
                    <div class="text-sm font-medium text-gray-900 truncate mb-1">
                        {{ $image->clean_name }}
                    </div>
                    
                    <div class="text-xs text-gray-500 space-y-1">
                        <div class="flex justify-between">
                            <span>{{ $image->formatted_dimensions }}</span>
                            <span>{{ $image->formatted_size }}</span>
                        </div>
                        
                        @if($image->imageable)
                            <div class="flex items-center space-x-1">
                                <svg class="w-3 h-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                </svg>
                                <span class="truncate">{{ $image->imageable->name ?? 'Prodotto' }}</span>
                            </div>
                        @else
                            <div class="text-orange-600 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                </svg>
                                @if($image->is_marketing)
                                    <span class="truncate text-orange-600">Marketing</span>
                                @else
                                    <span class="truncate">Non associata</span>
                                @endif
                            </div>
                        @endif

                        <!-- ✨ NUOVO: Marketing Category info -->
                        @if($image->is_marketing && $image->marketing_category)
                            <div class="text-orange-600 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="truncate">{{ $image->marketing_category_name }}</span>
                            </div>
                        @endif

                        @if($image->beauty_category && !$image->is_marketing)
                            <div class="text-purple-600 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                {{ ucfirst($image->beauty_category) }}
                            </div>
                        @endif

                        <!-- ✨ NUOVO: Campaign Name -->
                        @if($image->campaign_name)
                            <div class="text-indigo-600 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                </svg>
                                <span class="truncate">{{ $image->campaign_name }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between items-center">
                            <span>{{ $image->created_at->format('d/m') }}</span>
                            @if($image->is_optimized)
                                <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-12 bg-white rounded-lg shadow">
                    <div class="w-16 h-16 bg-gray-100 rounded-full mx-auto mb-4 flex items-center justify-center flex-shrink-0">
                        <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20" preserveAspectRatio="xMidYMid meet">
                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Nessuna immagine trovata</h3>
                    <p class="text-gray-500 mb-4">
                        @if($search || $type || $beautyCategory || $marketingFilter || $marketingCategory || $usage)
                            Prova a modificare i filtri di ricerca.
                        @else
                            Inizia caricando la tua prima immagine.
                        @endif
                    </p>
                    <button wire:click="openUploadModal" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                        Carica Prima Immagine
                    </button>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $this->images->links() }}
    </div>

    <!-- MODALS -->

    <!-- ✨ AGGIORNATO: Upload Modal con checkbox Marketing -->
    @if($showUploadModal)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Carica Immagini</h3>
                <button wire:click="closeUploadModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="p-6 space-y-4">
                <!-- File Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Seleziona Immagini</label>
                    <input type="file" 
                           wire:model="uploadImages" 
                           multiple 
                           accept="image/*"
                           class="w-full border border-gray-300 rounded-md p-2 text-sm">
                    <p class="text-xs text-gray-500 mt-1">JPEG, PNG, WebP - Max 10MB per file</p>
                    @error('uploadImages.*') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                    <select wire:model.live="uploadType" class="w-full rounded-md border-gray-300 text-sm">
                        <option value="gallery">Gallery</option>
                        <option value="beauty">Beauty</option>
                        <option value="product">Product</option>
                        <option value="category">Category</option>
                        <option value="content">Content</option>
                    </select>
                </div>

                <!-- ✨ NUOVO: Beauty Options -->
                @if($uploadType === 'beauty')
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 space-y-3">
                    <!-- Beauty Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Beauty Category</label>
                        <select wire:model="uploadBeautyCategory" class="w-full rounded-md border-gray-300 text-sm">
                            <option value="">Nessuna categoria</option>
                            <option value="main">Main</option>
                            <option value="slideshow">Slideshow</option>
                            <option value="header">Header</option>
                        </select>
                    </div>

                    <!-- ✨ NUOVO: Marketing Checkbox -->
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   wire:model.live="uploadIsMarketing" 
                                   class="rounded border-gray-300 text-orange-600 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm font-medium text-gray-700">🎯 Immagine Marketing</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1">Per comunicazione generale, banner, social, etc.</p>
                    </div>

                    <!-- ✨ NUOVO: Marketing Options (solo se is_marketing attivo) -->
                    @if($uploadIsMarketing)
                    <div class="pl-6 space-y-3 border-l-2 border-orange-200">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Marketing Category</label>
                            <select wire:model="uploadMarketingCategory" class="w-full rounded-md border-gray-300 text-sm">
                                <option value="">Seleziona categoria...</option>
                                <option value="hero">🎭 Hero Image</option>
                                <option value="banner">📢 Banner</option>
                                <option value="social">📱 Social Media</option>
                                <option value="brochure">📄 Brochure</option>
                                <option value="presentation">🎤 Presentazione</option>
                                <option value="web">🌐 Web Content</option>
                                <option value="press">📰 Press Kit</option>
                                <option value="advertising">📺 Advertising</option>
                                <option value="events">🎪 Eventi</option>
                                <option value="catalog">📚 Catalogo</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nome Campagna</label>
                            <input type="text" 
                                   wire:model="uploadCampaignName" 
                                   placeholder="Estate 2025, Lancio Prodotto..."
                                   class="w-full rounded-md border-gray-300 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Diritti d'Uso</label>
                            <textarea wire:model="uploadUsageRights" 
                                      placeholder="Descrivi i diritti di utilizzo..."
                                      rows="2"
                                      class="w-full rounded-md border-gray-300 text-sm"></textarea>
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Product Association -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Associa a Prodotto</label>
                    <select wire:model.live="uploadProductId" class="w-full rounded-md border-gray-300 text-sm">
                        <option value="">Nessuna associazione</option>
                        @foreach($this->products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                        @endforeach
                    </select>
                    @if($uploadIsMarketing)
                        <p class="text-xs text-orange-600 mt-1">
                            💡 Le immagini marketing possono non essere associate a prodotti
                        </p>
                    @endif
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            wire:click="closeUploadModal"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 text-sm">
                        Annulla
                    </button>
                    <button type="button" 
                            wire:click="processUpload"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                        Carica Immagini
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ✨ AGGIORNATO: Image Detail Modal con Marketing -->
    @if($showImageDetail && $this->selectedImage)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex">
            <!-- Image Preview -->
            <div class="flex-1 bg-gray-100 flex items-center justify-center">
                <img src="{{ $this->selectedImage->url }}" 
                     alt="{{ $this->selectedImage->alt_text }}" 
                     class="max-w-full max-h-full object-contain">
            </div>
            
            <!-- Sidebar -->
            <div class="w-80 bg-white overflow-y-auto">
                <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Dettagli Immagine</h3>
                    <button wire:click="closeImageDetail" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="p-6 space-y-6">
                    <!-- Quick Info -->
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <div class="font-medium text-gray-700">Tipo</div>
                            <div class="text-gray-900">
                                @if($this->selectedImage->type === 'beauty')
                                    @if($this->selectedImage->is_marketing)
                                        🎯 Marketing
                                    @else
                                        💄 Beauty
                                    @endif
                                @else
                                    {{ $this->selectedImage->type_name }}
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="font-medium text-gray-700">Dimensioni</div>
                            <div class="text-gray-900">{{ $this->selectedImage->formatted_dimensions }}</div>
                        </div>
                        <div>
                            <div class="font-medium text-gray-700">Dimensione</div>
                            <div class="text-gray-900">{{ $this->selectedImage->formatted_size }}</div>
                        </div>
                        <div>
                            <div class="font-medium text-gray-700">Utilizzo</div>
                            <div class="text-gray-900">{{ $this->selectedImage->usage_count }}x</div>
                        </div>
                    </div>

                    <!-- Edit Form -->
                    <form wire:submit="updateImageDetails" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alt Text</label>
                            <input type="text" 
                                   wire:model="editAltText" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Caption</label>
                            <textarea wire:model="editCaption" 
                                      rows="3" 
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                        </div>

                        @if($this->selectedImage->type === 'beauty')
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Beauty Category</label>
                                <select wire:model="editBeautyCategory" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                                    <option value="">Nessuna categoria</option>
                                    <option value="main">Main</option>
                                    <option value="slideshow">Slideshow</option>
                                    <option value="header">Header</option>
                                </select>
                            </div>

                            <!-- ✨ NUOVO: Marketing Toggle -->
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           wire:model.live="editIsMarketing" 
                                           class="rounded border-gray-300 text-orange-600 shadow-sm">
                                    <span class="ml-2 text-sm font-medium text-gray-700">🎯 Immagine Marketing</span>
                                </label>
                            </div>

                            <!-- ✨ NUOVO: Marketing Fields (solo se attivo) -->
                            @if($editIsMarketing)
                            <div class="pl-6 space-y-3 border-l-2 border-orange-200">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Marketing Category</label>
                                    <select wire:model="editMarketingCategory" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                                        <option value="">Seleziona categoria...</option>
                                        <option value="hero">🎭 Hero Image</option>
                                        <option value="banner">📢 Banner</option>
                                        <option value="social">📱 Social Media</option>
                                        <option value="brochure">📄 Brochure</option>
                                        <option value="presentation">🎤 Presentazione</option>
                                        <option value="web">🌐 Web Content</option>
                                        <option value="press">📰 Press Kit</option>
                                        <option value="advertising">📺 Advertising</option>
                                        <option value="events">🎪 Eventi</option>
                                        <option value="catalog">📚 Catalogo</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome Campagna</label>
                                    <input type="text" 
                                           wire:model="editCampaignName" 
                                           class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Diritti d'Uso</label>
                                    <textarea wire:model="editUsageRights" 
                                              rows="2"
                                              class="w-full rounded-md border-gray-300 shadow-sm text-sm"></textarea>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tags</label>
                            <input type="text" 
                                   wire:model="editTags" 
                                   placeholder="tag1, tag2, tag3"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>

                        <button type="submit" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-md text-sm">
                            Aggiorna Dettagli
                        </button>
                    </form>

                    <!-- Quick Actions -->
                    <div class="space-y-2">
                        @if($this->selectedImage->imageable)
                        <button wire:click="dissociateFromProduct({{ $this->selectedImage->id }})" 
                                wire:confirm="Disassociare l'immagine dal prodotto?"
                                class="w-full bg-orange-600 hover:bg-orange-700 text-white py-2 rounded-md text-sm inline-flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                            </svg>
                            Disassocia da {{ $this->selectedImage->imageable->name }}
                        </button>
                        @else
                        <button wire:click="openAssociateModal({{ $this->selectedImage->id }})" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-md text-sm inline-flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd"></path>
                            </svg>
                            Associa a Prodotto
                        </button>
                        @endif

                        @if(!$this->selectedImage->is_optimized)
                        <button wire:click="optimizeImage({{ $this->selectedImage->id }})" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-md text-sm inline-flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                            </svg>
                            Ottimizza Immagine
                        </button>
                        @endif

                        <button wire:click="deleteImage({{ $this->selectedImage->id }})" 
                                wire:confirm="Eliminare definitivamente questa immagine?"
                                class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-md text-sm inline-flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            Elimina Immagine
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-transition
             x-init="setTimeout(() => show = false, 3000)"
             class="fixed bottom-4 right-4 bg-green-600 text-white px-6 py-4 rounded-lg shadow-lg z-50">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-transition
             x-init="setTimeout(() => show = false, 5000)"
             class="fixed bottom-4 right-4 bg-red-600 text-white px-6 py-4 rounded-lg shadow-lg z-50">
            {{ session('error') }}
        </div>
    @endif
</div>