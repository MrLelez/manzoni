<?php

namespace App\Livewire\Admin;

use App\Models\Image;
use App\Models\Product;
use App\Services\ImageService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;

class ImageGallery extends Component
{
    use WithPagination, WithFileUploads;

    // Filtri e ricerca
    public $search = '';
    public $type = '';
    public $status = '';
    public $beautyCategory = '';
    public $usage = '';
    public $optimized = '';
    public $sortBy = 'created_at';
    public $sortDir = 'desc';

    // Bulk operations
    public $bulkMode = false;
    public $selectedImages = [];

    // Modal states
    public $showUploadModal = false;
    public $showImageDetail = false;
    public $selectedImageId = null;
    public $isMobile = false;

    // Upload form
    public $uploadImages = [];
    public $uploadType = 'gallery';
    public $uploadProductId = null;
    public $uploadBeautyCategory = null;

    // Detail edit form
    public $editingImage = null;
    public $editAltText = '';
    public $editCaption = '';
    public $editBeautyCategory = '';
    public $editTags = '';

    // Association
    public $showAssociateModal = false;
    public $associateImageId = null;
    public $associateProductId = null;
    public $associateType = 'gallery';
    public $associateBeautyCategory = null;
    public $setAsPrimary = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'type' => ['except' => ''],
        'status' => ['except' => ''],
        'beautyCategory' => ['except' => ''],
        'usage' => ['except' => ''],
        'optimized' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDir' => ['except' => 'desc'],
    ];

    public function mount()
    {
        // Detect mobile from user agent (basic detection)
        $this->isMobile = $this->detectMobile();
    }

    private function detectMobile()
    {
        $userAgent = request()->header('User-Agent');
        return preg_match('/(android|iphone|ipad|mobile)/i', $userAgent);
    }

    #[Computed]
    public function images()
    {
        $query = Image::with(['imageable', 'uploader']);

        // Apply filters
        if (!empty($this->search)) {
            $query->search($this->search);
        }

        if (!empty($this->type)) {
            $query->where('type', $this->type);
        }

        if (!empty($this->status)) {
            $query->where('status', $this->status);
        }

        if (!empty($this->beautyCategory)) {
            $query->where('beauty_category', $this->beautyCategory);
        }

        if (!empty($this->usage)) {
            if ($this->usage === 'used') {
                $query->where('usage_count', '>', 0);
            } elseif ($this->usage === 'unused') {
                $query->where('usage_count', 0);
            }
        }

        if ($this->optimized !== '') {
            $query->where('is_optimized', (bool) $this->optimized);
        }

        return $query->orderBy($this->sortBy, $this->sortDir)->paginate(24);
    }

    #[Computed]
    public function stats()
    {
        return [
            'total_images' => Image::count(),
            'gallery_images' => Image::where('type', 'gallery')->count(),
            'beauty_images' => Image::where('type', 'beauty')->count(),
            'orphan_images' => Image::whereNull('imageable_id')->count(),
            'unoptimized_images' => Image::where('is_optimized', false)->count(),
            'total_size_mb' => round(Image::sum('file_size') / 1024 / 1024, 2),
            'average_size_kb' => round(Image::avg('file_size') / 1024, 1)
        ];
    }

    #[Computed]
    public function products()
    {
        return Product::active()->select('id', 'name', 'sku')->orderBy('name')->get();
    }

    #[Computed]
    public function selectedImage()
    {
        if ($this->selectedImageId) {
            return Image::with(['imageable', 'uploader'])
                       ->find($this->selectedImageId);
        }
        return null;
    }

    // =====================================
    // FILTERING & SEARCH
    // =====================================

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedType()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function updatedBeautyCategory()
    {
        $this->resetPage();
    }

    public function updatedUsage()
    {
        $this->resetPage();
    }

    public function updatedOptimized()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDir = 'asc';
        }
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset([
            'search', 'type', 'status', 'beautyCategory', 
            'usage', 'optimized', 'sortBy', 'sortDir'
        ]);
        $this->sortBy = 'created_at';
        $this->sortDir = 'desc';
        $this->resetPage();
    }

    // =====================================
    // IMAGE DETAIL MODAL/PAGE
    // =====================================

    public function openImageDetail($imageId)
    {
        $this->selectedImageId = $imageId;
        
        if ($this->isMobile) {
            // Su mobile, redirect a pagina dedicata
            return redirect()->route('admin.images.show', $imageId);
        } else {
            // Su desktop, apri modal
            $this->showImageDetail = true;
            $this->loadImageForEdit();
        }
    }

    public function closeImageDetail()
    {
        $this->showImageDetail = false;
        $this->selectedImageId = null;
        $this->resetImageEdit();
    }

    private function loadImageForEdit()
    {
        if ($this->selectedImage) {
            $this->editAltText = $this->selectedImage->alt_text ?? '';
            $this->editCaption = $this->selectedImage->caption ?? '';
            $this->editBeautyCategory = $this->selectedImage->beauty_category ?? '';
            $this->editTags = is_array($this->selectedImage->tags) 
                ? implode(', ', $this->selectedImage->tags) 
                : '';
        }
    }

    private function resetImageEdit()
    {
        $this->reset([
            'editAltText', 'editCaption', 'editBeautyCategory', 'editTags'
        ]);
    }

    public function updateImageDetails()
    {
        if (!$this->selectedImage) return;

        $this->validate([
            'editAltText' => 'nullable|string|max:255',
            'editCaption' => 'nullable|string|max:500',
            'editBeautyCategory' => 'nullable|in:main,slideshow,header',
            'editTags' => 'nullable|string',
        ]);

        $tags = !empty($this->editTags) 
            ? array_map('trim', explode(',', $this->editTags))
            : null;

        $this->selectedImage->update([
            'alt_text' => $this->editAltText,
            'caption' => $this->editCaption,
            'beauty_category' => $this->editBeautyCategory,
            'tags' => $tags,
        ]);

        $this->dispatch('image-updated');
        session()->flash('success', 'Dettagli immagine aggiornati con successo!');
    }

    // =====================================
    // UPLOAD MODAL
    // =====================================

    public function openUploadModal()
    {
        $this->showUploadModal = true;
    }

    public function closeUploadModal()
    {
        $this->showUploadModal = false;
        $this->resetUploadForm();
    }

    private function resetUploadForm()
    {
        $this->reset([
            'uploadImages', 'uploadType', 'uploadProductId', 'uploadBeautyCategory'
        ]);
    }

    public function uploadImages()
    {
        $this->validate([
            'uploadImages' => 'required|array|min:1',
            'uploadImages.*' => 'image|mimes:jpeg,png,webp|max:10240',
            'uploadType' => 'required|in:gallery,beauty,product,category,content',
            'uploadProductId' => 'nullable|exists:products,id',
            'uploadBeautyCategory' => 'nullable|in:main,slideshow,header',
        ]);

        $imageService = app(ImageService::class);
        $uploadedCount = 0;
        $errors = [];

        foreach ($this->uploadImages as $file) {
            try {
                $imageable = $this->uploadProductId ? Product::find($this->uploadProductId) : null;
                
                // Usa il metodo uploadImage del tuo ImageService esistente
                $image = $imageService->uploadImage(
                    $file,  // UploadedFile
                    $imageable,
                    $this->uploadType,
                    auth()->id()
                );

                // Set beauty category if specified
                if ($this->uploadBeautyCategory && $this->uploadType === 'beauty') {
                    $image->update(['beauty_category' => $this->uploadBeautyCategory]);
                }

                $uploadedCount++;

            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if ($uploadedCount > 0) {
            session()->flash('success', "Caricate {$uploadedCount} immagini con successo!");
        }

        if (!empty($errors)) {
            session()->flash('error', 'Alcuni errori: ' . implode(', ', $errors));
        }

        $this->closeUploadModal();
        $this->dispatch('images-uploaded');
    }

    // =====================================
    // BULK OPERATIONS
    // =====================================

    public function toggleBulkMode()
    {
        $this->bulkMode = !$this->bulkMode;
        if (!$this->bulkMode) {
            $this->selectedImages = [];
        }
    }

    public function updatedSelectedImages()
    {
        // This fires when checkboxes change
    }

    public function selectAll()
    {
        $this->selectedImages = $this->images->pluck('id')->toArray();
    }

    public function deselectAll()
    {
        $this->selectedImages = [];
    }

    public function bulkOptimize()
    {
        if (empty($this->selectedImages)) {
            session()->flash('error', 'Seleziona almeno un\'immagine');
            return;
        }

        $imageService = app(ImageService::class);
        $optimized = 0;

        foreach ($this->selectedImages as $imageId) {
            try {
                $image = Image::find($imageId);
                if ($image && !$image->is_optimized) {
                    $imageService->optimizeImage($image);
                    $optimized++;
                }
            } catch (\Exception $e) {
                \Log::error("Bulk optimization failed for image {$imageId}: " . $e->getMessage());
            }
        }

        session()->flash('success', "Ottimizzate {$optimized} immagini");
        $this->selectedImages = [];
        $this->dispatch('images-optimized');
    }

    public function bulkDelete()
    {
        if (empty($this->selectedImages)) {
            session()->flash('error', 'Seleziona almeno un\'immagine');
            return;
        }

        $deleted = 0;
        foreach ($this->selectedImages as $imageId) {
            $image = Image::find($imageId);
            if ($image) {
                $image->delete(); // Soft delete
                $deleted++;
            }
        }

        session()->flash('success', "Eliminate {$deleted} immagini");
        $this->selectedImages = [];
        $this->dispatch('images-deleted');
    }

    // =====================================
    // SINGLE IMAGE ACTIONS
    // =====================================

    public function optimizeImage($imageId)
    {
        try {
            $image = Image::find($imageId);
            if ($image && !$image->is_optimized) {
                $imageService = app(ImageService::class);
                $result = $imageService->optimizeImage($image);
                
                if ($result['optimized']) {
                    session()->flash('success', $result['message'] ?? 'Immagine ottimizzata con successo!');
                } else {
                    session()->flash('info', $result['message'] ?? 'Immagine già ottimizzata');
                }
            } else {
                session()->flash('info', 'Immagine già ottimizzata o non trovata');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Errore durante ottimizzazione: ' . $e->getMessage());
        }

        $this->dispatch('image-optimized');
    }

    public function deleteImage($imageId)
    {
        $image = Image::find($imageId);
        if ($image) {
            $image->delete();
            session()->flash('success', 'Immagine eliminata con successo');
            
            // Close modal if this image was open
            if ($this->selectedImageId === $imageId) {
                $this->closeImageDetail();
            }
        }

        $this->dispatch('image-deleted');
    }

    // =====================================
    // ASSOCIATION
    // =====================================

    public function openAssociateModal($imageId)
    {
        $this->associateImageId = $imageId;
        $this->showAssociateModal = true;
    }

    public function closeAssociateModal()
    {
        $this->showAssociateModal = false;
        $this->reset([
            'associateImageId', 'associateProductId', 'associateType', 
            'associateBeautyCategory', 'setAsPrimary'
        ]);
    }

    public function associateToProduct()
    {
        $this->validate([
            'associateProductId' => 'required|exists:products,id',
            'associateType' => 'required|in:gallery,beauty',
            'associateBeautyCategory' => 'nullable|in:main,slideshow,header',
            'setAsPrimary' => 'boolean'
        ]);

        $image = Image::find($this->associateImageId);
        $product = Product::find($this->associateProductId);

        if ($image && $product) {
            // Update image association
            $image->update([
                'imageable_type' => Product::class,
                'imageable_id' => $product->id,
                'type' => $this->associateType,
                'beauty_category' => $this->associateBeautyCategory,
                'usage_count' => $image->usage_count + 1
            ]);

            // Set as primary if requested
            if ($this->setAsPrimary) {
                $product->setPrimaryImage($image);
            }

            session()->flash('success', "Immagine associata al prodotto {$product->name}");
            $this->closeAssociateModal();
            $this->dispatch('image-associated');
        }
    }

    public function dissociateFromProduct($imageId)
    {
        $image = Image::find($imageId);
        if ($image && $image->imageable) {
            $oldProduct = $image->imageable;
            
            // Clear association
            $image->update([
                'imageable_type' => null,
                'imageable_id' => null,
                'type' => 'temp',
                'beauty_category' => null
            ]);

            // Clear primary if this was primary
            if ($oldProduct instanceof Product && $oldProduct->primary_image_id === $image->id) {
                $oldProduct->clearPrimaryImage();
            }

            session()->flash('success', 'Immagine disassociata dal prodotto');
            $this->dispatch('image-dissociated');
        }
    }

    // =====================================
    // LISTENERS
    // =====================================

    #[On('image-updated')]
    #[On('images-uploaded')]
    #[On('images-optimized')]
    #[On('images-deleted')]
    #[On('image-optimized')]
    #[On('image-deleted')]
    #[On('image-associated')]
    #[On('image-dissociated')]
    public function refreshImages()
    {
        // This will trigger a re-render of the computed properties
        unset($this->images);
        unset($this->stats);
    }

    public function render()
    {
        return view('livewire.admin.image-gallery');
    }
}