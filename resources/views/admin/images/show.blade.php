<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.images.index') }}" class="text-gray-600 hover:text-gray-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div class="flex items-center">
                <div class="w-6 h-6 bg-purple-500 rounded-full flex items-center justify-center mr-3">
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight truncate">
                    {{ $image->clean_name }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4">
            
            <!-- Image Display -->
            <div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
                <div class="aspect-video bg-gray-100 flex items-center justify-center">
                    <img src="{{ $image->url }}" 
                         alt="{{ $image->alt_text }}" 
                         class="max-w-full max-h-full object-contain">
                </div>
                
                <!-- ✨ AGGIORNATO: Image Badges con Marketing -->
                <div class="p-4 border-b border-gray-200">
                    <div class="flex flex-wrap gap-2">
                        @if($image->type === 'beauty')
                            @if($image->is_marketing)
                                <!-- Beauty con tag Marketing = Badge arancione -->
                                <span class="bg-orange-600 text-white text-sm px-3 py-1 rounded-full inline-flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                    </svg>
                                    Marketing
                                </span>
                            @else
                                <!-- Beauty normale = Badge purple -->
                                <span class="bg-purple-600 text-white text-sm px-3 py-1 rounded-full inline-flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    Beauty
                                </span>
                            @endif
                        @elseif($image->type === 'gallery')
                            <span class="bg-green-600 text-white text-sm px-3 py-1 rounded-full inline-flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                </svg>
                                Gallery
                            </span>
                        @else
                            <span class="bg-blue-600 text-white text-sm px-3 py-1 rounded-full">{{ ucfirst($image->type) }}</span>
                        @endif

                        @if($image->isPrimaryForProduct())
                            <span class="bg-yellow-500 text-white text-sm px-3 py-1 rounded-full inline-flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                Primary
                            </span>
                        @endif

                        @if($image->is_optimized)
                            <span class="bg-green-500 text-white text-sm px-3 py-1 rounded-full inline-flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                                </svg>
                                Ottimizzata
                            </span>
                        @else
                            <span class="bg-gray-400 text-white text-sm px-3 py-1 rounded-full inline-flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                Non Ottimizzata
                            </span>
                        @endif

                        <!-- ✨ AGGIORNATO: Beauty category solo se non è marketing -->
                        @if($image->beauty_category && !$image->is_marketing)
                            <span class="bg-purple-500 text-white text-sm px-3 py-1 rounded-full">{{ ucfirst($image->beauty_category) }}</span>
                        @endif

                        <!-- ✨ NUOVO: Marketing category -->
                        @if($image->is_marketing && $image->marketing_category)
                            <span class="bg-orange-500 text-white text-sm px-3 py-1 rounded-full">{{ $image->marketing_category_name }}</span>
                        @endif
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="text-center">
                            <div class="font-semibold text-gray-900">{{ $image->formatted_dimensions }}</div>
                            <div class="text-gray-600">Dimensioni</div>
                        </div>
                        <div class="text-center">
                            <div class="font-semibold text-gray-900">{{ $image->formatted_size }}</div>
                            <div class="text-gray-600">File Size</div>
                        </div>
                        <div class="text-center">
                            <div class="font-semibold text-gray-900">{{ $image->usage_count }}x</div>
                            <div class="text-gray-600">Utilizzo</div>
                        </div>
                        <div class="text-center">
                            <div class="font-semibold text-gray-900">{{ $image->created_at->format('d/m/Y') }}</div>
                            <div class="text-gray-600">Creazione</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ✨ NUOVO: Marketing Info (se presente) -->
            @if($image->is_marketing)
            <div class="bg-orange-50 border border-orange-200 rounded-lg shadow mb-6 p-4">
                <div class="flex items-center mb-3">
                    <div class="w-5 h-5 bg-orange-500 rounded-full flex items-center justify-center mr-2">
                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">🎯 Informazioni Marketing</h3>
                </div>
                
                <div class="space-y-2">
                    @if($image->marketing_category)
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700">Categoria:</span>
                        <span class="text-sm text-gray-900">{{ $image->marketing_category_name }}</span>
                    </div>
                    @endif
                    
                    @if($image->campaign_name)
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700">Campagna:</span>
                        <span class="text-sm text-gray-900">{{ $image->campaign_name }}</span>
                    </div>
                    @endif
                    
                    @if($image->usage_rights)
                    <div>
                        <span class="text-sm font-medium text-gray-700">Diritti d'Uso:</span>
                        <p class="text-sm text-gray-900 mt-1">{{ $image->usage_rights }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Association Info -->
            @if($image->imageable)
            <div class="bg-white rounded-lg shadow mb-6 p-4">
                <div class="flex items-center mb-3">
                    <div class="w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center mr-2">
                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Associazione Prodotto</h3>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-medium text-gray-900">{{ $image->imageable->name }}</div>
                        <div class="text-sm text-gray-600">SKU: {{ $image->imageable->sku }}</div>
                        @if($image->imageable->primary_image_id === $image->id)
                            <div class="text-sm text-yellow-600 flex items-center mt-1">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                Immagine principale del prodotto
                            </div>
                        @endif
                    </div>
                    @if(Route::has('admin.products.show'))
                    <a href="{{ route('admin.products.show', $image->imageable) }}" 
                       class="text-blue-600 hover:text-blue-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    </a>
                    @endif
                </div>
            </div>
            @elseif($image->is_marketing)
            <!-- ✨ NUOVO: Info per immagini marketing non associate -->
            <div class="bg-orange-50 border border-orange-200 rounded-lg shadow mb-6 p-4">
                <div class="flex items-center text-orange-800">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v1a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm font-medium">
                        Immagine Marketing non associata a prodotti specifici
                    </span>
                </div>
            </div>
            @endif

            <!-- Edit Details -->
            <livewire:admin.image-detail-editor :image="$image" />

            <!-- ✨ AGGIORNATO: Quick Actions con toggle Marketing -->
            <div class="grid grid-cols-1 gap-3 mb-6">
                
                <!-- ✨ NUOVO: Toggle Marketing per Beauty -->
                @if($image->type === 'beauty')
                <button onclick="toggleMarketing()" 
                        class="w-full @if($image->is_marketing) bg-orange-600 hover:bg-orange-700 @else bg-gray-600 hover:bg-gray-700 @endif text-white py-3 rounded-lg font-medium inline-flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                    </svg>
                    @if($image->is_marketing)
                        🎯 Rimuovi da Marketing
                    @else
                        🎯 Aggiungi a Marketing
                    @endif
                </button>
                @endif

                @if($image->imageable)
                <button onclick="dissociateImage()" 
                        class="w-full bg-orange-600 hover:bg-orange-700 text-white py-3 rounded-lg font-medium inline-flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                    </svg>
                    Disassocia da Prodotto
                </button>
                @else
                <button onclick="openAssociateModal()" 
                        class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-medium inline-flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd"></path>
                    </svg>
                    Associa a Prodotto
                </button>
                @endif

                @if(!$image->is_optimized)
                <button onclick="optimizeImage()" 
                        class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-medium inline-flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                    </svg>
                    Ottimizza Immagine
                </button>
                @endif

                <button onclick="openReplaceModal()" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-medium inline-flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                    </svg>
                    Sostituisci Immagine
                </button>

                <button onclick="deleteImage()" 
                        class="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg font-medium inline-flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    Elimina Immagine
                </button>
            </div>

            <!-- URLs Section -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center mb-4">
                    <div class="w-5 h-5 bg-gray-500 rounded-full flex items-center justify-center mr-2">
                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">URLs</h3>
                </div>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">URL Pulito</label>
                        <div class="flex">
                            <input type="text" 
                                   value="{{ $image->url }}" 
                                   readonly 
                                   class="flex-1 bg-gray-50 border border-gray-300 rounded-l-md px-3 py-2 text-sm">
                            <button onclick="copyToClipboard('{{ $image->url }}')" 
                                    class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-r-md inline-flex items-center">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8 2a1 1 0 000 2h2a1 1 0 100-2H8z"></path>
                                    <path d="M3 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v6h-4.586l1.293-1.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L10.414 13H15v3a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">URL AWS S3</label>
                        <div class="flex">
                            <input type="text" 
                                   value="{{ $image->aws_url }}" 
                                   readonly 
                                   class="flex-1 bg-gray-50 border border-gray-300 rounded-l-md px-3 py-2 text-sm">
                            <button onclick="copyToClipboard('{{ $image->aws_url }}')" 
                                    class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-r-md inline-flex items-center">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8 2a1 1 0 000 2h2a1 1 0 100-2H8z"></path>
                                    <path d="M3 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v6h-4.586l1.293-1.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L10.414 13H15v3a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Show toast or alert
                alert('URL copiato negli appunti!');
            });
        }

        function optimizeImage() {
            if (confirm('Ottimizzare questa immagine?')) {
                // Use Livewire method
                Livewire.dispatch('optimize-image', { imageId: {{ $image->id }} });
            }
        }

        function dissociateImage() {
            if (confirm('Disassociare l\'immagine dal prodotto?')) {
                Livewire.dispatch('dissociate-image', { imageId: {{ $image->id }} });
            }
        }

        function deleteImage() {
            if (confirm('ATTENZIONE: Eliminare definitivamente questa immagine?')) {
                Livewire.dispatch('delete-image', { imageId: {{ $image->id }} });
            }
        }

        // ✨ NUOVO: Toggle marketing function
        function toggleMarketing() {
            const isCurrentlyMarketing = {{ $image->is_marketing ? 'true' : 'false' }};
            const action = isCurrentlyMarketing ? 'rimuovere da' : 'aggiungere a';
            
            if (confirm(`Vuoi ${action} Marketing questa immagine?`)) {
                Livewire.dispatch('toggle-marketing', { imageId: {{ $image->id }} });
            }
        }

        function openAssociateModal() {
            // Show associate modal
            document.getElementById('associateModal').classList.remove('hidden');
        }

        function openReplaceModal() {
            // Show replace modal
            document.getElementById('replaceModal').classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }
    </script>

    <!-- Associate Modal (Mobile) -->
    <div id="associateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 hidden">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0" onclick="closeModal('associateModal')"></div>
            
            <div class="inline-block align-bottom bg-white rounded-t-lg text-left overflow-hidden shadow-xl transform transition-all sm:align-middle sm:max-w-lg sm:w-full">
                <livewire:admin.image-associate-form :image="$image" />
            </div>
        </div>
    </div>

    <!-- Replace Modal (Mobile) -->
    <div id="replaceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 hidden">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0" onclick="closeModal('replaceModal')"></div>
            
            <div class="inline-block align-bottom bg-white rounded-t-lg text-left overflow-hidden shadow-xl transform transition-all sm:align-middle sm:max-w-lg sm:w-full">
                <livewire:admin.image-replace-form :image="$image" />
            </div>
        </div>
    </div>

</x-app-layout>