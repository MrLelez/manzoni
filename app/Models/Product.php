<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Product extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'sku',
        'name',
        'slug',
        'description',
        'short_description',
        'technical_specs',
        'category_id',
        'base_price',
        'weight',
        'dimensions',
        'materials',
        'colors',
        'installation_type',
        'warranty_years',
        'images',
        'attachments',
        'is_active',
        'is_featured',
        'sort_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'highlights' => 'json',
        'specifications' => 'json', 
        'certifications' => 'json',
        'features' => 'json',
        'downloads' => 'json',
        'structured_data' => 'json',
        'base_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'dimensions' => 'json',
        'materials' => 'json',
        'colors' => 'json',
        'images' => 'json',
        'attachments' => 'json',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'warranty_years' => 'integer',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ===================================================================
    // RELAZIONI PRINCIPALI
    // ===================================================================

    /**
     * Categoria di appartenenza
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Varianti del prodotto (fisso/removibile)
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Pricing per livelli rivenditori
     */
    public function pricing(): HasMany
    {
        return $this->hasMany(ProductPricing::class);
    }

    /**
     * Tag per ricerca e categorizzazione
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tags')
                    ->withTimestamps();
    }

    /**
     * Accessori condivisi
     */
    public function accessories(): BelongsToMany
    {
        return $this->belongsToMany(ProductAccessory::class, 'product_accessories')
                    ->withPivot(['quantity', 'is_required', 'display_order'])
                    ->withTimestamps();
    }

    /**
     * Immagini collegate al prodotto (polymorphic)
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable')
                    ->where('type', 'product')
                    ->where('status', 'active')
                    ->orderBy('created_at');
    }

    /**
     * Immagine principale del prodotto
     */
    public function mainImage(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')
                    ->where('type', 'product')
                    ->where('status', 'active')
                    ->oldest();
    }

    /**
     * Traduzioni polymorphic
     */
    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    // ===================================================================
    // RELAZIONI PRODOTTI (FRATELLI/FAMIGLIA)
    // ===================================================================

    /**
     * Prodotti correlati (fratelli) - relazione bidirezionale
     */
    public function relatedProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_relationships', 'product_id', 'related_product_id')
                    ->withPivot(['relationship_type', 'display_order'])
                    ->withTimestamps();
    }

    /**
     * Prodotti che hanno questo come correlato
     */
    public function relatedByProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_relationships', 'related_product_id', 'product_id')
                    ->withPivot(['relationship_type', 'display_order'])
                    ->withTimestamps();
    }

    /**
     * Prodotti della stessa famiglia
     */
    public function familyProducts(): BelongsToMany
    {
        return $this->relatedProducts()->wherePivot('relationship_type', 'family');
    }

    /**
     * Prodotti fratelli (stesso tipo)
     */
    public function siblingProducts(): BelongsToMany
    {
        return $this->relatedProducts()->wherePivot('relationship_type', 'sibling');
    }

    /**
     * Prodotti compatibili
     */
    public function compatibleProducts(): BelongsToMany
    {
        return $this->relatedProducts()->wherePivot('relationship_type', 'compatible');
    }

    // ===================================================================
    // SCOPE QUERIES
    // ===================================================================

    /**
     * Scope per prodotti attivi
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope per prodotti in evidenza
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope per ricerca full-text
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('short_description', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%")
              ->orWhere('technical_specs', 'like', "%{$search}%");
        });
    }

    /**
     * Scope per categoria
     */
    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope per range di prezzo
     */
    public function scopePriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('base_price', [$minPrice, $maxPrice]);
    }

    /**
     * Scope per colore
     */
    public function scopeByColor($query, $color)
    {
        return $query->whereJsonContains('colors', $color);
    }

    /**
     * Scope per materiale
     */
    public function scopeByMaterial($query, $material)
    {
        return $query->whereJsonContains('materials', $material);
    }

    // ===================================================================
    // ACCESSORS & MUTATORS
    // ===================================================================

    /**
     * Ottieni il prezzo formattato
     */
    public function getFormattedPriceAttribute(): string
    {
        return '€ ' . number_format($this->base_price, 2, ',', '.');
    }

    /**
     * Ottieni il prezzo per livello rivenditore
     */
    public function getPriceForLevel(int $level): float
    {
        $pricing = $this->pricing()->where('level', $level)->first();
        
        if ($pricing) {
            return $pricing->price;
        }

        // Fallback con sconto standard per livello
        $discountPercentage = match($level) {
            1 => 5,
            2 => 10,
            3 => 15,
            4 => 20,
            5 => 25,
            default => 0
        };

        return $this->base_price * (1 - $discountPercentage / 100);
    }

    /**
     * Ottieni il prezzo formattato per livello
     */
    public function getFormattedPriceForLevel(int $level): string
    {
        return '€ ' . number_format($this->getPriceForLevel($level), 2, ',', '.');
    }

    /**
     * Ottieni l'immagine principale
     */
    public function getMainImageAttribute(): ?string
    {
        if (!$this->images || !is_array($this->images)) {
            return null;
        }

        return $this->images[0] ?? null;
    }

    /**
     * Ottieni tutte le immagini tranne la principale
     */
    public function getGalleryImagesAttribute(): array
    {
        if (!$this->images || !is_array($this->images)) {
            return [];
        }

        return array_slice($this->images, 1);
    }

    /**
     * Ottieni la URL dell'immagine principale
     */
    public function getMainImageUrlAttribute(): ?string
    {
        if (!$this->main_image) {
            return null;
        }

        // Se è già una URL completa, restituiscila
        if (str_starts_with($this->main_image, 'http')) {
            return $this->main_image;
        }

        // Usa il sistema di clean URLs per le immagini
        return url('/img/' . $this->main_image);
    }

    /**
     * Ottieni il peso formattato
     */
    public function getFormattedWeightAttribute(): string
    {
        return $this->weight . ' kg';
    }

    /**
     * Ottieni le dimensioni formattate
     */
    public function getFormattedDimensionsAttribute(): string
    {
        if (!$this->dimensions) {
            return 'N/A';
        }

        $dims = $this->dimensions;
        return ($dims['length'] ?? 0) . ' x ' . ($dims['width'] ?? 0) . ' x ' . ($dims['height'] ?? 0) . ' cm';
    }

    /**
     * Ottieni una traduzione per campo specifico
     */
    public function getTranslation(string $field, string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        
        $translation = $this->translations()
            ->where('field', $field)
            ->where('locale', $locale)
            ->first();

        return $translation?->value ?? $this->getAttribute($field);
    }

    /**
     * Ottieni il nome tradotto
     */
    public function getTranslatedNameAttribute(): string
    {
        return $this->getTranslation('name') ?? $this->name;
    }

    /**
     * Ottieni la descrizione tradotta
     */
    public function getTranslatedDescriptionAttribute(): string
    {
        return $this->getTranslation('description') ?? $this->description;
    }

    // ===================================================================
    // MUTATORS
    // ===================================================================

    /**
     * Imposta lo slug automaticamente dal nome
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        
        if (!$this->slug) {
            $this->attributes['slug'] = \Str::slug($value);
        }
    }

    /**
     * Normalizza i colori come array
     */
    public function setColorsAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['colors'] = json_encode(explode(',', $value));
        } elseif (is_array($value)) {
            $this->attributes['colors'] = json_encode($value);
        }
    }

    /**
     * Normalizza i materiali come array
     */
    public function setMaterialsAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['materials'] = json_encode(explode(',', $value));
        } elseif (is_array($value)) {
            $this->attributes['materials'] = json_encode($value);
        }
    }

    // ===================================================================
    // HELPER METHODS
    // ===================================================================

    /**
     * Verifica se il prodotto è disponibile
     */
    public function isAvailable(): bool
    {
        return $this->is_active && !$this->trashed();
    }

    /**
     * Verifica se ha varianti
     */
    public function hasVariants(): bool
    {
        return $this->variants()->exists();
    }

    /**
     * Verifica se ha immagini
     */
    public function hasImages(): bool
    {
        return $this->images()->exists();
    }

    /**
     * Ottieni il numero di immagini
     */
    public function getImageCountAttribute(): int
    {
        return $this->images()->count();
    }

    /**
     * Aggiungi un'immagine al prodotto
     */
    public function addImage(Image $image): void
    {
        $this->images()->save($image);
    }

    /**
     * Rimuovi un'immagine dal prodotto
     */
    public function removeImage(Image $image): void
    {
        $image->delete();
    }

    /**
     * Verifica se ha accessori
     */
    public function hasAccessories(): bool
    {
        return $this->accessories()->exists();
    }

    /**
     * Verifica se ha prodotti correlati
     */
    public function hasRelatedProducts(): bool
    {
        return $this->relatedProducts()->exists();
    }

    /**
     * Ottieni tutti i colori disponibili
     */
    public function getAvailableColors(): array
    {
        return $this->colors ?? [];
    }

    /**
     * Ottieni tutti i materiali disponibili
     */
    public function getAvailableMaterials(): array
    {
        return $this->materials ?? [];
    }

    /**
     * Verifica se il prodotto è in una categoria specifica
     */
    public function isInCategory(int $categoryId): bool
    {
        return $this->category_id === $categoryId;
    }

    /**
     * Ottieni il percorso della categoria (breadcrumb)
     */
    public function getCategoryPath(): array
    {
        $path = [];
        $category = $this->category;
        
        while ($category) {
            array_unshift($path, $category);
            $category = $category->parent;
        }
        
        return $path;
    }

    /**
     * Ottieni il percorso della categoria come stringa
     */
    public function getCategoryPathString(): string
    {
        return implode(' > ', array_map(fn($cat) => $cat->name, $this->getCategoryPath()));
    }

    /**
     * Verifica se il prodotto è in offerta
     */
    public function isOnSale(): bool
    {
        // Implementare logica per verificare se è in offerta
        // Esempio: confrontare con prezzo originale o date di promozione
        return false; // TODO: Implementare logica offerte
    }

    /**
     * Ottieni il prezzo scontato per livello (se in offerta)
     */
    public function getSalePriceForLevel(int $level): ?float
    {
        if (!$this->isOnSale()) {
            return null;
        }
        
        // TODO: Implementare logica sconti/offerte
        return null;
    }

    /**
     * Ottieni lo sconto percentuale per livello
     */
    public function getDiscountPercentageForLevel(int $level): float
    {
        $originalPrice = $this->base_price;
        $discountedPrice = $this->getPriceForLevel($level);
        
        if ($originalPrice <= 0) {
            return 0;
        }
        
        return (($originalPrice - $discountedPrice) / $originalPrice) * 100;
    }

    /**
     * Verifica se il prodotto è nuovo (creato di recente)
     */
    public function isNew(int $days = 30): bool
    {
        return $this->created_at->diffInDays(now()) <= $days;
    }

    /**
     * Verifica se il prodotto è popolare (basato su ordini/visualizzazioni)
     */
    public function isPopular(): bool
    {
        // TODO: Implementare logica basata su analytics/ordini
        return false;
    }

    /**
     * Ottieni prodotti simili (stessa categoria)
     */
    public function getSimilarProducts(int $limit = 5)
    {
        return self::where('category_id', $this->category_id)
                   ->where('id', '!=', $this->id)
                   ->active()
                   ->limit($limit)
                   ->get();
    }

    /**
     * Ottieni prodotti correlati con tipo specifico
     */
    public function getRelatedProductsByType(string $type, int $limit = 5)
    {
        return $this->relatedProducts()
                    ->wherePivot('relationship_type', $type)
                    ->active()
                    ->limit($limit)
                    ->get();
    }

    /**
     * Calcola il peso totale con accessori
     */
    public function getTotalWeight(): float
    {
        $totalWeight = $this->weight ?? 0;
        
        foreach ($this->accessories as $accessory) {
            $quantity = $accessory->pivot->quantity ?? 1;
            $totalWeight += ($accessory->weight ?? 0) * $quantity;
        }
        
        return $totalWeight;
    }

    /**
     * Ottieni le dimensioni totali con accessori
     */
    public function getTotalDimensions(): array
    {
        $baseDimensions = $this->dimensions ?? ['length' => 0, 'width' => 0, 'height' => 0];
        
        // TODO: Implementare logica per calcolare dimensioni totali con accessori
        return $baseDimensions;
    }

    /**
     * Verifica se il prodotto può essere spedito
     */
    public function canBeShipped(): bool
    {
        // TODO: Implementare logica basata su peso, dimensioni, zona geografica
        return $this->is_active && !$this->trashed();
    }

    /**
     * Ottieni il costo di spedizione stimato
     */
    public function getShippingCost(?string $region = null): float
    {
        // TODO: Implementare logica costi spedizione
        return 0.0;
    }

    /**
     * Ottieni il tempo di consegna stimato
     */
    public function getEstimatedDeliveryTime(?string $region = null): string
    {
        // TODO: Implementare logica tempi consegna
        return '7-10 giorni lavorativi';
    }

    /**
     * Verifica se il prodotto ha certificazioni
     */
    public function hasCertifications(): bool
    {
        return !empty($this->certifications);
    }

    /**
     * Ottieni le certificazioni del prodotto
     */
    public function getCertifications(): array
    {
        return $this->certifications ?? [];
    }

    /**
     * Verifica se il prodotto è eco-friendly
     */
    public function isEcoFriendly(): bool
    {
        $ecoTags = ['eco-friendly', 'sostenibile', 'green', 'riciclabile'];
        
        if ($this->tags) {
            foreach ($this->tags as $tag) {
                if (in_array(strtolower($tag->name), $ecoTags)) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Ottieni il punteggio di sostenibilità
     */
    public function getSustainabilityScore(): int
    {
        $score = 0;
        
        if ($this->isEcoFriendly()) {
            $score += 25;
        }
        
        if ($this->hasCertifications()) {
            $score += 25;
        }
        
        // TODO: Aggiungere altri criteri di sostenibilità
        
        return min($score, 100);
    }

    /**
     * Activity Log Configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'sku', 'base_price', 'is_active', 'is_featured', 'category_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}