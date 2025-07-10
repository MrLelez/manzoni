<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

// Import dei Models usati nelle relazioni
use App\Models\Product;
use App\Models\Translation;
use App\Models\Image;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'product_id',
        'variant_type',
        'name',
        'description',
        'sku_suffix',
        'price_adjustment',
        'price_adjustment_type',
        'weight_adjustment',
        'dimensions_adjustment',
        'color',
        'material',
        'finish',
        'size',
        'configuration',
        'technical_specs',
        'installation_notes',
        'compatibility_notes',
        'is_default',
        'is_active',
        'is_removable',
        'sort_order',
        'stock_quantity',
        'min_quantity',
        'max_quantity',
        'lead_time_days',
        'availability_status',
        'attributes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'price_adjustment' => 'decimal:2',
        'weight_adjustment' => 'decimal:2',
        'dimensions_adjustment' => 'json',
        'configuration' => 'json',
        'technical_specs' => 'json',
        'attributes' => 'json',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'is_removable' => 'boolean',
        'sort_order' => 'integer',
        'stock_quantity' => 'integer',
        'min_quantity' => 'integer',
        'max_quantity' => 'integer',
        'lead_time_days' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [];

    // ===================================================================
    // RELAZIONI PRINCIPALI
    // ===================================================================

    /**
     * Prodotto di appartenenza
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Immagini specifiche della variante
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable')
                    ->where('type', 'product')
                    ->where('status', 'active')
                    ->orderBy('created_at');
    }

    /**
     * Immagine principale della variante
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
    // SCOPE QUERIES
    // ===================================================================

    /**
     * Scope per varianti attive
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope per varianti removibili
     */
    public function scopeRemovable($query)
    {
        return $query->where('is_removable', true);
    }

    /**
     * Scope per varianti fisse
     */
    public function scopeFixed($query)
    {
        return $query->where('is_removable', false);
    }

    /**
     * Scope per varianti default
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope per tipo di variante
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('variant_type', $type);
    }

    /**
     * Scope per colore
     */
    public function scopeByColor($query, string $color)
    {
        return $query->where('color', $color);
    }

    /**
     * Scope per materiale
     */
    public function scopeByMaterial($query, string $material)
    {
        return $query->where('material', $material);
    }

    /**
     * Scope per finitura
     */
    public function scopeByFinish($query, string $finish)
    {
        return $query->where('finish', $finish);
    }

    /**
     * Scope per dimensione
     */
    public function scopeBySize($query, string $size)
    {
        return $query->where('size', $size);
    }

    /**
     * Scope per disponibilità
     */
    public function scopeAvailable($query)
    {
        return $query->where('availability_status', 'available')
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
     * Ottieni il SKU completo (prodotto + suffisso)
     */
    public function getFullSkuAttribute(): string
    {
        $baseSku = $this->product->sku ?? 'PROD';
        return $this->sku_suffix ? $baseSku . '-' . $this->sku_suffix : $baseSku;
    }

    /**
     * Ottieni il prezzo finale della variante
     */
    public function getFinalPriceAttribute(): float
    {
        $basePrice = $this->product->base_price ?? 0;
        
        if ($this->price_adjustment_type === 'percentage') {
            return $basePrice * (1 + ($this->price_adjustment / 100));
        }
        
        return $basePrice + ($this->price_adjustment ?? 0);
    }

    /**
     * Ottieni il prezzo finale per livello rivenditore
     */
    public function getFinalPriceForLevel(int $level): float
    {
        $basePrice = $this->product->getPriceForLevel($level);
        
        if ($this->price_adjustment_type === 'percentage') {
            return $basePrice * (1 + ($this->price_adjustment / 100));
        }
        
        return $basePrice + ($this->price_adjustment ?? 0);
    }

    /**
     * Ottieni il prezzo finale formattato
     */
    public function getFormattedFinalPriceAttribute(): string
    {
        return '€ ' . number_format($this->final_price, 2, ',', '.');
    }

    /**
     * Ottieni il prezzo finale formattato per livello
     */
    public function getFormattedFinalPriceForLevel(int $level): string
    {
        return '€ ' . number_format($this->getFinalPriceForLevel($level), 2, ',', '.');
    }

    /**
     * Ottieni il peso finale
     */
    public function getFinalWeightAttribute(): float
    {
        $baseWeight = $this->product->weight ?? 0;
        return $baseWeight + ($this->weight_adjustment ?? 0);
    }

    /**
     * Ottieni il peso finale formattato
     */
    public function getFormattedFinalWeightAttribute(): string
    {
        return $this->final_weight . ' kg';
    }

    /**
     * Ottieni le dimensioni finali
     */
    public function getFinalDimensionsAttribute(): array
    {
        $baseDimensions = $this->product->dimensions ?? ['length' => 0, 'width' => 0, 'height' => 0];
        $adjustments = $this->dimensions_adjustment ?? [];
        
        return [
            'length' => ($baseDimensions['length'] ?? 0) + ($adjustments['length'] ?? 0),
            'width' => ($baseDimensions['width'] ?? 0) + ($adjustments['width'] ?? 0),
            'height' => ($baseDimensions['height'] ?? 0) + ($adjustments['height'] ?? 0),
        ];
    }

    /**
     * Ottieni le dimensioni finali formattate
     */
    public function getFormattedFinalDimensionsAttribute(): string
    {
        $dims = $this->final_dimensions;
        return $dims['length'] . ' x ' . $dims['width'] . ' x ' . $dims['height'] . ' cm';
    }

    /**
     * Ottieni l'URL dell'immagine principale
     */
    public function getMainImageUrlAttribute(): ?string
    {
        // Prima controlla se ha immagine propria
        $mainImage = $this->mainImage;
        
        if ($mainImage) {
            return $mainImage->clean_url;
        }
        
        // Altrimenti usa l'immagine del prodotto
        return $this->product->main_image_url;
    }

    /**
     * Ottieni il nome completo (prodotto + variante)
     */
    public function getFullNameAttribute(): string
    {
        $productName = $this->product->name ?? 'Prodotto';
        return $productName . ' - ' . $this->name;
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
            ->where('locale', $locale)
            ->first();

        return $translation?->value ?? null;
    }

    // ===================================================================
    // HELPER METHODS
    // ===================================================================

    /**
     * Verifica se la variante è disponibile
     */
    public function isAvailable(): bool
    {
        return $this->is_active && 
               $this->availability_status === 'available' && 
               $this->stock_quantity > 0 &&
               !$this->trashed();
    }

    /**
     * Verifica se la variante è la default
     */
    public function isDefault(): bool
    {
        return $this->is_default;
    }

    /**
     * Verifica se la variante è removibile
     */
    public function isRemovable(): bool
    {
        return $this->is_removable;
    }

    /**
     * Verifica se la variante è fissa
     */
    public function isFixed(): bool
    {
        return !$this->is_removable;
    }

    /**
     * Verifica se ha immagini proprie
     */
    public function hasOwnImages(): bool
    {
        return $this->images()->exists();
    }

    /**
     * Verifica se ha specifiche tecniche
     */
    public function hasTechnicalSpecs(): bool
    {
        return !empty($this->technical_specs);
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
     * Verifica se è in stock
     */
    public function isInStock(): bool
    {
        return $this->stock_quantity > 0;
    }

    /**
     * Verifica se è in low stock
     */
    public function isLowStock(int $threshold = 5): bool
    {
        return $this->stock_quantity <= $threshold && $this->stock_quantity > 0;
    }

    /**
     * Verifica se è out of stock
     */
    public function isOutOfStock(): bool
    {
        return $this->stock_quantity <= 0;
    }

    /**
     * Ottieni il tempo di consegna
     */
    public function getDeliveryTime(): string
    {
        if ($this->lead_time_days <= 0) {
            return 'Disponibile immediatamente';
        }
        
        if ($this->lead_time_days === 1) {
            return '1 giorno lavorativo';
        }
        
        return $this->lead_time_days . ' giorni lavorativi';
    }

    /**
     * Ottieni lo stato di disponibilità tradotto
     */
    public function getAvailabilityStatusText(): string
    {
        return match($this->availability_status) {
            'available' => 'Disponibile',
            'limited' => 'Disponibilità limitata',
            'preorder' => 'Preordine',
            'discontinued' => 'Fuori produzione',
            'out_of_stock' => 'Esaurito',
            default => 'Non disponibile'
        };
    }

    /**
     * Ottieni il colore in formato esadecimale
     */
    public function getColorHex(): ?string
    {
        // Mappa dei colori comuni
        $colorMap = [
            'bianco' => '#FFFFFF',
            'nero' => '#000000',
            'grigio' => '#808080',
            'blu' => '#0000FF',
            'rosso' => '#FF0000',
            'verde' => '#00FF00',
            'giallo' => '#FFFF00',
            'marrone' => '#A52A2A',
            'beige' => '#F5F5DC',
            'antracite' => '#2F2F2F',
        ];
        
        $color = strtolower($this->color);
        return $colorMap[$color] ?? null;
    }

    /**
     * Ottieni gli attributi personalizzati
     */
    public function getCustomAttributes(): array
    {
        return $this->attributes ?? [];
    }

    /**
     * Ottieni un attributo personalizzato
     */
    public function getCustomAttribute(string $key): mixed
    {
        return $this->getCustomAttributes()[$key] ?? null;
    }

    /**
     * Imposta un attributo personalizzato
     */
    public function setCustomAttribute(string $key, mixed $value): void
    {
        $attributes = $this->getCustomAttributes();
        $attributes[$key] = $value;
        $this->attributes = $attributes;
    }

    /**
     * Rimuovi un attributo personalizzato
     */
    public function removeCustomAttribute(string $key): void
    {
        $attributes = $this->getCustomAttributes();
        unset($attributes[$key]);
        $this->attributes = $attributes;
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
        return $this->save();
    }

    /**
     * Incrementa lo stock
     */
    public function incrementStock(int $quantity = 1): bool
    {
        $this->stock_quantity += $quantity;
        return $this->save();
    }

    /**
     * Imposta come variante default
     */
    public function setAsDefault(): bool
    {
        // Rimuovi default da altre varianti dello stesso prodotto
        $this->product->variants()->update(['is_default' => false]);
        
        // Imposta questa come default
        $this->is_default = true;
        return $this->save();
    }

    /**
     * Ottieni le varianti sorelle (stesso prodotto)
     */
    public function getSiblings()
    {
        return $this->product->variants()
                    ->where('id', '!=', $this->id)
                    ->ordered()
                    ->get();
    }

    /**
     * Ottieni le varianti compatibili
     */
    public function getCompatibleVariants()
    {
        return $this->getSiblings()->filter(function ($variant) {
            return $this->isCompatibleWith($variant);
        });
    }

    /**
     * Verifica compatibilità con altra variante
     */
    public function isCompatibleWith(ProductVariant $variant): bool
    {
        // Logica di compatibilità personalizzabile
        if (empty($this->compatibility_notes) && empty($variant->compatibility_notes)) {
            return true;
        }
        
        // TODO: Implementare logica di compatibilità basata su regole
        return true;
    }

    /**
     * Ottieni le configurazioni disponibili
     */
    public function getAvailableConfigurations(): array
    {
        return $this->configuration ?? [];
    }

    /**
     * Verifica se supporta una configurazione
     */
    public function supportsConfiguration(string $config): bool
    {
        return in_array($config, $this->getAvailableConfigurations());
    }

    /**
     * Ottieni il costo aggiuntivo formattato
     */
    public function getFormattedPriceAdjustmentAttribute(): string
    {
        if ($this->price_adjustment == 0) {
            return 'Incluso';
        }
        
        if ($this->price_adjustment_type === 'percentage') {
            return ($this->price_adjustment > 0 ? '+' : '') . $this->price_adjustment . '%';
        }
        
        $sign = $this->price_adjustment > 0 ? '+' : '';
        return $sign . '€ ' . number_format(abs($this->price_adjustment), 2, ',', '.');
    }

    /**
     * Ottieni statistiche della variante
     */
    public function getStats(): array
    {
        return [
            'total_orders' => 0, // TODO: Implementare conteggio ordini
            'stock_quantity' => $this->stock_quantity,
            'price_adjustment' => $this->price_adjustment,
            'final_price' => $this->final_price,
            'is_popular' => false, // TODO: Implementare logica popolarità
            'availability' => $this->availability_status,
            'has_own_images' => $this->hasOwnImages(),
        ];
    }

    // ===================================================================
    // STATIC METHODS
    // ===================================================================

    /**
     * Ottieni tutte le varianti di un prodotto
     */
    public static function forProduct(int $productId)
    {
        return self::where('product_id', $productId)
                   ->active()
                   ->ordered()
                   ->get();
    }

    /**
     * Ottieni varianti per tipo
     */
    public static function byType(string $type)
    {
        return self::where('variant_type', $type)
                   ->active()
                   ->ordered()
                   ->get();
    }

    /**
     * Ottieni varianti default
     */
    public static function defaults()
    {
        return self::where('is_default', true)
                   ->active()
                   ->get();
    }

    /**
     * Ottieni varianti disponibili
     */
    public static function available()
    {
        return self::where('is_active', true)
                   ->where('availability_status', 'available')
                   ->where('stock_quantity', '>', 0)
                   ->get();
    }

    /**
     * Activity Log Configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'variant_type', 'price_adjustment', 'is_active', 'is_default', 'stock_quantity'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}