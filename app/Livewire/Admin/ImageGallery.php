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
    public $orphanFilter = '';

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
        'orphanFilter' => ['except' => ''],
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

    // 🎯 FILTRO USAGE SEMPLIFICATO
    if (!empty($this->usage)) {
        if ($this->usage === 'used') {
            // Immagini USATE = che hanno un'associazione (imageable_id non null)
            $query->whereNotNull('imageable_id');
        } elseif ($this->usage === 'unused') {
            // Immagini NON USATE = che NON hanno un'associazione (imageable_id è null)
            $query->whereNull('imageable_id');
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
    // Conteggi base
    $totalImages = Image::count();
    $orphanImages = Image::whereNull('imageable_id')->count();
    
    // Immagini per tipo
    $galleryImages = Image::where('type', 'gallery')->count();
    $beautyImages = Image::where('type', 'beauty')->count();
    
    // Immagini non ottimizzate
    $unoptimizedImages = Image::where('is_optimized', false)->count();
    
    // Calcolo storage
    $totalSizeBytes = Image::sum('file_size') ?? 0;
    $totalSizeMb = round($totalSizeBytes / 1024 / 1024, 2);
    $averageSizeKb = $totalImages > 0 ? round(($totalSizeBytes / $totalImages) / 1024, 1) : 0;
    
    // 🎯 CALCOLO UNUSED IMAGES CORRETTO
    $unusedImages = Image::where(function($q) {
        $q->whereNull('imageable_id') // Orfane (senza associazione)
          ->orWhere(function($subQ) {
              // Associate ma mai usate
              $subQ->whereNotNull('imageable_id')
                   ->where('usage_count', '<=', 0);
          })
          ->orWhere(function($subQ) {
              // Non accedute da 30+ giorni o mai accedute
              $subQ->where('last_accessed_at', '<', now()->subDays(30))
                   ->orWhereNull('last_accessed_at');
          })
          ->orWhereIn('status', ['failed', 'processing']) // Status problematici
          ->orWhere('type', 'temp'); // Temporanee
    })->distinct()->count();
    
    // Immagini veramente usate (per calcolo)
    $usedImages = $totalImages - $unusedImages;
    
    return [
        // Conteggi principali
        'total_images' => $totalImages,
        'gallery_images' => $galleryImages,
        'beauty_images' => $beautyImages,
        
        // Stato utilizzo
        'orphan_images' => $orphanImages,
        'unused_images' => $unusedImages,
        'used_images' => $usedImages,
        
        // Ottimizzazione
        'unoptimized_images' => $unoptimizedImages,
        
        // Storage
        'total_size_mb' => $totalSizeMb,
        'average_size_kb' => $averageSizeKb,
        
        // 🔍 Stats aggiuntive utili
        'usage_percentage' => $totalImages > 0 ? round(($usedImages / $totalImages) * 100, 1) : 0,
        'optimization_percentage' => $totalImages > 0 ? round((($totalImages - $unoptimizedImages) / $totalImages) * 100, 1) : 0,
        
        // 🛠️ Debug stats (rimuovi in produzione se non servono)
        'debug_orphan_count' => $orphanImages,
        'debug_never_used' => Image::where('usage_count', '<=', 0)->count(),
        'debug_old_access' => Image::where('last_accessed_at', '<', now()->subDays(30))->count(),
        'debug_never_accessed' => Image::whereNull('last_accessed_at')->count(),
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
            'usage', 'optimized', 'sortBy', 'sortDir',
            'usage', 'optimized', 'orphanFilter'
        ]);
        $this->sortBy = 'created_at';
        $this->sortDir = 'desc';
        $this->resetPage();
    }

    private function applyOrphanFilter($query)
    {
        switch ($this->orphanFilter) {
            case 'no_association':
                // Immagini senza associazione polymorphic
                $query->whereNull('imageable_id');
                break;
                
            case 'unused':
                // Immagini mai usate (usage_count = 0)
                $query->where('usage_count', 0);
                break;
                
            case 'old_unused':
                // Immagini non usate da 30+ giorni
                $query->where(function($q) {
                    $q->where('last_accessed_at', '<', now()->subDays(30))
                      ->orWhereNull('last_accessed_at');
                });
                break;
                
            case 'temp_failed':
                // Immagini temp o failed
                $query->where(function($q) {
                    $q->where('type', 'temp')
                      ->orWhere('status', 'failed');
                });
                break;
                
            case 'all_orphans':
                // Tutte le possibili orfane
                $query->where(function($q) {
                    $q->whereNull('imageable_id')
                      ->orWhere('usage_count', 0)
                      ->orWhere('last_accessed_at', '<', now()->subDays(30))
                      ->orWhereNull('last_accessed_at')
                      ->orWhere('type', 'temp')
                      ->orWhere('status', 'failed');
                });
                break;
        }
    }

    // ✨ NUOVO METODO per reset orphan filter
    public function updatedOrphanFilter()
    {
        $this->resetPage();
    }

    // ✨ NUOVO METODO per click su statistica orfane
    public function showOrphans($type = 'all_orphans')
    {
        $this->orphanFilter = $type;
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
        // Pulisci eventuali errori di validazione precedenti
        $this->resetValidation();
        
        // Pulisci i messaggi flash precedenti
        session()->forget(['success', 'error']);
        
        $this->showUploadModal = true;
        
        \Log::info('Upload modal opened', [
            'user_id' => auth()->id(),
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    public function checkValidationBeforeSubmit()
{
    \Log::info('Pre-validation check', [
        'uploadImages_count' => count($this->uploadImages ?? []),
        'uploadImages_empty' => empty($this->uploadImages),
        'uploadType' => $this->uploadType,
        'uploadProductId' => $this->uploadProductId,
        'uploadBeautyCategory' => $this->uploadBeautyCategory,
        'user_id' => auth()->id()
    ]);

    // Test validazione manualmente
    try {
        $this->validate([
            'uploadImages' => 'required|array|min:1',
            'uploadImages.*' => 'image|mimes:jpeg,png,webp|max:10240',
            'uploadType' => 'required|in:gallery,beauty,product,category,content',
            'uploadProductId' => 'nullable|exists:products,id',
            'uploadBeautyCategory' => 'nullable|in:main,slideshow,header',
        ]);
        
        \Log::info('Validation PASSED');
        session()->flash('success', 'Validazione OK - puoi procedere con upload');
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Validation FAILED', [
            'errors' => $e->errors(),
            'messages' => $e->getMessage()
        ]);
        
        session()->flash('error', 'Errori validazione: ' . json_encode($e->errors()));
    }
}

    public function closeUploadModal()
    {
        $this->showUploadModal = false;
    
        // Pulisci errori di validazione
        $this->resetValidation();
        
        // Reset form
        $this->resetUploadForm();
        
        \Log::info('Upload modal closed');
    }

    private function resetUploadForm()
{
    $this->reset([
        'uploadImages', 
        'uploadType', 
        'uploadProductId', 
        'uploadBeautyCategory'
    ]);
    
    // Re-imposta i valori di default
    $this->uploadType = 'gallery';
    $this->uploadProductId = null;
    $this->uploadBeautyCategory = null;
}



   public function processUpload()
{
    
    try {

        // Step 1: Validazione
        
        $this->validate([
            'uploadImages' => 'required|array|min:1',
            'uploadImages.*' => 'image|mimes:jpeg,png,webp|max:10240',
            'uploadType' => 'required|in:gallery,beauty,product,category,content',
            'uploadProductId' => 'nullable|exists:products,id',
            'uploadBeautyCategory' => 'nullable|in:main,slideshow,header',
        ], [
            'uploadImages.required' => 'Seleziona almeno un\'immagine',
            'uploadImages.*.image' => 'Il file deve essere un\'immagine valida',
            'uploadImages.*.mimes' => 'Formato non supportato (solo JPEG, PNG, WebP)',
            'uploadImages.*.max' => 'L\'immagine è troppo grande (max 10MB)'
        ]);

        // \Log::info('Validation PASSED');

        // Step 2: Preparazione variabili
        $uploadedCount = 0;
        $errors = [];
        $product = null;

        // Step 3: Trova prodotto se specificato
        if ($this->uploadProductId) {
            // \Log::info('Looking for product...', ['product_id' => $this->uploadProductId]);
            
            $product = \App\Models\Product::find($this->uploadProductId);
            if (!$product) {
                throw new \Exception("Prodotto con ID {$this->uploadProductId} non trovato");
            }
            
            // \Log::info('Product found', [
            //     'product_id' => $product->id,
            //     'product_name' => $product->name
            // ]);
        }

        // Step 4: Istanzia ImageService
        // \Log::info('Creating ImageService...');
        $imageService = app(\App\Services\ImageService::class);
        // \Log::info('ImageService created successfully');

        // Step 5: Loop attraverso i file
        // \Log::info('Starting file processing loop', [
        //     'total_files' => count($this->uploadImages)
        // ]);

        foreach ($this->uploadImages as $index => $file) {
            // \Log::info("Processing file {$index}", [
            //     'original_name' => $file->getClientOriginalName(),
            //     'size' => $file->getSize(),
            //     'mime_type' => $file->getMimeType(),
            //     'is_valid' => $file->isValid(),
            //     'error_code' => $file->getError()
            // ]);

            try {
                // Call ImageService
                // \Log::info('Calling imageService->uploadImage...');
                
                $image = $imageService->uploadImage(
                    $file,              // UploadedFile
                    $product,           // Related model (può essere null)  
                    $this->uploadType,  // Tipo immagine
                    auth()->id()        // User ID
                );

                // \Log::info('ImageService upload SUCCESS', [
                //     'image_id' => $image->id,
                //     'clean_name' => $image->clean_name,
                //     'aws_key' => $image->aws_key,
                //     'aws_url' => $image->aws_url
                // ]);

                // Imposta beauty category se specificata
                if ($this->uploadBeautyCategory && $this->uploadType === 'beauty') {
                    // \Log::info('Setting beauty category...');
                    $image->update(['beauty_category' => $this->uploadBeautyCategory]);
                    // \Log::info('Beauty category set', [
                    //     'image_id' => $image->id,
                    //     'category' => $this->uploadBeautyCategory
                    // ]);
                }

                $uploadedCount++;
                // \Log::info("File {$index} processed successfully", [
                //     'uploaded_count' => $uploadedCount
                // ]);

            } catch (\Exception $e) {
                \Log::error("File {$index} processing FAILED", [
                    'file' => $file->getClientOriginalName(),
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                $errors[] = "Errore {$file->getClientOriginalName()}: " . $e->getMessage();
            }
        }

        // \Log::info('File processing loop completed', [
        //     'uploaded_count' => $uploadedCount,
        //     'errors_count' => count($errors)
        // ]);

        // Step 6: Feedback risultati
        if ($uploadedCount > 0) {
            $message = "Caricate {$uploadedCount} immagini con successo!";
            session()->flash('success', $message);
            // \Log::info('Upload SUCCESS', [
            //     'message' => $message,
            //     'uploaded_count' => $uploadedCount
            // ]);
        }

        if (!empty($errors)) {
            $errorMessage = 'Alcuni errori: ' . implode('; ', array_slice($errors, 0, 3));
            session()->flash('error', $errorMessage);
            \Log::warning('Upload with errors', [
                'errors_count' => count($errors),
                'first_errors' => array_slice($errors, 0, 3)
            ]);
        }

        // Se nessun file è stato caricato
        if ($uploadedCount === 0 && empty($errors)) {
            $message = 'Nessun file è stato caricato. Verifica i file selezionati.';
            session()->flash('error', $message);
            \Log::warning('No files uploaded', [
                'message' => $message,
                'uploadImages_count' => count($this->uploadImages ?? [])
            ]);
        }

    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Validation ERROR', [
            'errors' => $e->errors(),
            'messages' => $e->getMessage()
        ]);
        
        // Re-throw validation exception per mostrare errori nel form
        throw $e;
        
    } catch (\Exception $e) {
        // \Log::error('Upload process COMPLETELY FAILED', [
        //     'error' => $e->getMessage(),
        //     'trace' => $e->getTraceAsString(),
        //     'user_id' => auth()->id(),
        //     'file' => $e->getFile(),
        //     'line' => $e->getLine()
        // ]);
        
        session()->flash('error', 'Errore durante upload: ' . $e->getMessage());
        
    } finally {
        // \Log::info('=== UPLOAD CLEANUP START ===');
        
        // Sempre chiudi modal e refresh (anche in caso di errore)
        try {
            $this->closeUploadModal();
            // \Log::info('Modal closed successfully');
            
            $this->dispatch('images-uploaded');
            // \Log::info('Event dispatched successfully');
            
        } catch (\Exception $e) {
            \Log::error('Cleanup FAILED', [
                'error' => $e->getMessage()
            ]);
        }
        
        // \Log::info('=== UPLOAD IMAGES END ===');
    }
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