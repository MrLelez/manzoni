<?php
// app/Livewire/Admin/ProductEditor.php - VERSIONE CORRETTA COMPLETA

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use App\Models\Product;
use App\Models\Category;
use App\Models\Tag;
use App\Services\ImageService;
use Illuminate\Support\Str;
use Spatie\Activitylog\Facades\Activity;

class ProductEditor extends Component
{
    use WithFileUploads;

    // Product data
    public Product $product;
    public $name;
    public $sku;
    public $description;
    public $short_description;
    public $base_price;
    public $status;
    public $category_id;
    public $material;
    public $color;
    public $is_featured;
    public $is_customizable;
    
    // UI states
    public $editingField = null;
    public $showImageUpload = false;
    public $uploadedImages = [];
    public $isUploading = false;
    public $showTagForm = false;
    public $showCategoryForm = false;
    public $newTagName = '';
    public $newCategoryName = '';
    public $newCategoryDescription = '';
    public $editingTag = null;
    public $editingCategory = null;
    public $editTagName = '';
    public $editCategoryName = '';
    public $showGalleryAdvanced = false;
    
    // Data collections
    public $categories;
    public $tags;
    public $selectedTags = [];

    public $showUploadModal = false;
    public $uploadMode = null; // 'gallery' o 'beauty'
    
    // Validation rules
    protected $rules = [
        'name' => 'required|string|max:255',
        'sku' => 'required|string|max:100',
        'description' => 'nullable|string',
        'short_description' => 'nullable|string|max:500',
        'base_price' => 'required|numeric|min:0',
        'status' => 'required|in:active,inactive,draft',
        'category_id' => 'nullable|exists:categories,id',
        'material' => 'nullable|string|max:255',
        'color' => 'nullable|string|max:100',
        'is_featured' => 'boolean',
        'is_customizable' => 'boolean',
        'newTagName' => 'required|string|max:100|unique:tags,name',
        'newCategoryName' => 'required|string|max:255|unique:categories,name',
        'newCategoryDescription' => 'nullable|string|max:500',
        'editTagName' => 'required|string|max:100',
        'editCategoryName' => 'required|string|max:255',
        'uploadedImages.*' => 'image|max:10240',
    ];

    public function mount(Product $product)
    {
        // Load product with all relations
        $this->product = Product::with(['primaryImage', 'images', 'tags', 'category'])
                               ->find($product->id);
        
        // Initialize properties
        $this->name = $this->product->name;
        $this->sku = $this->product->sku;
        $this->description = $this->product->description;
        $this->base_price = $this->product->base_price;
        $this->status = $this->product->status ?? 'draft';
        $this->category_id = $this->product->category_id;
        $this->is_featured = $this->product->is_featured ?? false;
        
        // Load categories and tags safely
        try {
            $this->categories = \App\Models\Category::where('is_active', true)->get();
        } catch (\Exception $e) {
            $this->categories = collect();
        }
        
        try {
            $this->tags = \App\Models\Tag::where('is_active', true)->get();
            $this->selectedTags = $this->product->tags ? $this->product->tags->pluck('id')->toArray() : [];
        } catch (\Exception $e) {
            $this->tags = collect();
            $this->selectedTags = [];
        }
    }

    // ===========================
    // BASIC PRODUCT METHODS
    // ===========================

    public function toggleStatus()
    {
        $statuses = ['active', 'inactive', 'draft'];
        $currentIndex = array_search($this->status, $statuses);
        $nextIndex = ($currentIndex + 1) % count($statuses);
        
        $oldStatus = $this->status;
        $this->status = $statuses[$nextIndex];
        
        $this->product->status = $this->status;
        $this->product->save();
        
        Activity::performedOn($this->product)
            ->causedBy(auth()->user())
            ->withProperties([
                'old_status' => $oldStatus,
                'new_status' => $this->status
            ])
            ->log('Changed product status');
        
        $this->dispatch('status-changed', status: $this->status);
        $this->dispatch('toast', message: 'Status aggiornato!', type: 'success');
    }

