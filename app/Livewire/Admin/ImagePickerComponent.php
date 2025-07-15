<?php
// app/Livewire/Admin/ImagePickerComponent.php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use App\Models\Image;
use App\Services\ImageService;

class ImagePickerComponent extends Component
{
    use WithPagination;

    // Configuration
    public $isOpen = false;
    public $mode = 'gallery'; // 'gallery' or 'beauty'
    public $title = 'Seleziona Immagine';
    public $description = 'Scegli un\'immagine esistente';
    public $productId = null;
    public $eventName = 'image-selected'; // evento da emettere
    public $multiSelect = false; // permette selezione multipla
    
    // Search & Filter
    public $search = '';
    public $selectedType = '';
    public $selectedStatus = 'active';
    public $sortBy = 'created_at';
    public $sortOrder = 'desc';
    public $perPage = 24;
    
    // Selection
    public $selectedImages = [];
    public $selectedCount = 0;
    
    // State
    public $isLoading = false;

    public function confirmSelection()
{
    if (empty($this->selectedImages)) {
        $this->dispatch('toast', message: 'Seleziona almeno un\'immagine!', type: 'warning');
        return;
    }

    $images = Image::whereIn('id', $this->selectedImages)->get();
    
    // Emetti direttamente al componente ProductEditor
    $this->dispatch('processImageSelection', 
        imageIds: $this->selectedImages,
        images: $images->toArray(),
        mode: $this->mode,
        productId: $this->productId
    )->to('admin.product-editor');
    
    $this->closePicker();
}
    
    protected $queryString = [
        'search' => ['except' => ''],
        'selectedType' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortOrder' => ['except' => 'desc']
    ];

    public function mount($productId = null, $mode = 'gallery', $multiSelect = false, $eventName = 'image-selected')
    {
        $this->productId = $productId;
        $this->mode = $mode;
        $this->multiSelect = $multiSelect;
        $this->eventName = $eventName;
        
        // Set title and description based on mode
        if ($mode === 'beauty') {
            $this->title = 'Seleziona Immagine Sfondo';
            $this->description = 'Scegli un\'immagine per sfondi, slideshow o header';
        }
    }

    #[On('open-image-picker')]
    public function openPicker($data = [])
    {
        $this->isOpen = true;
        
        // Update configuration if provided
        if (isset($data['mode'])) $this->mode = $data['mode'];
        if (isset($data['productId'])) $this->productId = $data['productId'];
        if (isset($data['multiSelect'])) $this->multiSelect = $data['multiSelect'];
        if (isset($data['eventName'])) $this->eventName = $data['eventName'];
        
        // Reset selection
        $this->selectedImages = [];
        $this->selectedCount = 0;
        
        // Reset pagination
        $this->resetPage();
    }

    public function closePicker()
    {
        $this->isOpen = false;
        $this->selectedImages = [];
        $this->selectedCount = 0;
        $this->search = '';
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedType()
    {
        $this->resetPage();
    }

    public function setSortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortOrder = $this->sortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortOrder = 'desc';
        }
        $this->resetPage();
    }

    public function toggleImageSelection($imageId)
    {
        if ($this->multiSelect) {
            if (in_array($imageId, $this->selectedImages)) {
                $this->selectedImages = array_diff($this->selectedImages, [$imageId]);
            } else {
                $this->selectedImages[] = $imageId;
            }
            $this->selectedCount = count($this->selectedImages);
        } else {
            $this->selectedImages = [$imageId];
            $this->selectedCount = 1;
        }
    }

    public function selectImage($imageId)
    {
        \Log::info('🚀 PICKER: Selecting image', ['imageId' => $imageId]);
        if (!$this->multiSelect) {
            // Single selection - emit immediately
            $image = Image::find($imageId);
            if ($image) {
                \Log::info('📤 PICKER: Dispatching event', [
                'eventName' => $this->eventName,
                'imageId' => $imageId
            ]);
                $this->dispatch($this->eventName, [
                    'imageId' => $imageId,
                    'image' => $image->toArray(),
                    'mode' => $this->mode,
                    'productId' => $this->productId
                ]);
                $this->closePicker();
            }
        } else {
            // Multi selection - just toggle
            $this->toggleImageSelection($imageId);
        }
    }

    public function clearSelection()
    {
        $this->selectedImages = [];
        $this->selectedCount = 0;
    }

    public function getImagesProperty()
    {
        $query = Image::query()
            ->where('status', $this->selectedStatus)
            ->where('processing_status', 'completed');

        // Search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('clean_name', 'like', '%' . $this->search . '%')
                  ->orWhere('original_filename', 'like', '%' . $this->search . '%')
                  ->orWhere('alt_text', 'like', '%' . $this->search . '%');
            });
        }

        // Type filter
        if ($this->selectedType) {
            $query->where('type', $this->selectedType);
        } else {
            // Default filter based on mode
            if ($this->mode === 'gallery') {
                $query->whereIn('type', ['gallery', 'product', 'temp']);
            } elseif ($this->mode === 'beauty') {
                $query->whereIn('type', ['beauty', 'product', 'temp']);
            }
        }

        // Exclude images already assigned to current product (if productId provided)
        if ($this->productId) {
            $query->where(function($q) {
                $q->where('imageable_type', '!=', 'App\\Models\\Product')
                  ->orWhere('imageable_id', '!=', $this->productId)
                  ->orWhereNull('imageable_id');
            });
        }

        // Sort
        $query->orderBy($this->sortBy, $this->sortOrder);

        return $query->paginate($this->perPage);
    }

    public function getAvailableTypesProperty()
    {
        return [
            '' => 'Tutti i tipi',
            'gallery' => 'Gallery',
            'beauty' => 'Beauty/Marketing',
            'product' => 'Prodotto',
            'category' => 'Categoria',
            'content' => 'Contenuto',
            'temp' => 'Temporanea'
        ];
    }

    public function deleteImage($imageId)
    {
        try {
            $image = Image::find($imageId);
            if (!$image) {
                $this->dispatch('toast', message: 'Immagine non trovata!', type: 'error');
                return;
            }

            $imageService = app(ImageService::class);
            $success = $imageService->deleteImage($image);
            
            if ($success) {
                $this->dispatch('toast', message: 'Immagine eliminata!', type: 'success');
                
                // Remove from selection if selected
                $this->selectedImages = array_diff($this->selectedImages, [$imageId]);
                $this->selectedCount = count($this->selectedImages);
            } else {
                $this->dispatch('toast', message: 'Errore durante eliminazione!', type: 'error');
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Errore: ' . $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.admin.image-picker-component', [
            'images' => $this->images,
            'availableTypes' => $this->availableTypes
        ]);
    }
}