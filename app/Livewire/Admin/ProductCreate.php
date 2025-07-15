<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class ProductCreate extends Component
{
    // Proprietà base del prodotto
    public $name = '';
    public $sku = '';
    public $description = '';
    public $short_description = '';
    public $base_price = '';
    public $category_id = '';
    public $status = 'active';
    public $featured = false;
    public $weight = '';
    public $dimensions = '';
    public $installation_type = '';
    public $warranty_years = '';
    public $technical_specs = '';
    
    // Altre proprietà
    public $images = [];
    public $tags = [];
    public $tagInput = '';
    public $relationships = [];
    public $accessories = [];
    public $pricing = [];
    
    // Messaggi e stato
    public $successMessage = '';
    public $validationErrors = []; // ✅ FIX: Rinominato per evitare conflitto
    
    // Form data
    public $formData = [];
    
    // ID prodotto per editing
    public $productId = null;
    public $product = null;
    public $isEditing = false;

    // Listeners per eventi TagManager
    protected $listeners = [
        'tag-selected' => 'handleTagSelected',
        'tag-created' => 'handleTagCreated',
        'tag-removed' => 'handleTagRemoved',
    ];

    // ✅ FIX: Regole di validazione complete e corrette
    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z0-9\-]+$/',
                'unique:products,sku' . ($this->productId ? ',' . $this->productId : '')
            ],
            'base_price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:active,inactive,draft',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:5000',
            'weight' => 'nullable|numeric|min:0',
            'warranty_years' => 'nullable|integer|min:1|max:50',
            'technical_specs' => 'nullable|string|max:10000',
            'installation_type' => 'nullable|string|in:fixed,removable,portable',
            'dimensions' => 'nullable|string|max:255',
        ];
    }

    // ✅ FIX: Messaggi di errore in italiano
    protected function messages()
    {
        return [
            'name.required' => 'Il nome del prodotto è obbligatorio.',
            'name.max' => 'Il nome non può superare i 255 caratteri.',
            'sku.required' => 'Il codice SKU è obbligatorio.',
            'sku.unique' => 'Questo SKU esiste già. Scegli un codice diverso.',
            'sku.regex' => 'Lo SKU deve contenere solo lettere maiuscole, numeri e trattini.',
            'base_price.required' => 'Il prezzo base è obbligatorio.',
            'base_price.numeric' => 'Il prezzo deve essere un numero valido.',
            'base_price.min' => 'Il prezzo deve essere maggiore di 0.',
            'category_id.required' => 'La categoria è obbligatoria.',
            'category_id.exists' => 'La categoria selezionata non è valida.',
            'status.required' => 'Lo stato è obbligatorio.',
            'status.in' => 'Lo stato deve essere: attivo, inattivo o bozza.',
        ];
    }

    public function mount($productId = null)
    {
        $this->productId = $productId;
        $this->isEditing = !is_null($productId);
        
        if ($this->productId) {
            $this->product = Product::findOrFail($this->productId);
            $this->loadProductData();
        }
        
        $this->loadFormData();
    }

    public function loadProductData()
    {
        if (!$this->product) return;

        $this->name = $this->product->name;
        $this->sku = $this->product->sku;
        $this->description = $this->product->description;
        $this->short_description = $this->product->short_description;
        $this->base_price = $this->product->base_price;
        $this->category_id = $this->product->category_id;
        $this->status = $this->product->status;
        $this->featured = $this->product->is_featured;
        $this->weight = $this->product->weight;
        $this->dimensions = $this->product->dimensions;
        $this->installation_type = $this->product->installation_type;
        $this->warranty_years = $this->product->warranty_years;
        $this->technical_specs = $this->product->technical_specs;
    }

    public function loadFormData()
    {
        try {
            $this->formData = [
                'categories' => Category::where('is_active', true)->orderBy('name')->get(),
                'statuses' => [
                    'active' => 'Attivo',
                    'inactive' => 'Inattivo',
                    'draft' => 'Bozza',
                ],
                'installation_types' => [
                    'fixed' => 'Fisso',
                    'removable' => 'Removibile',
                    'portable' => 'Portatile',
                ],
                'user_levels' => [
                    1 => ['name' => 'Base', 'discount' => '5'],
                    2 => ['name' => 'Silver', 'discount' => '10'],
                    3 => ['name' => 'Gold', 'discount' => '15'],
                    4 => ['name' => 'Platinum', 'discount' => '20'],
                    5 => ['name' => 'Diamond', 'discount' => '25'],
                ],
                'relationship_types' => [
                    'related' => 'Prodotto correlato',
                    'similar' => 'Prodotto simile',
                    'accessory' => 'Accessorio',
                    'variant' => 'Variante',
                ],
                'available_products' => Product::where('is_active', true)
                                              ->when($this->productId, fn($q) => $q->where('id', '!=', $this->productId))
                                              ->select('id', 'name', 'sku')
                                              ->orderBy('name')
                                              ->get()
                                              ->toArray(),
            ];
        } catch (\Exception $e) {
            \Log::error('Errore caricamento form data', ['error' => $e->getMessage()]);
            $this->formData = [
                'categories' => collect([]),
                'statuses' => [],
                'installation_types' => [],
                'user_levels' => [],
                'relationship_types' => [],
                'available_products' => [],
            ];
        }
    }

    // ========================================
    // GESTIONE EVENTI TAG MANAGER
    // ========================================

    public function handleTagSelected($data)
    {
        $category = $data['category'];
        $tag = $data['tag'];
        
        $this->successMessage = "Tag {$tag['name']} selezionato per {$category}";
    }

    public function handleTagCreated($data)
    {
        $category = $data['category'];
        $tag = $data['tag'];
        
        $this->successMessage = "Nuovo tag '{$tag['name']}' creato per {$category}!";
    }

    public function handleTagRemoved($data)
    {
        $category = $data['category'];
        $this->successMessage = "Tag rimosso da {$category}";
    }

    // ========================================
    // METODI PRINCIPALI - COMPLETAMENTE RIVISTI
    // ========================================

    public function generateSku()
    {
        if (empty($this->name)) {
            $this->addError('sku', 'Inserisci prima il nome del prodotto');
            return;
        }

        $baseSku = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $this->name), 0, 8));
        $counter = 1;
        
        do {
            $this->sku = $baseSku . '-' . str_pad($counter, 3, '0', STR_PAD_LEFT);
            $counter++;
        } while (Product::where('sku', $this->sku)->exists());
    }

    // ✅ FIX: Metodo save completamente riscritto e testato
    public function save()
    {
        // Reset errori precedenti
        $this->resetErrorBag();
        $this->validationErrors = []; // ✅ FIX: Usa la proprietà rinominata

        try {
            // ✅ FIX: Debug dei dati prima della validazione
            \Log::info('ProductCreate: Tentativo salvataggio', [
                'name' => $this->name,
                'sku' => $this->sku,
                'base_price' => $this->base_price,
                'category_id' => $this->category_id,
                'status' => $this->status
            ]);

            // Validazione
            $validated = $this->validate();
            
            \Log::info('ProductCreate: Validazione passata', $validated);

            // Inizia transazione database
            \DB::beginTransaction();

            // ✅ FIX: Dati corretti per il salvataggio
            $productData = [
                'name' => $this->name,
                'slug' => Str::slug($this->name), // ✅ AGGIUNTO: genera slug automaticamente
                'sku' => $this->sku,
                'description' => $this->description,
                'short_description' => $this->short_description,
                'base_price' => (float) $this->base_price, // ✅ FIX: cast a float
                'category_id' => (int) $this->category_id, // ✅ FIX: cast a int
                'status' => $this->status,
                'is_featured' => (bool) $this->featured, // ✅ FIX: cast a boolean
                'weight' => $this->weight ? (float) $this->weight : null,
                'dimensions' => $this->dimensions,
                'installation_type' => $this->installation_type,
                'warranty_years' => $this->warranty_years ? (int) $this->warranty_years : null,
                'technical_specs' => $this->technical_specs,
                'is_active' => true, // ✅ AGGIUNTO: campo obbligatorio
            ];

            \Log::info('ProductCreate: Dati preparati per salvataggio', $productData);

            if ($this->productId) {
                // Update prodotto esistente
                $this->product->update($productData);
                $this->successMessage = 'Prodotto aggiornato con successo!';
                
                \Log::info('ProductCreate: Prodotto aggiornato', ['id' => $this->productId]);
            } else {
                // ✅ FIX: Crea nuovo prodotto con dati corretti
                $product = Product::create($productData);

                // Aggiorna le proprietà per permettere ai TagManager di funzionare
                $this->productId = $product->id;
                $this->product = $product;
                $this->isEditing = true;

                $this->successMessage = 'Prodotto creato con successo! ID: ' . $product->id;
                
                \Log::info('ProductCreate: Nuovo prodotto creato', [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku
                ]);

                // Notifica i TagManager che il prodotto è stato creato
                $this->dispatch('productCreated', [
                    'productId' => $this->productId
                ]);
            }

            // Commit transazione
            \DB::commit();

            // ✅ SUCCESS: Notifica successo
            $this->dispatch('success', $this->successMessage);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \DB::rollBack();
            
            // ✅ FIX: Gestione errori di validazione migliorata
            $this->validationErrors = $e->validator->errors()->all();
            
            \Log::error('ProductCreate: Errori di validazione', [
                'errors' => $this->validationErrors,
                'data' => $this->only(['name', 'sku', 'base_price', 'category_id'])
            ]);
            
            $this->dispatch('error', 'Controlla i campi evidenziati e riprova.');
            
        } catch (\Exception $e) {
            \DB::rollBack();
            
            // ✅ FIX: Logging dettagliato degli errori
            $errorMessage = 'Errore durante il salvataggio: ' . $e->getMessage();
            $this->validationErrors = [$errorMessage];
            
            \Log::error('ProductCreate: Errore generico', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'product_data' => $this->only(['name', 'sku', 'base_price', 'category_id'])
            ]);
            
            $this->dispatch('error', $errorMessage);
        }
    }

    // ✅ FIX: Metodo saveDraft migliorato
    public function saveDraft()
    {
        $originalStatus = $this->status;
        $this->status = 'draft';
        
        $this->save();
        
        if (!$this->validationErrors) {
            $this->successMessage = 'Bozza salvata con successo!';
        }
        
        $this->status = $originalStatus;
    }

    // ========================================
    // METODI TAG E RELAZIONI (invariati)
    // ========================================

    public function addTag()
    {
        if (empty($this->tagInput)) return;
        
        if (!in_array($this->tagInput, $this->tags)) {
            $this->tags[] = $this->tagInput;
        }
        
        $this->tagInput = '';
    }

    public function removeTag($index)
    {
        unset($this->tags[$index]);
        $this->tags = array_values($this->tags);
    }

    public function addImage()
    {
        $this->images[] = ['file' => null];
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
            'type' => 'related'
        ];
    }

    public function removeRelationship($index)
    {
        unset($this->relationships[$index]);
        $this->relationships = array_values($this->relationships);
    }

    public function render()
    {
        return view('livewire.admin.product-create');
    }
}