    public function toggleFeatured()
    {
        $this->is_featured = !$this->is_featured;
        $this->product->is_featured = $this->is_featured;
        $this->product->save();
        
        Activity::performedOn($this->product)
            ->causedBy(auth()->user())
            ->log($this->is_featured ? 'Featured product' : 'Unfeatured product');
        
        $this->dispatch('featured-toggled', featured: $this->is_featured);
        $this->dispatch('toast', message: $this->is_featured ? 'Prodotto in evidenza!' : 'Rimosso da evidenza', type: 'success');
    }

    public function updateCategory()
    {
        $this->validateOnly('category_id');
        
        $oldCategory = $this->product->category;
        $this->product->category_id = $this->category_id;
        $this->product->save();
        
        $newCategory = Category::find($this->category_id);
        
        Activity::performedOn($this->product)
            ->causedBy(auth()->user())
            ->withProperties([
                'old_category' => $oldCategory?->name,
                'new_category' => $newCategory?->name
            ])
            ->log('Changed product category');
        
        $this->dispatch('category-updated');
        $this->dispatch('toast', message: 'Categoria aggiornata!', type: 'success');
    }

    public function updateTags()
    {
        $this->product->tags()->sync($this->selectedTags);
        
        Activity::performedOn($this->product)
            ->causedBy(auth()->user())
            ->withProperties(['tags' => $this->selectedTags])
            ->log('Updated product tags');
        
        $this->dispatch('tags-updated');
        $this->dispatch('toast', message: 'Tags aggiornati!', type: 'success');
    }

    // ===========================
    // FIELD EDITING METHODS
    // ===========================

    public function startEditing($field)
    {
        $this->editingField = $field;
    }

    public function stopEditing()
    {
        $this->editingField = null;
    }

    public function saveField($field)
    {
        $this->validateOnly($field);
        
        $this->product->update([
            $field => $this->{$field}
        ]);
        
        Activity::performedOn($this->product)
            ->causedBy(auth()->user())
            ->withProperties([
                'field' => $field,
                'new_value' => $this->{$field}
            ])
            ->log("Updated product {$field}");
        
        $this->editingField = null;
        $this->dispatch('field-saved', field: $field);
        $this->dispatch('toast', message: ucfirst($field) . ' aggiornato!', type: 'success');
    }

    // ===========================
    // IMAGE UPLOAD METHODS ✨
    // ===========================

    // ===========================
    // MODAL UPLOAD METHODS ✨
    // ===========================

    /**
     * Apri modal per upload in modalità specifica
     */
    public function openUploadModal($mode)
    {
        $this->uploadMode = $mode; // 'gallery' o 'beauty'
        $this->showUploadModal = true;
        $this->uploadedImages = []; // Reset
    }

    /**
     * Chiudi modal upload
     */
    public function closeUploadModal()
    {
        $this->showUploadModal = false;
        $this->uploadMode = null;
        $this->uploadedImages = [];
    }

