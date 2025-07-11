<?php
// app/Livewire/Admin/ProductEditor.php - FIXED VERSION

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
    public $isUploading = false; // ✨ AGGIUNTO
    public $showTagForm = false;
    public $showCategoryForm = false;
    public $newTagName = '';
    public $newCategoryName = '';
    public $newCategoryDescription = '';
    public $editingTag = null;
    public $editingCategory = null;
    public $editTagName = '';
    public $editCategoryName = '';
    public $showGalleryAdvanced = false; // ✨ AGGIUNTO
    
    // Data collections
    public $categories;
    public $tags;
    public $selectedTags = [];
    
    // Validation rules
    protected $rules = [
        'name' => 'required|string|max:255',
        'sku' => 'required|string|max:100',
        'description' => 'nullable|string',
        'short_description' => 'nullable|string|max:500',
        'base_price' => 'required|numeric|min:0',
        'status' => 'required|in:active,inactive,draft',
        'category_id' => 'required|exists:categories,id',
        'material' => 'nullable|string|max:255',
        'color' => 'nullable|string|max:100',
        'is_featured' => 'boolean',
        'is_customizable' => 'boolean',
        'newTagName' => 'required|string|max:100|unique:tags,name',
        'newCategoryName' => 'required|string|max:255|unique:categories,name',
        'newCategoryDescription' => 'nullable|string|max:500',
        'editTagName' => 'required|string|max:100',
        'editCategoryName' => 'required|string|max:255',
        'uploadedImages.*' => 'image|max:10240', // ✨ AGGIUNTO per validazione file
    ];

   public function mount(Product $product)
{
    // Carica il prodotto con le relazioni
    $this->product = $product->load(['images' => function($query) {
        $query->orderBy('sort_order', 'asc');
    }]);
    
    // Inizializza i campi editabili
    $this->name = $product->name;
    $this->sku = $product->sku;
    $this->description = $product->description;
    $this->base_price = $product->base_price;
    $this->status = $product->status ?? 'draft';
    $this->category_id = $product->category_id;
    $this->is_featured = $product->is_featured ?? false;
    
    // Carica categorie e tags se esistono
    try {
        $this->categories = \App\Models\Category::where('is_active', true)->get();
    } catch (\Exception $e) {
        $this->categories = collect();
    }
    
    try {
        $this->tags = \App\Models\Tag::where('is_active', true)->get();
        $this->selectedTags = $product->tags ? $product->tags->pluck('id')->toArray() : [];
    } catch (\Exception $e) {
        $this->tags = collect();
        $this->selectedTags = [];
    }
}

    // ===========================
    // INLINE EDITING METHODS
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
        
        $oldValue = $this->product->{$field};
        $newValue = $this->{$field};
        
        if ($oldValue !== $newValue) {
            // Auto-genera slug se cambia il nome
            if ($field === 'name') {
                $this->product->slug = Str::slug($newValue);
            }
            
            $this->product->{$field} = $newValue;
            $this->product->save();
            
            // Log dell'attività
            Activity::performedOn($this->product)
                ->causedBy(auth()->user())
                ->withProperties([
                    'field' => $field,
                    'old_value' => $oldValue,
                    'new_value' => $newValue
                ])
                ->log("Updated product {$field}");
            
            $this->dispatch('field-saved', field: $field);
            $this->dispatch('toast', message: 'Campo aggiornato con successo!', type: 'success');
        }
        
        $this->stopEditing();
    }

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
    // IMAGE UPLOAD METHODS - FIXED ✨
    // ===========================

    public function updatedUploadedImages()
    {
        $this->validate([
            'uploadedImages.*' => 'image|max:10240', // 10MB max
        ]);

        $this->isUploading = true;

        foreach ($this->uploadedImages as $image) {
            try {
                $imageService = app(ImageService::class);
                
                // ✨ FIX: Passa l'ID dell'utente invece dell'oggetto User
                $uploadedImage = $imageService->uploadImage(
                    $image,
                    $this->product,
                    'product',
                    auth()->id() // ✨ CAMBIATO: era auth()->user(), ora auth()->id()
                );
                
                // Se è la prima immagine, impostala come principale
                if ($this->product->images()->count() === 1) {
                    $this->setPrimaryImage($uploadedImage->id);
                }
                
                Activity::performedOn($this->product)
                    ->causedBy(auth()->user())
                    ->withProperties(['image_id' => $uploadedImage->id])
                    ->log('Added product image');
                
            } catch (\Exception $e) {
                $this->isUploading = false;
                $this->dispatch('upload-error', message: 'Errore durante upload: ' . $e->getMessage());
                \Log::error('Upload error: ' . $e->getMessage());
                return;
            }
        }
        
        $this->uploadedImages = [];
        $this->isUploading = false;
        $this->product->refresh();
        $this->dispatch('images-uploaded');
        $this->dispatch('toast', message: 'Immagini caricate con successo!', type: 'success');
    }

    public function deleteImage($imageId)
    {
        $image = $this->product->images()->find($imageId);
        if ($image) {
            $image->delete();
            
            Activity::performedOn($this->product)
                ->causedBy(auth()->user())
                ->withProperties(['image_id' => $imageId])
                ->log('Deleted product image');
            
            $this->product->refresh();
            $this->dispatch('image-deleted');
            $this->dispatch('toast', message: 'Immagine eliminata!', type: 'success');
        }
    }

    // ✨ METODO FISSO per il riordinamento
    public function reorderImages($imageIds)
{
    \Log::info('Reorder called with:', $imageIds);
    
    if (!is_array($imageIds) || empty($imageIds)) {
        return;
    }
    
    try {
        foreach ($imageIds as $index => $imageId) {
            if (is_numeric($imageId)) {
                \DB::table('images')
                    ->where('id', $imageId)
                    ->where('imageable_type', 'App\Models\Product')
                    ->where('imageable_id', $this->product->id)
                    ->update(['sort_order' => $index + 1]);
            }
        }
        
        $this->product->refresh();
        
        \Log::info('Images reordered successfully');
        
    } catch (\Exception $e) {
        \Log::error('Reorder failed: ' . $e->getMessage());
    }
}

    public function setPrimaryImage($imageId)
    {
        try {
            // Rimuovi primary da tutte le immagini
            $this->product->images()->update(['is_primary' => false]);
            
            // Imposta la nuova immagine primary
            $image = $this->product->images()->find($imageId);
            if ($image) {
                $image->update(['is_primary' => true]);
                
                Activity::performedOn($this->product)
                    ->causedBy(auth()->user())
                    ->withProperties(['image_id' => $imageId])
                    ->log('Set primary product image');
                
                $this->product->refresh();
                $this->dispatch('primary-image-set', imageId: $imageId);
                $this->dispatch('toast', message: 'Immagine principale impostata!', type: 'success');
            }
        } catch (\Exception $e) {
            \Log::error('Error setting primary image: ' . $e->getMessage());
            $this->dispatch('toast', message: 'Errore impostazione immagine principale', type: 'error');
        }
    }

    public function updateImageAltText($imageId, $altText)
    {
        $image = $this->product->images()->find($imageId);
        if ($image) {
            $image->update(['alt_text' => $altText]);
            
            Activity::performedOn($this->product)
                ->causedBy(auth()->user())
                ->withProperties(['image_id' => $imageId, 'alt_text' => $altText])
                ->log('Updated image alt text');
            
            $this->dispatch('alt-text-updated', imageId: $imageId);
        }
    }

    // ===========================
    // HELPER METHODS ✨
    // ===========================

    public function optimizeAllImages()
    {
        // TODO: Implementare ottimizzazione immagini
        $this->dispatch('toast', message: 'Ottimizzazione in sviluppo...', type: 'info');
    }

    public function downloadAllImages()
    {
        // TODO: Implementare download ZIP
        $this->dispatch('toast', message: 'Download in sviluppo...', type: 'info');
    }

    public function generateAltTexts()
    {
        $images = $this->product->images()->whereNull('alt_text')->orWhere('alt_text', '')->get();
        foreach ($images as $image) {
            $altText = $this->product->name . ' - Immagine ' . ($image->sort_order ?? '');
            $image->update(['alt_text' => $altText]);
        }
        $this->product->refresh();
        $this->dispatch('toast', message: 'Alt text generati automaticamente!', type: 'success');
    }

    public function setPrimaryToFirst()
    {
        $firstImage = $this->product->images()->orderBy('sort_order')->first();
        if ($firstImage) {
            $this->setPrimaryImage($firstImage->id);
        }
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
        
        // Ricarica i tags
        $this->tags = Tag::active()->get();
        
        // Aggiungi automaticamente il nuovo tag al prodotto
        $this->selectedTags[] = $tag->id;
        $this->updateTags();
        
        // Reset form
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
        
        // Ricarica le categorie
        $this->categories = Category::active()->get();
        
        // Assegna automaticamente la nuova categoria al prodotto
        $this->category_id = $category->id;
        $this->updateCategory();
        
        // Reset form
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