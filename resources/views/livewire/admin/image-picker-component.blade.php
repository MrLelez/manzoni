{{-- resources/views/livewire/admin/image-picker-component.blade.php --}}
{{-- MODERN IMAGE PICKER COMPONENT - TALL STACK --}}
<div>
@if($isOpen)
    <div class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-[60] p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-7xl w-full max-h-[90vh] overflow-hidden">
            
            {{-- Header --}}
            <div class="border-b border-gray-200 px-6 py-4 bg-gradient-to-r from-blue-50 to-purple-50">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6 6h.01M6 20h.01M18 20h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $title }}</h3>
                            <p class="text-sm text-gray-600">{{ $description }}</p>
                        </div>
                        @if($multiSelect && $selectedCount > 0)
                            <div class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                {{ $selectedCount }} selezionat{{ $selectedCount === 1 ? 'a' : 'e' }}
                            </div>
                        @endif
                    </div>
                    
                    <button wire:click="closePicker" 
                            class="text-gray-400 hover:text-gray-600 p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Search and Filters Bar --}}
            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50">
                <div class="flex flex-wrap items-center gap-4">
                    {{-- Search Input --}}
                    <div class="flex-1 min-w-[300px]">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" 
                                   wire:model.live.debounce.300ms="search" 
                                   placeholder="Cerca per nome, filename o alt text..."
                                   class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    {{-- Type Filter --}}
                    <div class="min-w-[150px]">
                        <select wire:model.live="selectedType" 
                                class="w-full py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @foreach($availableTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sort Controls --}}
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium text-gray-700">Ordina:</span>
                        <button wire:click="setSortBy('created_at')" 
                                class="px-3 py-1.5 text-sm rounded-lg transition-colors {{ $sortBy === 'created_at' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            Data
                            @if($sortBy === 'created_at')
                                <svg class="w-3 h-3 inline ml-1 {{ $sortOrder === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                </svg>
                            @endif
                        </button>
                        
                        <button wire:click="setSortBy('clean_name')" 
                                class="px-3 py-1.5 text-sm rounded-lg transition-colors {{ $sortBy === 'clean_name' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            Nome
                            @if($sortBy === 'clean_name')
                                <svg class="w-3 h-3 inline ml-1 {{ $sortOrder === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                </svg>
                            @endif
                        </button>
                        
                        <button wire:click="setSortBy('file_size')" 
                                class="px-3 py-1.5 text-sm rounded-lg transition-colors {{ $sortBy === 'file_size' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            Dimensioni
                            @if($sortBy === 'file_size')
                                <svg class="w-3 h-3 inline ml-1 {{ $sortOrder === 'desc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                </svg>
                            @endif
                        </button>
                    </div>

                    {{-- Results Count --}}
                    <div class="text-sm text-gray-500">
                        {{ $images->total() }} immagini
                    </div>
                </div>
            </div>

            {{-- Images Grid --}}
            <div class="flex-1 overflow-y-auto p-6" style="max-height: calc(90vh - 240px);">
                @if($images->count() > 0)
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                        @foreach($images as $image)
                            <div class="relative group bg-white border rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 
                                       {{ in_array($image->id, $selectedImages) ? 'ring-2 ring-blue-500 ring-offset-2' : 'hover:border-blue-300' }}">
                                
                                {{-- Selection Indicator --}}
                                @if($multiSelect)
                                    <div class="absolute top-3 left-3 z-20">
                                        <input type="checkbox" 
                                               wire:click="toggleImageSelection({{ $image->id }})"
                                               {{ in_array($image->id, $selectedImages) ? 'checked' : '' }}
                                               class="w-5 h-5 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500">
                                    </div>
                                @endif

                                {{-- Type Badge --}}
                                <div class="absolute top-3 right-3 z-20">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full text-white"
                                          style="background-color: {{ $image->dominant_color }}">
                                        {{ ucfirst($image->type) }}
                                    </span>
                                </div>

                                {{-- Image --}}
                                <div class="aspect-square relative overflow-hidden cursor-pointer"
                                     wire:click="selectImage({{ $image->id }})">
                                    <img src="{{ $image->url }}" 
                                         alt="{{ $image->alt_text }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                         loading="lazy">
                                    
                                    {{-- Hover Overlay --}}
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300">
                                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                            @if(!$multiSelect)
                                                <div class="px-4 py-2 bg-white text-gray-900 rounded-lg font-medium shadow-lg">
                                                    Seleziona
                                                </div>
                                            @endif
                                        </div>
                                        
                                        {{-- Quick Actions --}}
                                        <div class="absolute bottom-3 right-3 flex space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                            <button wire:click.stop="deleteImage({{ $image->id }})"
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
                                    <div class="text-xs font-medium text-gray-700 truncate mb-1" title="{{ $image->clean_name }}">
                                        {{ $image->clean_name }}
                                    </div>
                                    <div class="flex justify-between items-center text-xs text-gray-500">
                                        <span>{{ $image->formatted_dimensions }}</span>
                                        <span>{{ $image->formatted_size }}</span>
                                    </div>
                                    @if($image->alt_text)
                                        <div class="text-xs text-gray-400 truncate mt-1" title="{{ $image->alt_text }}">
                                            {{ $image->alt_text }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if($images->hasPages())
                        <div class="mt-8">
                            {{ $images->links() }}
                        </div>
                    @endif

                @else
                    {{-- Empty State --}}
                    <div class="text-center py-16">
                        <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6 6l-1-1m1 1l4 4m-4-4v6m0-6h6m-6 0l-1-1"/>
                        </svg>
                        <h3 class="mt-6 text-lg font-medium text-gray-900">Nessuna immagine trovata</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            @if($search)
                                Prova a modificare i termini di ricerca o i filtri.
                            @else
                                Non ci sono immagini disponibili con i filtri attuali.
                            @endif
                        </p>
                        @if($search)
                            <button wire:click="$set('search', '')" 
                                    class="mt-4 px-4 py-2 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 transition-colors">
                                Cancella Ricerca
                            </button>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Footer Actions --}}
            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-600">
                        @if($multiSelect)
                            {{ $selectedCount }} immagini selezionate
                        @else
                            Click su un'immagine per selezionarla
                        @endif
                    </div>
                    
                    <div class="flex space-x-3">
                        <button wire:click="closePicker" 
                                class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Annulla
                        </button>
                        
                        @if($multiSelect)
                            @if($selectedCount > 0)
                                <button wire:click="clearSelection" 
                                        class="px-6 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                    Deseleziona Tutto
                                </button>
                                
                                <button wire:click="confirmSelection" 
                                        class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                                    Seleziona {{ $selectedCount }} Immagini
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Loading Overlay --}}
    <div wire:loading.flex wire:target="search,selectedType,setSortBy,selectImage,deleteImage" 
         class="fixed inset-0 bg-black bg-opacity-30 z-[70] items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl p-6">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="text-gray-700 font-medium">Caricamento...</span>
            </div>
        </div>
    </div>
@endif
</div>

@push('scripts')
<script>
    // Gestisci escape key per chiudere il modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && @js($isOpen)) {
            @this.call('closePicker');
        }
    });
</script>
@endpush

@push('styles')
<style>
    /* Custom checkbox styling for better visibility */
    input[type="checkbox"]:checked {
        background-color: #3B82F6;
        border-color: #3B82F6;
    }
    
    /* Smooth animations */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Custom scrollbar for the images area */
    .overflow-y-auto::-webkit-scrollbar {
        width: 8px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    
    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@endpush