    /**
     * Upload con destinazione basata su modalità
     */
    public function updatedUploadedImages()
{
    if (!$this->uploadMode) {
        $this->dispatch('toast', message: 'Modalità upload non impostata!', type: 'error');
        return;
    }

    $this->validate([
        'uploadedImages.*' => 'image|max:10240',
    ]);

    $uploadedCount = 0;
    $wasFirstGalleryUpload = $this->uploadMode === 'gallery' && $this->product->galleryImages()->count() === 0;

    foreach ($this->uploadedImages as $image) {
        try {
            $imageService = app(ImageService::class);
            
            // Upload con tipo basato su modalità
            $uploadedImage = $imageService->uploadImage(
                $image,
                $this->product,
                $this->uploadMode, // 'gallery' o 'beauty'
                auth()->id()
            );
            
            // Se è beauty, inizia senza categoria (da assegnare manualmente)
            if ($this->uploadMode === 'beauty') {
                $uploadedImage->update(['beauty_category' => null]);
            }
            
            $uploadedCount++;
            
            Activity::performedOn($this->product)
                ->causedBy(auth()->user())
                ->withProperties([
                    'image_id' => $uploadedImage->id, 
                    'type' => $this->uploadMode
                ])
                ->log("Added {$this->uploadMode} image");
                
        } catch (\Exception $e) {
            \Log::error("Upload failed: " . $e->getMessage());
            $this->dispatch('toast', message: 'Errore upload: ' . $e->getMessage(), type: 'error');
            return;
        }
    }
    
    // Reset e reload
    $this->uploadedImages = [];
    $this->reloadProduct();
    
    // Se era il primo upload gallery e non c'è primary, imposta automaticamente
    if ($wasFirstGalleryUpload && $uploadedCount > 0 && !$this->product->hasPrimaryImage()) {
        $firstImage = $this->product->galleryImages()->first();
        if ($firstImage) {
            $this->product->setPrimaryImage($firstImage);
            $this->reloadProduct();
        }
    }
    
    $destination = $this->uploadMode === 'gallery' ? 'Gallery' : 'Sfondi';
    $this->dispatch('toast', message: "{$uploadedCount} immagini caricate in {$destination}!", type: 'success');
    
    // Per beauty, mostra suggerimento di assegnare categorie
    if ($this->uploadMode === 'beauty' && $uploadedCount > 0) {
        $this->dispatch('toast', message: 'Hover sulle immagini per assegnare categorie!', type: 'info');
    }
    
    // Chiudi modal
    $this->closeUploadModal();
}

public function debugImage($imageId) 
{
    $image = \App\Models\Image::find($imageId);
    \Log::info('DEBUG IMAGE:', [
        'id' => $image->id,
        'type' => $image->type,
        'beauty_category' => $image->beauty_category,
        'fillable' => $image->getFillable()
    ]);
    
    $this->dispatch('toast', message: 'Debug logged!', type: 'info');
}


    public function deleteImage($imageId)
    {
        $image = $this->product->images()->find($imageId);
        
        if (!$image) {
            $this->dispatch('toast', message: 'Immagine non trovata!', type: 'error');
            return;
        }
        
        try {
            $imageType = 'gallery';
            
            // Determine what type of image we're deleting
            if ($this->product->isPrimaryImage($image)) {
                $imageType = 'primary';
                $this->product->clearPrimaryImage();
            } elseif ($image->isBeauty()) {
                $imageType = 'beauty';
            }
            
            $image->delete();
            
            Activity::performedOn($this->product)
                ->causedBy(auth()->user())
                ->withProperties([
                    'image_id' => $imageId,
                    'image_type' => $imageType
                ])
                ->log("Deleted {$imageType} image");
            
            $this->reloadProduct();
            $this->dispatch('image-deleted');
            $this->dispatch('toast', message: 'Immagine eliminata!', type: 'success');
            
        } catch (\Exception $e) {
            \Log::error('Failed to delete image: ' . $e->getMessage());
            $this->dispatch('toast', message: 'Errore: ' . $e->getMessage(), type: 'error');
        }
    }

