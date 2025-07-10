<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

// Import dei Models usati nelle relazioni
use App\Models\Product;
use App\Models\Category;
use App\Models\Translation;
use App\Models\Image;
use App\Models\Tag;

class ProductAccessory extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug',
        'sku',
        'description',
        'short_description',
        'category_id',
        'accessory_type',
        'base_price',
        'cost_price',
        'currency',
        'weight',
        'length',
        'width',
        'height',
        'material',
        'color',
        'finish',
        'installation_notes',
        'compatibility_notes',
        'specifications',
        'features',
        'certifications',
        'is_active',
        'is_featured',
        'is_universal',
        'requires_installation',
        'sort_order',
        'stock_quantity',
        'stock_status',
        'min_order_quantity',
        'max_order_quantity',
        'lead_time_days',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'base_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'specifications' => 'json',
        'features' => 'json',
        'certifications' => 'json',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_universal' => 'boolean',
        'requires_installation' => 'boolean',
        'sort_order' => 'integer',
        'stock_quantity' => 'integer',
        'min_order_quantity' => 'integer',
        'max_order_quantity' => 'integer',
        'lead_time_days' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

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
     * Prodotti che utilizzano questo accessorio
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_accessories')
                    ->withPivot(['quantity', 'is_required', 'is_included', 'display_order', 'price_override'])
                    ->withTimestamps();
    }

    /**
     * Prodotti che richiedono questo accessorio
     */
    public function requiredByProducts(): BelongsToMany
    {
        return $this->products()->wherePivot('is_required', true);
    }

    /**
     * Prodotti che includono questo accessorio
     */
    public function includedInProducts(): BelongsToMany
    {
        return $this->products()->wherePivot('is_included', true);
    }

    /**
     * Prodotti che hanno questo accessorio come opzionale
     */
    public function optionalForProducts(): BelongsToMany
    {
        return $this->products()->wherePivot('is_required', false)->wherePivot('is_included', false);
    }

    /**
     * Immagini dell'accessorio
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable')
                    ->where('type', 'accessory')
                    ->where('status', 'active')
                    ->orderBy('created_at');
    }

    /**
     * Immagine principale dell'accessorio
     */
    public function mainImage(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')
                    ->where('type', 'accessory')
                    ->where('status', 'active')
                    ->oldest();
    }

    /**
     * Traduzioni dell'accessorio
     */
    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    /**
     * Tag associati all'accessorio
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'accessory_tags')
                    ->withTimestamps();
    }

    // ===================================================================
    // SCOPE QUERIES
    // ===================================================================

    /**
     * Scope per accessori attivi
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope per accessori in evidenza
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope per accessori universali
     */
    public function scopeUniversal($query)
    {
        return $query->where('is_universal', true);
    }

    /**
     * Scope per tipo di accessorio
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('accessory_type', $type);
    }

    /**
     * Scope per categoria
     */
    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope per materiale
     */
    public function scopeByMaterial($query, string $material)
    {
        return $query->where('material', $material);
    }

    /**
     * Scope per colore
     */
    public function scopeByColor($query, string $color)
    {
        return $query->where('color', $color);
    }

    /**
     * Scope per ricerca
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
    }

    /**
     * Scope per range di prezzo
     */
    public function scopePriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('base_price', [$minPrice, $maxPrice]);
    }

    /**
     * Scope per disponibilità
     */
    public function scopeAvailable($query)
    {
        return $query->where('stock_status', 'in_stock')
                    ->where('stock_quantity', '>', 0);
    }

    /**
     * Scope per ordinamento
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // ===================================================================
    // ACCESSORS & MUTATORS
    // ===================================================================

    /**
     * Genera automaticamente lo slug dal nome
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        
        if (!$this->slug) {
            $this->attributes['slug'] = \Str::slug($value);
        }
    }

    /**
     * Ottieni il prezzo formattato
     */
    public function getFormattedPriceAttribute(): string
    {
        return '€ ' . number_format($this->base_price, 2, ',', '.');
    }

    /**
     * Ottieni il peso formattato
     */
    public function getFormattedWeightAttribute(): string
    {
        return $this->weight ? $this->weight . ' kg' : 'N/A';
    }

    /**
     * Ottieni le dimensioni formattate
     */
    public function getFormattedDimensionsAttribute(): string
    {
        if (!$this->length || !$this->width || !$this->height) {
            return 'N/A';
        }

        return $this->length . ' x ' . $this->width . ' x ' . $this->height . ' cm';
    }

    /**
     * Ottieni l'URL dell'immagine principale
     */
    public function getMainImageUrlAttribute(): ?string
    {
        $mainImage = $this->mainImage;
        
        if (!$mainImage) {
            return null;
        }

        return $mainImage->clean_url;
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

    /**
     * Ottieni una traduzione per campo specifico
     */
    public function getTranslation(string $field, string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        
        $translation = $this->translations()
            ->where('field', $field)
            ->where('locale_code', $locale)
            ->first();

        return $translation?->value ?? null;
    }

    /**
     * Ottieni il tipo di accessorio formattato
     */
    public function getFormattedTypeAttribute(): string
    {
        return match($this->accessory_type) {
            'mounting' => 'Fissaggio',
            'protection' => 'Protezione',
            'decoration' => 'Decorazione',
            'maintenance' => 'Manutenzione',
            'lighting' => 'Illuminazione',
            'comfort' => 'Comfort',
            'security' => 'Sicurezza',
            'customization' => 'Personalizzazione',
            default => 'Accessorio'
        };
    }

    /**
     * Ottieni lo stato stock formattato
     */
    public function getFormattedStockStatusAttribute(): string
    {
        return match($this->stock_status) {
            'in_stock' => 'Disponibile',
            'out_of_stock' => 'Esaurito',
            'on_backorder' => 'In arrivo',
            'discontinued' => 'Fuori produzione',
            default => 'Non disponibile'
        };
    }

    // ===================================================================
    // HELPER METHODS
    // ===================================================================

    /**
     * Verifica se l'accessorio è attivo
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Verifica se l'accessorio è in evidenza
     */
    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    /**
     * Verifica se l'accessorio è universale
     */
    public function isUniversal(): bool
    {
        return $this->is_universal;
    }

    /**
     * Verifica se richiede installazione
     */
    public function requiresInstallation(): bool
    {
        return $this->requires_installation;
    }

    /**
     * Verifica se è disponibile
     */
    public function isAvailable(): bool
    {
        return $this->is_active && 
               $this->stock_status === 'in_stock' && 
               $this->stock_quantity > 0;
    }

    /**
     * Verifica se è esaurito
     */
    public function isOutOfStock(): bool
    {
        return $this->stock_status === 'out_of_stock' || $this->stock_quantity <= 0;
    }

    /**
     * Verifica se è in low stock
     */
    public function isLowStock(int $threshold = 5): bool
    {
        return $this->stock_quantity <= $threshold && $this->stock_quantity > 0;
    }

    /**
     * Verifica se ha immagini
     */
    public function hasImages(): bool
    {
        return $this->images()->exists();
    }

    /**
     * Verifica se ha specifiche tecniche
     */
    public function hasSpecifications(): bool
    {
        return !empty($this->specifications);
    }

    /**
     * Verifica se ha note di installazione
     */
    public function hasInstallationNotes(): bool
    {
        return !empty($this->installation_notes);
    }

    /**
     * Verifica se ha note di compatibilità
     */
    public function hasCompatibilityNotes(): bool
    {
        return !empty($this->compatibility_notes);
    }

    /**
     * Verifica se è compatibile con un prodotto
     */
    public function isCompatibleWith(Product $product): bool
    {
        // Se è universale, è compatibile con tutti
        if ($this->is_universal) {
            return true;
        }

        // Verifica se è già associato al prodotto
        return $this->products()->where('product_id', $product->id)->exists();
    }

    /**
     * Verifica se è richiesto per un prodotto
     */
    public function isRequiredFor(Product $product): bool
    {
        return $this->requiredByProducts()->where('product_id', $product->id)->exists();
    }

    /**
     * Verifica se è incluso in un prodotto
     */
    public function isIncludedIn(Product $product): bool
    {
        return $this->includedInProducts()->where('product_id', $product->id)->exists();
    }

    /**
     * Verifica se è opzionale per un prodotto
     */
    public function isOptionalFor(Product $product): bool
    {
        return $this->optionalForProducts()->where('product_id', $product->id)->exists();
    }

    /**
     * Ottieni la quantità per un prodotto specifico
     */
    public function getQuantityForProduct(Product $product): int
    {
        $pivot = $this->products()->where('product_id', $product->id)->first();
        return $pivot ? $pivot->pivot->quantity : 1;
    }

    /**
     * Ottieni il prezzo per un prodotto specifico
     */
    public function getPriceForProduct(Product $product): float
    {
        $pivot = $this->products()->where('product_id', $product->id)->first();
        return $pivot && $pivot->pivot->price_override ? $pivot->pivot->price_override : $this->base_price;
    }

    /**
     * Ottieni il prezzo formattato per un prodotto
     */
    public function getFormattedPriceForProduct(Product $product): string
    {
        return '€ ' . number_format($this->getPriceForProduct($product), 2, ',', '.');
    }

    /**
     * Ottieni accessori simili
     */
    public function getSimilarAccessories(int $limit = 5)
    {
        return self::where('id', '!=', $this->id)
                   ->where('accessory_type', $this->accessory_type)
                   ->active()
                   ->ordered()
                   ->limit($limit)
                   ->get();
    }

    /**
     * Ottieni accessori compatibili
     */
    public function getCompatibleAccessories(int $limit = 5)
    {
        return self::where('id', '!=', $this->id)
                   ->where('category_id', $this->category_id)
                   ->active()
                   ->ordered()
                   ->limit($limit)
                   ->get();
    }

    /**
     * Decrementa lo stock
     */
    public function decrementStock(int $quantity = 1): bool
    {
        if ($this->stock_quantity < $quantity) {
            return false;
        }
        
        $this->stock_quantity -= $quantity;
        
        // Aggiorna stato se necessario
        if ($this->stock_quantity <= 0) {
            $this->stock_status = 'out_of_stock';
        }
        
        return $this->save();
    }

    /**
     * Incrementa lo stock
     */
    public function incrementStock(int $quantity = 1): bool
    {
        $this->stock_quantity += $quantity;
        
        // Aggiorna stato se necessario
        if ($this->stock_quantity > 0 && $this->stock_status === 'out_of_stock') {
            $this->stock_status = 'in_stock';
        }
        
        return $this->save();
    }

    /**
     * Ottieni statistiche dell'accessorio
     */
    public function getStats(): array
    {
        return [
            'total_products' => $this->products()->count(),
            'required_products' => $this->requiredByProducts()->count(),
            'included_products' => $this->includedInProducts()->count(),
            'optional_products' => $this->optionalForProducts()->count(),
            'stock_quantity' => $this->stock_quantity,
            'stock_status' => $this->stock_status,
            'is_universal' => $this->is_universal,
            'requires_installation' => $this->requires_installation,
            'has_images' => $this->hasImages(),
        ];
    }

    // ===================================================================
    // STATIC METHODS
    // ===================================================================

    /**
     * Ottieni accessori per tipo
     */
    public static function byType(string $type)
    {
        return self::active()
                   ->byType($type)
                   ->ordered()
                   ->get();
    }

    /**
     * Ottieni accessori universali
     */
    public static function universal()
    {
        return self::active()
                   ->universal()
                   ->ordered()
                   ->get();
    }

    /**
     * Ottieni accessori in evidenza
     */
    public static function featured(int $limit = 10)
    {
        return self::active()
                   ->featured()
                   ->ordered()
                   ->limit($limit)
                   ->get();
    }

    /**
     * Ottieni accessori compatibili con un prodotto
     */
    public static function compatibleWith(Product $product)
    {
        return self::active()
                   ->where(function ($query) use ($product) {
                       $query->where('is_universal', true)
                             ->orWhereHas('products', function ($q) use ($product) {
                                 $q->where('product_id', $product->id);
                             });
                   })
                   ->ordered()
                   ->get();
    }

    /**
     * Ricerca accessori
     */
    public static function search(string $query, int $limit = 10)
    {
        return self::active()
                   ->search($query)
                   ->ordered()
                   ->limit($limit)
                   ->get();
    }

    /**
     * Ottieni statistiche globali
     */
    public static function getGlobalStats(): array
    {
        return [
            'total_accessories' => self::count(),
            'active_accessories' => self::active()->count(),
            'featured_accessories' => self::featured()->count(),
            'universal_accessories' => self::universal()->count(),
            'by_type' => self::selectRaw('accessory_type, COUNT(*) as count')
                            ->groupBy('accessory_type')
                            ->pluck('count', 'accessory_type')
                            ->toArray(),
            'by_stock_status' => self::selectRaw('stock_status, COUNT(*) as count')
                                   ->groupBy('stock_status')
                                   ->pluck('count', 'stock_status')
                                   ->toArray(),
        ];
    }

    /**
     * Activity Log Configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'sku', 'base_price', 'is_active', 'is_featured', 'stock_quantity', 'stock_status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}