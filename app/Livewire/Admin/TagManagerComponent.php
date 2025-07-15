<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Tag;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TagManagerComponent extends Component
{
    // Configurazione passata dal blade
    public $category;
    public $product = null;
    public $productId = null;
    public $isMultipleCategory = null;
    
    // Stato interno
    public $selectedTagId = null;
    public $selectedTagIds = []; // ✅ NUOVO: Per selezioni multiple (features)
    public $availableTags = [];
    public $showCreateForm = false;
    
    // Form nuovi tag
    public $newTagName = '';
    public $newTagDescription = '';
    public $newTagColor = '#6B7280';
    public $newTagIcon = '';

    // Listeners
    protected $listeners = [
        'productCreated' => 'handleProductCreated'
    ];

    protected function rules()
    {
        return [
            'selectedTagId' => 'nullable|exists:tags,id',
            'selectedTagIds' => 'array',
            'selectedTagIds.*' => 'exists:tags,id',
            'newTagName' => 'required|string|max:255|unique:tags,name',
            'newTagDescription' => 'nullable|string|max:500',
            'newTagColor' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'newTagIcon' => 'nullable|string|max:10',
        ];
    }

    protected function messages()
    {
        return [
            'newTagName.required' => 'Il nome del tag è obbligatorio.',
            'newTagName.unique' => 'Esiste già un tag con questo nome.',
            'newTagColor.required' => 'Il colore è obbligatorio.',
            'newTagColor.regex' => 'Il colore deve essere in formato esadecimale (#000000).',
        ];
    }

    public function mount($category, $productId = null)
    {
        $this->category = $category;
        $this->productId = $productId;
        
        if ($this->productId) {
            $this->product = Product::find($this->productId);
            $this->loadSelectedTags();
        }
        
        $this->loadAvailableTags();
    }

    public function handleProductCreated($data)
    {
        $this->productId = $data['productId'];
        $this->product = Product::find($this->productId);
        $this->loadSelectedTags();
    }

    public function render()
    {
        return view('livewire.admin.tag-manager-component');
    }

    public function loadAvailableTags()
    {
        try {
            $this->availableTags = Tag::where('category', $this->category)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            $this->availableTags = collect([]);
            \Log::error('Errore caricamento tag', ['error' => $e->getMessage()]);
        }
    }

    // ✅ NUOVO: Caricamento tag selezionati (singoli o multipli)
    public function loadSelectedTags()
    {
        if (!$this->product) return;

        try {
            if ($this->isMultipleCategory()) {
                // ✅ Categories con selezione multipla (features)
                $this->selectedTagIds = $this->product->tags()
                    ->where('category', $this->category)
                    ->pluck('tags.id')
                    ->toArray();
            } else {
                // ✅ Categories con selezione singola (material, color, finish)
                $defaultField = "default_{$this->category}_tag_id";
                
                if (isset($this->product->{$defaultField})) {
                    $this->selectedTagId = $this->product->{$defaultField};
                }
            }
        } catch (\Exception $e) {
            \Log::error('Errore caricamento tag selezionati', ['error' => $e->getMessage()]);
        }
    }

    // ✅ NUOVO: Determina se la categoria supporta selezione multipla
    public function isMultipleCategory()
    {
        return in_array($this->category, ['feature', 'certification', 'style']);
    }

    // ✅ NUOVO: Gestione selezione singola
    public function selectTag($tagId)
    {
        try {
            if ($this->isMultipleCategory()) {
                // Per categorie multiple, usa toggleFeature
                $this->toggleFeature($tagId);
            } else {
                // Per categorie singole, comportamento originale
                $this->selectedTagId = $tagId;
                
                if ($this->product) {
                    $this->saveToProduct();
                }
            }
        } catch (\Exception $e) {
            $this->dispatch('error', 'Errore durante la selezione del tag: ' . $e->getMessage());
        }
    }

    // ✅ NUOVO: Toggle feature per selezione multipla
    public function toggleFeature($tagId)
    {
        try {
            if (in_array($tagId, $this->selectedTagIds)) {
                // Rimuovi se già selezionato
                $this->selectedTagIds = array_diff($this->selectedTagIds, [$tagId]);
            } else {
                // Aggiungi se non selezionato
                $this->selectedTagIds[] = $tagId;
            }

            if ($this->product) {
                $this->saveMultipleToProduct();
            }
        } catch (\Exception $e) {
            $this->dispatch('error', 'Errore durante la selezione della feature: ' . $e->getMessage());
        }
    }

    // ✅ NUOVO: Salvataggio per selezioni multiple
    public function saveMultipleToProduct()
    {
        if (!$this->product || !$this->isMultipleCategory()) return;

        try {
            // Sincronizza i tag della categoria
            $currentTagIds = $this->product->tags()
                ->where('category', '!=', $this->category)
                ->pluck('tags.id')
                ->toArray();

            $newTagIds = array_merge($currentTagIds, $this->selectedTagIds);

            $this->product->tags()->sync($newTagIds);

            // Emetti evento
            $selectedTags = Tag::whereIn('id', $this->selectedTagIds)->get();
            
            $this->dispatch('features-updated', [
                'category' => $this->category,
                'tagIds' => $this->selectedTagIds,
                'tags' => $selectedTags->toArray()
            ]);

            $tagNames = $selectedTags->pluck('name')->join(', ');
            $this->dispatch('success', "Features {$this->category}: {$tagNames}");

        } catch (\Exception $e) {
            $this->dispatch('error', 'Errore durante il salvataggio: ' . $e->getMessage());
            \Log::error('Errore saveMultipleToProduct', ['error' => $e->getMessage()]);
        }
    }

    // ✅ MODIFICATO: Salvataggio per selezioni singole
    public function saveToProduct()
    {
        if (!$this->product || !$this->selectedTagId || $this->isMultipleCategory()) return;

        try {
            $tag = Tag::find($this->selectedTagId);
            if (!$tag) return;

            // Assicurati che il prodotto abbia il tag nella relazione many-to-many
            if (!$this->product->tags()->where('tag_id', $this->selectedTagId)->exists()) {
                $this->product->tags()->attach($this->selectedTagId);
            }

            // Imposta come default per questa categoria
            $defaultField = "default_{$this->category}_tag_id";
            $hasOptionsField = "has_{$this->category}_options";

            $this->product->update([
                $defaultField => $this->selectedTagId,
                $hasOptionsField => true
            ]);

            // Emetti evento
            $this->dispatch('tag-selected', [
                'category' => $this->category,
                'tagId' => $this->selectedTagId,
                'tag' => $tag->toArray()
            ]);

            $this->dispatch('success', "Tag {$tag->name} selezionato per {$this->category}");

        } catch (\Exception $e) {
            $this->dispatch('error', 'Errore durante il salvataggio: ' . $e->getMessage());
            \Log::error('Errore saveToProduct', ['error' => $e->getMessage()]);
        }
    }

    public function createTag()
    {
        $this->resetErrorBag();
        
        try {
            $this->validate([
                'newTagName' => 'required|string|max:255|unique:tags,name',
                'newTagDescription' => 'nullable|string|max:500',
                'newTagColor' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'newTagIcon' => 'nullable|string|max:10',
            ]);

            $tag = Tag::create([
                'name' => $this->newTagName,
                'slug' => Str::slug($this->newTagName),
                'category' => $this->category,
                'description' => $this->newTagDescription,
                'color' => $this->newTagColor,
                'icon' => $this->newTagIcon,
                'is_active' => true,
                'is_featured' => false,
                'usage_count' => 0,
                'sort_order' => Tag::where('category', $this->category)->max('sort_order') + 1,
            ]);

            // Ricarica i tag disponibili
            $this->loadAvailableTags();
            
            // ✅ NUOVO: Gestione dopo creazione
            if ($this->isMultipleCategory()) {
                // Per features, aggiungi automaticamente
                $this->selectedTagIds[] = $tag->id;
                if ($this->product) {
                    $this->saveMultipleToProduct();
                }
            } else {
                // Per singoli, seleziona automaticamente
                $this->selectedTagId = $tag->id;
                if ($this->product) {
                    $this->saveToProduct();
                }
            }
            
            // Chiudi il form
            $this->resetCreateForm();
            
            // Emetti evento
            $this->dispatch('tag-created', [
                'category' => $this->category,
                'tag' => $tag->toArray()
            ]);

            $this->dispatch('success', "Tag '{$tag->name}' creato e selezionato!");

        } catch (ValidationException $e) {
            $this->dispatch('error', 'Errori di validazione. Controlla i campi evidenziati.');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Errore durante la creazione del tag: ' . $e->getMessage());
            \Log::error('Errore createTag', ['error' => $e->getMessage()]);
        }
    }

    // ✅ NUOVO: Rimozione per selezioni multiple
    public function removeSelection()
    {
        try {
            if ($this->isMultipleCategory()) {
                $this->selectedTagIds = [];
                
                if ($this->product) {
                    $this->saveMultipleToProduct();
                }
                
                $this->dispatch('features-cleared', [
                    'category' => $this->category
                ]);
                
                $this->dispatch('success', "Tutte le features {$this->category} rimosse");
            } else {
                $this->selectedTagId = null;
                
                if ($this->product) {
                    $defaultField = "default_{$this->category}_tag_id";
                    $hasOptionsField = "has_{$this->category}_options";
                    
                    $this->product->update([
                        $defaultField => null,
                        $hasOptionsField => false
                    ]);
                }

                $this->dispatch('tag-removed', [
                    'category' => $this->category
                ]);

                $this->dispatch('success', "Tag rimosso da {$this->category}");
            }

        } catch (\Exception $e) {
            $this->dispatch('error', 'Errore durante la rimozione: ' . $e->getMessage());
        }
    }

    public function showCreateModal()
    {
        $this->resetErrorBag();
        $this->showCreateForm = true;
    }

    public function resetCreateForm()
    {
        $this->showCreateForm = false;
        $this->newTagName = '';
        $this->newTagDescription = '';
        $this->newTagColor = '#6B7280';
        $this->newTagIcon = '';
        $this->resetErrorBag();
    }

    // ✅ NUOVO: Computed properties aggiornate
    public function getSelectedTagsProperty()
    {
        if ($this->isMultipleCategory()) {
            return Tag::whereIn('id', $this->selectedTagIds)->get();
        } else {
            return $this->selectedTagId ? Tag::find($this->selectedTagId) : null;
        }
    }

    public function getCategoryLabelProperty()
    {
        return match($this->category) {
            'material' => 'Materiale',
            'color' => 'Colore', 
            'finish' => 'Finitura',
            'certification' => 'Certificazione',
            'style' => 'Stile',
            'feature' => 'Caratteristiche',
            default => ucfirst($this->category)
        };
    }
}