    // ===========================
    // PRIMARY IMAGE METHODS ✨
    // ===========================

    
    /**
 * Delete gallery image
 */
public function deleteGalleryImage($imageId)
{
    $image = $this->product->galleryImages()->find($imageId);
    
    if (!$image) {
        $this->dispatch('toast', message: 'Immagine non trovata!', type: 'error');
        return;
    }
    
    try {
        // Clear primary se necessario
        if ($this->product->isPrimaryImage($image)) {
            $this->product->clearPrimaryImage();
        }
        
        $image->delete();
        $this->reloadProduct();
        
        Activity::performedOn($this->product)
            ->causedBy(auth()->user())
            ->withProperties(['image_id' => $imageId])
            ->log('Deleted gallery image');
        
        $this->dispatch('toast', message: 'Immagine eliminata!', type: 'success');
        
    } catch (\Exception $e) {
        \Log::error('Failed to delete gallery image: ' . $e->getMessage());
        $this->dispatch('toast', message: 'Errore: ' . $e->getMessage(), type: 'error');
    }
}


/**
 * Delete beauty image
 */
public function deleteBeautyImage($imageId)
{
    $image = $this->product->beautyImages()->find($imageId);
    
    if (!$image) {
        $this->dispatch('toast', message: 'Immagine sfondo non trovata!', type: 'error');
        return;
    }
    
    try {
        $image->delete();
        $this->reloadProduct();
        
        Activity::performedOn($this->product)
            ->causedBy(auth()->user())
            ->withProperties(['image_id' => $imageId])
            ->log('Deleted beauty image');
        
        $this->dispatch('toast', message: 'Immagine sfondo eliminata!', type: 'success');
        
    } catch (\Exception $e) {
        \Log::error('Failed to delete beauty image: ' . $e->getMessage());
        $this->dispatch('toast', message: 'Errore: ' . $e->getMessage(), type: 'error');
    }
}

/**
 * Generate alt texts per entrambe le gallery
 */
public function generateAltTexts()
{
    $galleryImages = $this->product->galleryImages()->whereNull('alt_text')->orWhere('alt_text', '')->get();
    $beautyImages = $this->product->beautyImages()->whereNull('alt_text')->orWhere('alt_text', '')->get();
    $updated = 0;
    
    // Alt text per gallery
    foreach ($galleryImages as $image) {
        $altText = $this->product->name;
        
        if ($this->product->isPrimaryImage($image)) {
            $altText .= ' - Immagine principale';
        } else {
            $altText .= ' - Gallery';
        }
        
        $image->update(['alt_text' => $altText]);
        $updated++;
    }
    
    // Alt text per beauty
    foreach ($beautyImages as $image) {
        $altText = $this->product->name . ' - Immagine sfondo';
        $image->update(['alt_text' => $altText]);
        $updated++;
    }
    
    $this->reloadProduct();
    
    Activity::performedOn($this->product)
        ->causedBy(auth()->user())
        ->withProperties(['updated_count' => $updated])
        ->log('Generated alt texts for all images');
    
    $this->dispatch('toast', message: "Alt text generati per {$updated} immagini!", type: 'success');
}

    public function clearPrimaryImage()
    {
        try {
            $this->product->clearPrimaryImage();
            $this->reloadProduct();
            
            $this->dispatch('primary-image-cleared');
            $this->dispatch('toast', message: 'Immagine principale rimossa!', type: 'success');
            
        } catch (\Exception $e) {
            \Log::error('Failed to clear primary image: ' . $e->getMessage());
            $this->dispatch('toast', message: 'Errore: ' . $e->getMessage(), type: 'error');
        }
    }

    // ===========================
    // BEAUTY SYSTEM METHODS ✨
    // ===========================

    /**
     * Mark an image as beauty type
     */
    public function markAsBeauty($imageId)
    {
        try {
            $image = $this->product->images()->find($imageId);
            
            if (!$image) {
                $this->dispatch('toast', message: 'Immagine non trovata!', type: 'error');
                return;
            }
            
            $this->product->markAsBeauty($image);
            $this->reloadProduct();
            
            Activity::performedOn($this->product)
                ->causedBy(auth()->user())
                ->withProperties(['image_id' => $imageId])
                ->log('Marked image as beauty');
            
            $this->dispatch('beauty-marked', imageId: $imageId);
            $this->dispatch('toast', message: 'Immagine marcata come Beauty!', type: 'success');
            
        } catch (\Exception $e) {
            \Log::error('Failed to mark as beauty: ' . $e->getMessage());
            $this->dispatch('toast', message: 'Errore: ' . $e->getMessage(), type: 'error');
        }
    }

