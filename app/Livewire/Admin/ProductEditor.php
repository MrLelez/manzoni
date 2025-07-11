<?php
// app/Livewire/Admin/ProductEditor.php

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
    public $showTagForm = false;
    public $showCategoryForm = false;
    public $newTagName = '';
    public $newCategoryName = '';
    public $newCategoryDescription = '';
    public $editingTag = null;
    public $editingCategory = null;
    public $editTagName = '';
    public $editCategoryName = '';
    
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
    ];

    public function mount(Product $product)
    {
        // Carica il prodotto con le relazioni (gestendo errori)
        try {
            $this->product = $product->load(['category', 'images']);
            
            // Prova a caricare i tags se la tabella esiste
            try {
                $this->product->load('tags');
            } catch (\Exception $e) {
                // Se i tags non funzionano, continua senza
            }
        } catch (\Exception $e) {
            $this->product = $product;
        }
        
        // Inizializza i campi editabili
        $this->name = $product->name;
        $this->sku = $product->sku;
        $this->description = $product->description;
        $this->short_description = $product->short_description;
        $this->base_price = $product->base_price;
        $this->status = $product->status;
        $this->category_id = $product->category_id;
        $this->material = $product->material;
        $this->color = $product->color;
        $this->is_featured = $product->is_featured ?? false;
        $this->is_customizable = $product->is_customizable ?? false;
        
        // Carica i dati per i dropdown (con fallback)
        try {
            $this->categories = Category::active()->get();
        } catch (\Exception $e) {
            $this->categories = collect();
        }
        
        try {
            $this->tags = Tag::active()->get();
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
    }

    public function updateTags()
    {
        $this->product->tags()->sync($this->selectedTags);
        
        Activity::performedOn($this->product)
            ->causedBy(auth()->user())
            ->withProperties(['tags' => $this->selectedTags])
            ->log('Updated product tags');
        
        $this->dispatch('tags-updated');
    }

    // ===========================
    // IMAGE UPLOAD METHODS
    // ===========================

    public function updatedUploadedImages()
    {
        $this->validate([
            'uploadedImages.*' => 'image|max:10240', // 10MB max
        ]);

        foreach ($this->uploadedImages as $image) {
            try {
                $imageService = app(ImageService::class);
                $uploadedImage = $imageService->uploadImage(
                    $image,
                    $this->product,
                    'product',
                    auth()->user()
                );
                
                Activity::performedOn($this->product)
                    ->causedBy(auth()->user())
                    ->withProperties(['image_id' => $uploadedImage->id])
                    ->log('Added product image');
                
            } catch (\Exception $e) {
                $this->dispatch('upload-error', message: 'Errore durante upload: ' . $e->getMessage());
            }
        }
        
        $this->uploadedImages = [];
        $this->product->refresh();
        $this->dispatch('images-uploaded');
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
        }
    }

    public function reorderImages($orderedIds)
    {
        foreach ($orderedIds as $index => $imageId) {
            $this->product->images()->where('id', $imageId)->update([
                'sort_order' => $index + 1
            ]);
        }
        
        Activity::performedOn($this->product)
            ->causedBy(auth()->user())
            ->withProperties(['new_order' => $orderedIds])
            ->log('Reordered product images');
        
        $this->product->refresh();
        $this->dispatch('images-reordered');
    }

    public function setPrimaryImage($imageId)
    {
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
    }

    public function startEditingTag($tagId)
    {
        $tag = Tag::find($tagId);
        if ($tag) {
            $this->editingTag = $tagId;
            $this->editTagName = $tag->name;
        }
    }

    public function saveTag()
    {
        $this->validateOnly('editTagName');
        
        $tag = Tag::find($this->editingTag);
        if ($tag) {
            $oldName = $tag->name;
            $tag->update([
                'name' => $this->editTagName,
                'slug' => Str::slug($this->editTagName)
            ]);
            
            // Ricarica i tags
            $this->tags = Tag::active()->get();
            
            Activity::performedOn($tag)
                ->causedBy(auth()->user())
                ->withProperties(['old_name' => $oldName, 'new_name' => $this->editTagName])
                ->log('Updated tag name');
            
            $this->editingTag = null;
            $this->editTagName = '';
            
            $this->dispatch('tag-updated', name: $tag->name);
        }
    }

    public function cancelTagEdit()
    {
        $this->editingTag = null;
        $this->editTagName = '';
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