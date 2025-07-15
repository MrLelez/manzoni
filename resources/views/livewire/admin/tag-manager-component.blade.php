<div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
    
    {{-- Selected Tag Display --}}
    @if($this->isMultipleCategory())
        @if(!empty($selectedTagIds) && $this->selectedTags && $this->selectedTags->count() > 0)
            <div class="mb-3">
                <div class="flex flex-wrap gap-2">
                    @foreach($this->selectedTags as $tag)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-white"
                              style="background-color: {{ $tag->color }};">
                            {{ $tag->name }}
                            <button wire:click="toggleFeature({{ $tag->id }})"
                                    class="ml-2 text-white text-opacity-80 hover:text-opacity-100 transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
    @else
        @if($this->selectedTags)
            <div class="mb-3">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-white"
                          style="background-color: {{ $this->selectedTags->color }};">
                        {{ $this->selectedTags->name }}
                    </span>
                    <button wire:click="removeSelection" 
                            class="text-gray-400 hover:text-red-500 text-sm font-medium transition-colors">
                        Rimuovi
                    </button>
                </div>
            </div>
        @endif
    @endif

    {{-- Tag Selection --}}
    @if(($this->isMultipleCategory()) || (!$this->isMultipleCategory() && !$this->selectedTags))
        <div class="space-y-3">
            {{-- Available Tags --}}
            @if($availableTags && $availableTags->count() > 0)
                <div class="max-h-32 overflow-y-auto space-y-2">
                    @foreach($availableTags as $tag)
                        @php
                            $isSelected = $this->isMultipleCategory() 
                                ? in_array($tag->id, $selectedTagIds) 
                                : ($selectedTagId == $tag->id);
                        @endphp
                        
                        <button wire:click="selectTag({{ $tag->id }})"
                                class="w-full flex items-center p-3 text-left rounded-lg border transition-colors {{ $isSelected ? 'border-blue-200 bg-blue-50' : 'border-gray-200 hover:bg-gray-50' }}">
                            
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium text-white mr-3"
                                  style="background-color: {{ $tag->color ?? '#6B7280' }};">
                                {{ $tag->name }}
                            </span>
                            
                            @if($tag->description)
                                <span class="text-sm text-gray-600 flex-1">{{ $tag->description }}</span>
                            @endif
                            
                            @if($isSelected)
                                <svg class="w-4 h-4 text-blue-500 ml-auto" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </button>
                    @endforeach
                </div>
            @endif

            {{-- Create New Button --}}
            <button wire:click="showCreateModal"
                    class="w-full flex items-center justify-center px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-600 hover:border-gray-400 hover:text-gray-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Crea nuovo {{ strtolower($this->categoryLabel) }}
            </button>
        </div>
    @endif

    {{-- Create Modal --}}
    @if($showCreateForm)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-black bg-opacity-50" wire:click="resetCreateForm"></div>
                
                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">
                            Crea {{ $this->categoryLabel }}
                        </h3>
                        <button wire:click="resetCreateForm" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="createTag" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nome {{ $this->categoryLabel }}
                            </label>
                            <input type="text" 
                                   wire:model="newTagName"
                                   placeholder="Es. Antisfondamento, Ecologico, Resistente UV..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('newTagName') border-red-500 @enderror">
                            @error('newTagName')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descrizione</label>
                            <textarea wire:model="newTagDescription"
                                      rows="2"
                                      placeholder="Descrizione opzionale..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('newTagDescription') border-red-500 @enderror"></textarea>
                            @error('newTagDescription')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Colore</label>
                            <input type="color" 
                                   wire:model="newTagColor"
                                   class="w-full h-10 border border-gray-300 rounded-lg cursor-pointer @error('newTagColor') border-red-500 @enderror">
                            @error('newTagColor')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        @if($newTagName)
                            <div class="p-3 bg-gray-50 rounded-lg border">
                                <p class="text-sm font-medium text-gray-700 mb-2">Anteprima:</p>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-white"
                                      style="background-color: {{ $newTagColor }};">
                                    {{ $newTagName }}
                                </span>
                            </div>
                        @endif

                        <div class="flex justify-end space-x-3 pt-4 border-t">
                            <button type="button" 
                                    wire:click="resetCreateForm"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                Annulla
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 transition-colors">
                                @if($this->isMultipleCategory())
                                    Crea e Aggiungi
                                @else
                                    Crea e Seleziona
                                @endif
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Debug Info --}}
    @if(config('app.debug'))
        <div class="mt-2 text-xs text-gray-400">
            Debug: {{ $category }} 
            @if($this->isMultipleCategory())
                - Multi: {{ count($selectedTagIds) }} selected
            @else
                - Single: {{ $selectedTagId ?? 'none' }}
            @endif
            - Tags: {{ $availableTags ? $availableTags->count() : 0 }}
        </div>
    @endif
</div>