    /**
     * Mark an image as gallery (remove from beauty)
     */
    public function markAsGallery($imageId)
    {
        try {
            $image = $this->product->images()->find($imageId);
            
            if (!$image) {
                $this->dispatch('toast', message: 'Immagine non trovata!', type: 'error');
                return;
            }
            
            $this->product->markAsGallery($image);
            $this->reloadProduct();
            
            Activity::performedOn($this->product)
                ->causedBy(auth()->user())
                ->withProperties(['image_id' => $imageId])
                ->log('Marked image as gallery');
            
            $this->dispatch('beauty-cleared', imageId: $imageId);
            $this->dispatch('toast', message: 'Immagine rimossa da Beauty!', type: 'info');
            
        } catch (\Exception $e) {
            \Log::error('Failed to mark as gallery: ' . $e->getMessage());
            $this->dispatch('toast', message: 'Errore: ' . $e->getMessage(), type: 'error');
        }
    }

    /**
     * Set second image as beauty (quick action)
     */
    public function setBeautyToSecond()
    {
        $secondImage = $this->product->galleryImages()->skip(1)->first();
        
        if ($secondImage) {
            $this->markAsBeauty($secondImage->id);
        } else {
            $this->dispatch('toast', message: 'Non ci sono abbastanza immagini gallery!', type: 'error');
        }
    }


// ===========================
// GALLERY IMAGE METHODS ✨
// ===========================

/**
 * Set primary image (solo per gallery)
 */
public function setPrimaryImage($imageId)
{
    try {
        $image = $this->product->galleryImages()->find($imageId);
        
        if (!$image) {
            $this->dispatch('toast', message: 'Immagine non trovata!', type: 'error');
            return;
        }
        
        $this->product->setPrimaryImage($image);
        $this->reloadProduct();
        
        Activity::performedOn($this->product)
            ->causedBy(auth()->user())
            ->withProperties(['image_id' => $imageId])
            ->log('Set primary image');
        
        $this->dispatch('toast', message: 'Primary impostata!', type: 'success');
        
    } catch (\Exception $e) {
        \Log::error('Failed to set primary: ' . $e->getMessage());
        $this->dispatch('toast', message: 'Errore: ' . $e->getMessage(), type: 'error');
    }
}

public function assignBeautyCategory($imageId, $category)
{
    \Log::info('🔥 assignBeautyCategory called', [
        'image_id' => $imageId,
        'category' => $category
    ]);

    try {
        // Query diretta invece di relazione
        $image = \App\Models\Image::where('id', $imageId)
                                 ->where('imageable_type', 'App\\Models\\Product')
                                 ->where('imageable_id', $this->product->id)
                                 ->where('type', 'beauty')
                                 ->first();
        
        if (!$image) {
            $this->dispatch('toast', message: 'Immagine beauty non trovata!', type: 'error');
            return;
        }

        \Log::info('✅ Image found', [
            'id' => $image->id,
            'current_category' => $image->beauty_category
        ]);

        // Update diretto
        $updated = \DB::table('images')
                     ->where('id', $imageId)
                     ->update(['beauty_category' => $category]);
        
        \Log::info('🔄 Direct DB update', [
            'updated_rows' => $updated,
            'category' => $category
        ]);

        // Verifica update
        $updatedImage = \App\Models\Image::find($imageId);
        \Log::info('✅ After update verification', [
            'id' => $updatedImage->id,
            'beauty_category' => $updatedImage->beauty_category
        ]);

        // Clear ALL cache e forza reload completo
        $this->resetComponent();
        
        $categoryNames = [
            'main' => 'Sfondo Principale',
            'slideshow' => 'Slideshow', 
            'header' => 'Header'
        ];
        
        $this->dispatch('toast', message: "✅ Assegnata a {$categoryNames[$category]}!", type: 'success');
        
    } catch (\Exception $e) {
        \Log::error('❌ assignBeautyCategory failed', [
            'error' => $e->getMessage(),
            'line' => $e->getLine()
        ]);
        $this->dispatch('toast', message: 'Errore: ' . $e->getMessage(), type: 'error');
    }
}


/**
 * Rimuovi una beauty image dalla sua categoria
 */
public function removeFromBeautyCategory($imageId)
{
    try {
        $result = \DB::table('images')
                    ->where('id', $imageId)
                    ->update(['beauty_category' => null]);
        
        \Log::info('🗑️ Category removed', [
            'image_id' => $imageId,
            'rows_affected' => $result
        ]);
        
        $this->resetComponent();
        $this->dispatch('toast', message: 'Categoria rimossa!', type: 'info');
        
    } catch (\Exception $e) {
        \Log::error('❌ Remove category failed', ['error' => $e->getMessage()]);
        $this->dispatch('toast', message: 'Errore: ' . $e->getMessage(), type: 'error');
    }
}
/**
 * Ottieni beauty images per categoria specifica
 */
public function getBeautyByCategory($category)
{
    return $this->product->getBeautyByCategory($category);
}


