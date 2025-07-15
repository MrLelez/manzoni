<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\Image;
use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Exception;

class ProductCreateService
{
    public function createProduct(array $data): Product
    {
        DB::beginTransaction();
        
        try {
            // 1. Crea prodotto principale con campi del tuo controller
            $product = Product::create([
                'name' => $data['name'],
                'sku' => $data['sku'],
                'slug' => $this->generateSlug($data['name']),
                'description' => $data['description'] ?? null,
                'short_description' => $data['short_description'] ?? null,
                'technical_specs' => $data['technical_specs'] ?? null,
                'base_price' => $data['base_price'],
                'category_id' => $data['category_id'] ?? null,
                'weight' => $data['weight'] ?? null,
                'dimensions' => $data['dimensions'] ?? null,
                'materials' => $data['materials'] ?? null,
                'colors' => $data['colors'] ?? null,
                'installation_type' => $data['installation_type'] ?? null,
                'warranty_years' => $data['warranty_years'] ?? null,
                'is_active' => $data['status'] === 'active',
                'is_featured' => $data['featured'] ?? false,
                'meta_title' => $data['meta_title'] ?? null,
                'meta_description' => $data['meta_description'] ?? null,
                'meta_keywords' => $data['meta_keywords'] ?? null,
                'beauty_category' => $data['beauty_category'] ?? null,
            ]);

            // 2. Gestisci immagini con il tuo sistema morphMany
            if (!empty($data['images'])) {
                $primaryImageId = null;
                foreach ($data['images'] as $index => $imageData) {
                    if (isset($imageData['file'])) {
                        $imageService = new ImageService();
                        $image = $imageService->uploadImage($imageData['file'], 'gallery');
                        
                        // Associa l'immagine al prodotto usando morphMany
                        $image->update([
                            'imageable_type' => Product::class,
                            'imageable_id' => $product->id,
                            'sort_order' => $index,
                            'alt_text' => $imageData['alt'] ?? $product->name,
                            'type' => $imageData['type'] ?? 'gallery'
                        ]);
                        
                        // Prima immagine come primary
                        if ($index === 0) {
                            $primaryImageId = $image->id;
                        }
                    }
                }
                
                // Imposta primary image se presente
                if ($primaryImageId) {
                    $product->update(['primary_image_id' => $primaryImageId]);
                }
            }

            // 3. Gestisci pricing con il tuo sistema
            if (!empty($data['pricing'])) {
                foreach ($data['pricing'] as $level => $price) {
                    if (!empty($price)) {
                        $product->pricing()->create([
                            'level' => $level,
                            'price' => $price,
                            'currency' => 'EUR'
                        ]);
                    }
                }
            }

            // 4. Gestisci relazioni con il tuo sistema
            if (!empty($data['relationships'])) {
                foreach ($data['relationships'] as $relationData) {
                    if (!empty($relationData['related_product_id'])) {
                        $product->relatedProducts()->attach($relationData['related_product_id'], [
                            'relationship_type' => $relationData['type'],
                            'display_order' => $relationData['display_order'] ?? 0
                        ]);
                    }
                }
            }

            // 5. Gestisci accessori con il tuo sistema
            if (!empty($data['accessories'])) {
                foreach ($data['accessories'] as $accessoryData) {
                    if (!empty($accessoryData['product_id'])) {
                        $product->accessories()->attach($accessoryData['product_id'], [
                            'quantity' => $accessoryData['quantity'] ?? 1,
                            'is_required' => $accessoryData['is_required'] ?? false,
                            'display_order' => $accessoryData['display_order'] ?? 0
                        ]);
                    }
                }
            }

            // 6. Gestisci tags con il tuo sistema
            if (!empty($data['tags'])) {
                $tagIds = [];
                foreach ($data['tags'] as $tagName) {
                    $tag = Tag::firstOrCreate([
                        'name' => trim($tagName),
                        'slug' => Str::slug($tagName)
                    ]);
                    $tagIds[] = $tag->id;
                }
                $product->tags()->sync($tagIds);
            }

            // 7. Gestisci varianti con il tuo sistema
            if (!empty($data['variants'])) {
                foreach ($data['variants'] as $variantData) {
                    $product->variants()->create([
                        'name' => $variantData['name'],
                        'sku' => $variantData['sku'],
                        'price_modifier' => $variantData['price_modifier'] ?? 0,
                        'specifications' => $variantData['specifications'] ?? null,
                        'is_active' => $variantData['is_active'] ?? true
                    ]);
                }
            }

            // 8. Gestisci traduzioni con il tuo sistema morphMany
            if (!empty($data['translations'])) {
                foreach ($data['translations'] as $locale => $translation) {
                    if (!empty($translation['name'])) {
                        $product->translations()->create([
                            'locale' => $locale,
                            'field' => 'name',
                            'value' => $translation['name']
                        ]);
                    }
                    if (!empty($translation['description'])) {
                        $product->translations()->create([
                            'locale' => $locale,
                            'field' => 'description',
                            'value' => $translation['description']
                        ]);
                    }
                }
            }

            DB::commit();
            
            // 9. Log attività usando il tuo sistema
            activity()
                ->performedOn($product)
                ->causedBy(auth()->user())
                ->log('created_product');

            return $product;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function generateSlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;
        
        while (Product::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    public function getFormData(): array
    {
        return [
            'categories' => Category::active()->orderBy('name')->get(),
            'tags' => Tag::active()->orderBy('name')->get(),
            'available_products' => Product::where('is_active', true)
                ->with('category')
                ->orderBy('name')
                ->get()
                ->map(fn($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'sku' => $p->sku,
                    'base_price' => $p->base_price,
                    'category_name' => $p->category?->name ?? 'Senza categoria'
                ]),
            'user_levels' => [
                1 => ['name' => 'Nuovo', 'discount' => 5],
                2 => ['name' => 'Consolidato', 'discount' => 10],
                3 => ['name' => 'Fedele', 'discount' => 15],
                4 => ['name' => 'Premium', 'discount' => 20],
                5 => ['name' => 'Top Partner', 'discount' => 25]
            ],
            'materials' => [
                'acciaio_inox' => 'Acciaio Inox',
                'acciaio_corten' => 'Acciaio Corten',
                'alluminio' => 'Alluminio',
                'ghisa' => 'Ghisa',
                'legno' => 'Legno',
                'plastica_riciclata' => 'Plastica Riciclata',
                'cemento' => 'Cemento'
            ],
            'colors' => [
                'bianco' => 'Bianco',
                'nero' => 'Nero',
                'grigio' => 'Grigio',
                'marrone' => 'Marrone',
                'verde' => 'Verde',
                'blu' => 'Blu',
                'rosso' => 'Rosso',
                'corten' => 'Corten'
            ],
            'installation_types' => [
                'removable' => 'Rimovibile',
                'fixed' => 'Fisso',
                'both' => 'Entrambi'
            ],
            'statuses' => [
                'active' => 'Attivo',
                'inactive' => 'Inattivo'
            ],
            'relationship_types' => [
                'sibling' => 'Prodotto Fratello',
                'family' => 'Stessa Famiglia',
                'related' => 'Correlato',
                'compatible' => 'Compatibile',
                'alternative' => 'Alternativo',
                'bundle' => 'Bundle/Kit',
                'upgrade' => 'Upgrade/Potenziamento'
            ],
            'beauty_categories' => [
                'main' => 'Principale',
                'slideshow' => 'Slideshow',
                'header' => 'Header',
                'detail' => 'Dettaglio'
            ]
        ];
    }

    public function validateData(array $data): array
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors['name'] = 'Il nome è obbligatorio';
        }

        if (empty($data['sku'])) {
            $errors['sku'] = 'Il codice SKU è obbligatorio';
        } elseif (Product::where('sku', $data['sku'])->exists()) {
            $errors['sku'] = 'Questo SKU esiste già';
        }

        if (empty($data['base_price']) || $data['base_price'] <= 0) {
            $errors['base_price'] = 'Il prezzo deve essere maggiore di 0';
        }

        // Validazione SKU format
        if (!empty($data['sku']) && !preg_match('/^[A-Z0-9\-]+$/', $data['sku'])) {
            $errors['sku'] = 'Lo SKU deve contenere solo lettere maiuscole, numeri e trattini';
        }

        // Validazione categoria con metodo del tuo controller
        if (!empty($data['category_id']) && !Category::find($data['category_id'])) {
            $errors['category_id'] = 'Categoria non valida';
        }

        return $errors;
    }
}