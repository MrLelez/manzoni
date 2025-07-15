<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Services\ProductCreateService;
use Exception;

class ProductCreate extends Component
{
    use WithFileUploads;

    // Basic product data - aggiornato per il tuo model
    public $name = '';
    public $sku = '';
    public $description = '';
    public $short_description = '';
    public $technical_specs = '';
    public $base_price = '';
    public $category_id = '';
    public $weight = '';
    public $dimensions = '';
    public $materials = [];
    public $colors = [];
    public $installation_type = 'removable';
    public $warranty_years = 2;
    public $status = 'active';
    public $featured = false;
    public $meta_title = '';
    public $meta_description = '';
    public $meta_keywords = '';
    public $beauty_category = '';

    // Images
    public $images = [];

    // Pricing
    public $pricing = [];

    // Relationships
    public $relationships = [];

    // Accessories
    public $accessories = [];

    // Variants
    public $variants = [];

    // Tags
    public $tags = [];
    public $tagInput = '';

    // Translations
    public $translations = [];
    public $activeLocale = 'it';

    // Form data
    public $formData = [];

    // UI state
    public $isLoading = false;
    public $validationErrors = [];
    public $successMessage = '';

    protected $productService;

    public function mount()
    {
        $this->productService = new ProductCreateService();
        $this->formData = $this->productService->getFormData();

        // Initialize pricing
        foreach ($this->formData['user_levels'] as $level => $data) {
            $this->pricing[$level] = '';
        }

        // Initialize translations
        $locales = ['it', 'en', 'fr', 'de', 'es'];
        foreach ($locales as $locale) {
            $this->translations[$locale] = ['name' => '', 'description' => ''];
        }
    }

    public function updatedName($value)
    {
        if (empty($this->meta_title)) {
            $this->meta_title = $value;
        }
    }

    public function updatedBasePrice($value)
    {
        if (!empty($value) && is_numeric($value)) {
            foreach ($this->formData['user_levels'] as $level => $data) {
                $discount = $data['discount'] / 100;
                $this->pricing[$level] = round($value * (1 - $discount), 2);
            }
        }
    }

    public function generateSku()
    {
        if (!empty($this->name) && !empty($this->category_id)) {
            $category = $this->formData['categories']->firstWhere('id', $this->category_id);
            $categoryPrefix = $category ? strtoupper(substr($category->name, 0, 3)) : 'PRD';
            $namePrefix = strtoupper(substr(str_replace(' ', '', $this->name), 0, 3));
            $this->sku = $categoryPrefix . '-' . $namePrefix . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        }
    }

    public function addImage()
    {
        $this->images[] = [
            'file' => null, 
            'alt' => '',
            'type' => 'gallery'
        ];
    }

    public function removeImage($index)
    {
        unset($this->images[$index]);
        $this->images = array_values($this->images);
    }

    public function addRelationship()
    {
        $this->relationships[] = [
            'related_product_id' => '',
            'type' => 'related',
            'is_bidirectional' => true
        ];
    }

    public function removeRelationship($index)
    {
        unset($this->relationships[$index]);
        $this->relationships = array_values($this->relationships);
    }

    public function addAccessory()
    {
        $this->accessories[] = [
            'product_id' => '',
            'is_required' => false,
            'quantity' => 1,
            'price_modifier' => 0,
            'description' => ''
        ];
    }

    public function removeAccessory($index)
    {
        unset($this->accessories[$index]);
        $this->accessories = array_values($this->accessories);
    }

    public function addVariant()
    {
        $this->variants[] = [
            'name' => '',
            'sku' => '',
            'price_modifier' => 0,
            'specifications' => '',
            'is_active' => true
        ];
    }

    public function removeVariant($index)
    {
        unset($this->variants[$index]);
        $this->variants = array_values($this->variants);
    }

    public function addTag()
    {
        $tagName = trim($this->tagInput);
        if (!empty($tagName) && !in_array($tagName, $this->tags)) {
            $this->tags[] = $tagName;
            $this->tagInput = '';
        }
    }

    public function removeTag($index)
    {
        unset($this->tags[$index]);
        $this->tags = array_values($this->tags);
    }

    public function switchLocale($locale)
    {
        $this->activeLocale = $locale;
    }

    public function saveDraft()
    {
        $this->status = 'draft';
        $this->save();
    }

    public function save()
    {
        $this->isLoading = true;
        $this->validationErrors = [];

        try {
            $productData = [
                'name' => $this->name,
                'sku' => $this->sku,
                'description' => $this->description,
                'short_description' => $this->short_description,
                'technical_specs' => $this->technical_specs,
                'base_price' => $this->base_price,
                'category_id' => $this->category_id ?: null,
                'weight' => $this->weight,
                'dimensions' => $this->dimensions,
                'materials' => $this->materials,
                'colors' => $this->colors,
                'installation_type' => $this->installation_type,
                'warranty_years' => $this->warranty_years,
                'status' => $this->status,
                'featured' => $this->featured,
                'meta_title' => $this->meta_title,
                'meta_description' => $this->meta_description,
                'meta_keywords' => $this->meta_keywords,
                'beauty_category' => $this->beauty_category,
                'images' => $this->images,
                'pricing' => $this->pricing,
                'relationships' => $this->relationships,
                'accessories' => $this->accessories,
                'variants' => $this->variants,
                'tags' => $this->tags,
                'translations' => $this->translations,
            ];

            $validationErrors = $this->productService->validateData($productData);
            if (!empty($validationErrors)) {
                $this->validationErrors = $validationErrors;
                $this->isLoading = false;
                return;
            }

            $product = $this->productService->createProduct($productData);
            $this->successMessage = 'Prodotto creato con successo!';
            
            // Redirect compatibile con il tuo controller
            return redirect()->route('admin.products.index')
                            ->with('success', 'Prodotto creato con successo!');

        } catch (Exception $e) {
            $this->validationErrors = ['general' => 'Errore: ' . $e->getMessage()];
        }

        $this->isLoading = false;
    }

    public function render()
    {
        return view('livewire.admin.product-create');
    }
}