    /**
     * Set a random gallery image as beauty
     */
    public function setRandomAsBeauty()
    {
        $galleryImages = $this->product->galleryImages()->get();
        
        if ($galleryImages->count() > 0) {
            $randomImage = $galleryImages->random();
            $this->markAsBeauty($randomImage->id);
        } else {
            $this->dispatch('toast', message: 'Nessuna immagine gallery disponibile!', type: 'error');
        }
    }

    /**
     * Clear all beauty images (make them gallery)
     */
    public function clearAllBeauty()
    {
        try {
            $beautyImages = $this->product->beautyImages()->get();
            $count = $beautyImages->count();
            
            if ($count === 0) {
                $this->dispatch('toast', message: 'Nessuna immagine beauty da rimuovere!', type: 'info');
                return;
            }
            
            foreach ($beautyImages as $image) {
                $this->product->markAsGallery($image);
            }
            
            $this->reloadProduct();
            
            Activity::performedOn($this->product)
                ->causedBy(auth()->user())
                ->withProperties(['count' => $count])
                ->log('Cleared all beauty images');
            
            $this->dispatch('toast', message: "{$count} immagini beauty rimosse!", type: 'success');
            
        } catch (\Exception $e) {
            \Log::error('Failed to clear all beauty: ' . $e->getMessage());
            $this->dispatch('toast', message: 'Errore: ' . $e->getMessage(), type: 'error');
        }
    }

    /**
     * Swap primary and first beauty image
     */
    public function swapPrimaryAndBeauty()
    {
        try {
            $primaryImage = $this->product->primaryImage;
            $firstBeauty = $this->product->beautyImages()->first();
            
            if (!$primaryImage || !$firstBeauty) {
                $this->dispatch('toast', message: 'Mancano immagini primary o beauty per lo swap!', type: 'error');
                return;
            }
            
            // Temporarily clear primary
            $this->product->clearPrimaryImage();
            
            // Mark current primary as beauty
            $this->product->markAsBeauty($primaryImage);
            
            // Mark current beauty as gallery then set as primary
            $this->product->markAsGallery($firstBeauty);
            $this->product->setPrimaryImage($firstBeauty);
            
            $this->reloadProduct();
            
            Activity::performedOn($this->product)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old_primary_id' => $primaryImage->id,
                    'new_primary_id' => $firstBeauty->id
                ])
                ->log('Swapped primary and beauty images');
            
            $this->dispatch('toast', message: 'Primary e Beauty scambiate!', type: 'success');
            
        } catch (\Exception $e) {
            \Log::error('Failed to swap primary and beauty: ' . $e->getMessage());
            $this->dispatch('toast', message: 'Errore: ' . $e->getMessage(), type: 'error');
        }
    }

    /**
     * Set first image as primary (existing method enhanced)
     */
    public function setPrimaryToFirst()
    {
        $firstImage = $this->product->images()->first();
        
        if ($firstImage) {
            $this->setPrimaryImage($firstImage->id);
        } else {
            $this->dispatch('toast', message: 'Nessuna immagine disponibile!', type: 'error');
        }
    }

    /**
     * Generate alt texts for images (enhanced for beauty system)
     */
    
    // ===========================
    // HELPER METHODS ✨
    // ===========================

    /**
     * Enhanced reload method to include beauty system relations
     */
    private function reloadProduct()
    {
        $this->product = Product::with([
            'primaryImage',
        'images',
        'galleryImages',
        'beautyImages',
        'tags',
        'category'
        ])->find($this->product->id);
    }

    public function reorderImages($imageIds)
    {
        \Log::info('Reorder called with:', $imageIds);
        
        if (!is_array($imageIds) || empty($imageIds)) {
            return;
        }
        
        try {
            \App\Models\Image::updateOrder($imageIds);
            
            $this->reloadProduct();
            
            Activity::performedOn($this->product)
                ->causedBy(auth()->user())
                ->withProperties(['reordered_images' => $imageIds])
                ->log('Reordered product images');
            
            $this->dispatch('images-reordered');
            $this->dispatch('toast', message: 'Ordine immagini aggiornato!', type: 'success');
            
            \Log::info('Images reordered successfully');
            
        } catch (\Exception $e) {
            \Log::error('Reorder failed: ' . $e->getMessage());
            $this->dispatch('toast', message: 'Errore riordinamento immagini', type: 'error');
        }
    }

    public function optimizeAllImages()
    {
        $this->dispatch('toast', message: 'Ottimizzazione in sviluppo...', type: 'info');
    }

    // ===========================
    // TAGS MANAGEMENT
    // ===========================

    public function createTag()
    {
        $this->validateOnly('newTagName');
        
        $tag = Tag::create([
            'name' => $this->newTagName,
            'slug' => Str::slug($this->newTagName),
            'is_active' => true,
            'sort_order' => Tag::max('sort_order') + 1
        ]);
        
        $this->tags = Tag::active()->get();
        $this->selectedTags[] = $tag->id;
        $this->updateTags();
        
        $this->newTagName = '';
        $this->showTagForm = false;
        
        Activity::performedOn($tag)
            ->causedBy(auth()->user())
            ->log('Created new tag');
        
        $this->dispatch('tag-created', name: $tag->name);
        $this->dispatch('toast', message: 'Tag creato e assegnato!', type: 'success');
    }

    // ===========================
    // CATEGORIES MANAGEMENT
    // ===========================

    public function createCategory()
    {
        $this->validateOnly(['newCategoryName', 'newCategoryDescription']);
        
        $category = Category::create([
            'name' => $this->newCategoryName,
            'slug' => Str::slug($this->newCategoryName),
            'description' => $this->newCategoryDescription,
            'is_active' => true,
            'sort_order' => Category::max('sort_order') + 1
        ]);
        
        $this->categories = Category::active()->get();
        $this->category_id = $category->id;
        $this->updateCategory();
        
        $this->newCategoryName = '';
        $this->newCategoryDescription = '';
        $this->showCategoryForm = false;
        
        Activity::performedOn($category)
            ->causedBy(auth()->user())
            ->log('Created new category');
        
        $this->dispatch('category-created', name: $category->name);
        $this->dispatch('toast', message: 'Categoria creata e assegnata!', type: 'success');
    }

    #[Computed]
    public function statusBadge()
    {
        $badges = [
            'active' => '<span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Attivo</span>',
            'inactive' => '<span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Inattivo</span>',
            'draft' => '<span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">Bozza</span>',
        ];
        
        return $badges[$this->status] ?? $this->status;
    }

    // ===========================
    // RENDER
    // ===========================

    public function render()
    {
        return view('livewire.admin.product-editor')->layout('layouts.app');
    }
}