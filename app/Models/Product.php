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
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Facades\Activity;

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
        'beauty_category',
        'primary_image_id',
        'has_material_options',
        'has_color_options',
        'has_finish_options',
        'default_material_tag_id',
        'default_color_tag_id',
        'default_finish_tag_id',
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
        'has_material_options' => 'boolean',
        'has_color_options' => 'boolean',
        'has_finish_options' => 'boolean',
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
    public function variants()
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

    // ===================================================================
    // RELAZIONI IMMAGINI - BEAUTY SYSTEM ✨
    // ===================================================================

    /**
     * Relazione con l'immagine principale designata
     */
    public function primaryImage(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'primary_image_id');
    }

    /**
     * Tutte le immagini collegate al prodotto (gallery + beauty)
     */
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable')
                    ->whereIn('type', ['gallery', 'beauty']) // ✨ Include both types
                    ->where('status', 'active')
                    ->ordered();
    }

    /**
     * Solo immagini gallery (normali)
     */
    public function galleryImages()
    {
        return $this->morphMany(Image::class, 'imageable')
                    ->where('type', 'gallery')
                    ->where('status', 'active')
                    ->ordered();
    }

    /**
     * Solo immagini beauty (speciali)
     */
    public function beautyImages()
    {
        return $this->morphMany(Image::class, 'imageable')
                    ->where('type', 'beauty')
                    ->where('status', 'active')
                    ->ordered();
    }

    /**
     * Traduzioni polymorphic
     */
    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    /**
     * Tag materiali disponibili per questo prodotto
     */
    public function materialTags(): BelongsToMany
    {
        return $this->tags()->where('type', 'material')->active();
    }

    /**
     * Tag colori disponibili per questo prodotto
     */
    public function colorTags(): BelongsToMany
    {
        return $this->tags()->where('type', 'color')->active();
    }

    /**
     * Tag finiture disponibili per questo prodotto
     */
    public function finishTags(): BelongsToMany
    {
        return $this->tags()->where('type', 'finish')->active();
    }

    /**
     * Tag materiale di default
     */
    public function defaultMaterialTag(): BelongsTo
    {
        return $this->belongsTo(Tag::class, 'default_material_tag_id');
    }

    /**
     * Tag colore di default
     */
    public function defaultColorTag(): BelongsTo
    {
        return $this->belongsTo(Tag::class, 'default_color_tag_id');
    }

    /**
     * Tag finitura di default
     */
    public function defaultFinishTag(): BelongsTo
    {
        return $this->belongsTo(Tag::class, 'default_finish_tag_id');
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
     * Scope per prodotti con immagini ✨ UPDATED
     */
    public function scopeWithImages($query)
    {
        return $query->whereHas('images');
    }

    /**
     * Scope per prodotti senza immagini ✨ UPDATED
     */
    public function scopeWithoutImages($query)
    {
        return $query->whereDoesntHave('images');
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
    // ACCESSORS & MUTATORS - PRIMARY IMAGE SYSTEM ✨
    // ===================================================================


    /**
     * Ottieni i materiali disponibili - VERSIONE DINAMICA
     * Se has_material_options = true, usa i tag, altrimenti usa il campo 'material'
     */
    public function getMaterialsOptionsAttribute()
    {
        if ($this->has_material_options) {
            return $this->materialTags()->ordered()->get();
        }
        
        // Fallback: usa il campo 'material' esistente
        return $this->material ? collect([['name' => $this->material]]) : collect();
    }

    /**
     * Ottieni i colori disponibili - VERSIONE DINAMICA
     * Se has_color_options = true, usa i tag, altrimenti usa il campo 'color'
     */
    public function getColorsOptionsAttribute()
    {
        if ($this->has_color_options) {
            return $this->colorTags()->ordered()->get();
        }
        
        // Fallback: usa il campo 'color' esistente
        return $this->color ? collect([['name' => $this->color]]) : collect();
    }

    /**
     * Ottieni le finiture disponibili - VERSIONE DINAMICA
     * Se has_finish_options = true, usa i tag, altrimenti usa il campo 'finish'
     */
    public function getFinishesOptionsAttribute()
    {
        if ($this->has_finish_options) {
            return $this->finishTags()->ordered()->get();
        }
        
        // Fallback: usa il campo 'finish' esistente
        return $this->finish ? collect([['name' => $this->finish]]) : collect();
    }

    /**
     * Ottieni il materiale principale (compatibilità con codice esistente)
     */
    public function getPrimaryMaterialAttribute(): string
    {
        if ($this->has_material_options && $this->defaultMaterialTag) {
            return $this->defaultMaterialTag->name;
        }
        return $this->material ?? '';
    }

    /**
     * Ottieni il colore principale (compatibilità con codice esistente)
     */
    public function getPrimaryColorAttribute(): string
    {
        if ($this->has_color_options && $this->defaultColorTag) {
            return $this->defaultColorTag->name;
        }
        return $this->color ?? '';
    }

    /**
     * Ottieni la finitura principale (compatibilità con codice esistente)
     */
    public function getPrimaryFinishAttribute(): string
    {
        if ($this->has_finish_options && $this->defaultFinishTag) {
            return $this->defaultFinishTag->name;
        }
        return $this->finish ?? '';
    }

    /**
     * Ottieni l'immagine principale
     */
    public function getPrimaryImageAttribute(): ?Image
    {
        if ($this->primary_image_id) {
        // forza la query sul BelongsTo senza passare per $this->primaryImage
        return $this->primaryImage()->first();
    }

    // fallback alla prima gallery
    return $this->galleryImages()->first();
    }

    /**
     * Ottieni URL dell'immagine principale
     */
    public function getPrimaryImageUrlAttribute(): ?string
    {
        return $this->primary_image?->url;
    }

    /**
     * Ottieni alt text dell'immagine principale
     */
    public function getPrimaryImageAltAttribute(): ?string
    {
        return $this->primary_image?->alt_text ?? $this->name;
    }

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

    // ===========================
// BEAUTY CATEGORIES METHODS ✨
// ===========================

/**
 * Ottieni beauty images per categoria specifica
 */
public function getBeautyByCategory($category)
{
    return $this->beautyImages()->where('beauty_category', $category);
}

/**
 * Ottieni beauty images per sfondo principale
 */
public function getMainBeautyImages()
{
    return $this->getBeautyByCategory('main');
}

/**
 * Ottieni beauty images per slideshow
 */
public function getSlideshowBeautyImages()
{
    return $this->getBeautyByCategory('slideshow');
}

/**
 * Ottieni beauty images per header
 */
public function getHeaderBeautyImages()
{
    return $this->getBeautyByCategory('header');
}

/**
 * Ottieni beauty images senza categoria
 */
public function getUncategorizedBeautyImages()
{
    return $this->beautyImages()->whereNull('beauty_category');
}

/**
 * Verifica se ha almeno una beauty image nella categoria
 */
public function hasBeautyInCategory($category): bool
{
    return $this->getBeautyByCategory($category)->exists();
}

/**
 * Verifica se ha sfondo principale
 */
public function hasMainBeautyImage(): bool
{
    return $this->hasBeautyInCategory('main');
}

/**
 * Verifica se ha immagini slideshow
 */
public function hasSlideshowImages(): bool
{
    return $this->hasBeautyInCategory('slideshow');
}

/**
 * Verifica se ha immagini header
 */
public function hasHeaderImages(): bool
{
    return $this->hasBeautyInCategory('header');
}

/**
 * Ottieni la prima beauty image della categoria
 */
public function getFirstBeautyByCategory($category)
{
    return $this->getBeautyByCategory($category)->first();
}

/**
 * Ottieni URL della prima beauty image della categoria
 */
public function getFirstBeautyUrlByCategory($category): ?string
{
    $image = $this->getFirstBeautyByCategory($category);
    return $image ? $image->url : null;
}

/**
 * Conta beauty images per categoria
 */
public function countBeautyByCategory($category): int
{
    return $this->getBeautyByCategory($category)->count();
}

/**
 * Ottieni stats complete delle beauty categories
 */
public function getBeautyCategoryStats(): array
{
    return [
        'main' => $this->countBeautyByCategory('main'),
        'slideshow' => $this->countBeautyByCategory('slideshow'),
        'header' => $this->countBeautyByCategory('header'),
        'uncategorized' => $this->beautyImages()->whereNull('beauty_category')->count(),
        'total' => $this->beauty_count
    ];
}

/**
 * Assegna beauty image a categoria
 */
public function assignBeautyToCategory(Image $image, string $category): bool
{
    if (!$this->ownsImage($image) || !$image->isBeauty()) {
        throw new \InvalidArgumentException('Image must be a beauty image belonging to this product');
    }
    
    $validCategories = ['main', 'slideshow', 'header'];
    if (!in_array($category, $validCategories)) {
        throw new \InvalidArgumentException('Invalid beauty category');
    }
    
    $image->update(['beauty_category' => $category]);
    
    Activity::performedOn($this)
        ->causedBy(auth()->user())
        ->withProperties([
            'image_id' => $image->id,
            'category' => $category
        ])
        ->log("Assigned beauty image to {$category}");
    
    return true;
}

/**
 * Rimuovi beauty image da categoria
 */
public function removeBeautyFromCategory(Image $image): bool
{
    if (!$this->ownsImage($image) || !$image->isBeauty()) {
        throw new \InvalidArgumentException('Image must be a beauty image belonging to this product');
    }
    
    $oldCategory = $image->beauty_category;
    $image->update(['beauty_category' => null]);
    
    Activity::performedOn($this)
        ->causedBy(auth()->user())
        ->withProperties([
            'image_id' => $image->id,
            'old_category' => $oldCategory
        ])
        ->log("Removed beauty image from {$oldCategory}");
    
    return true;
}

    // ===================================================================
    // BEAUTY SYSTEM METHODS ✨
    // ===================================================================

    /**
     * Mark image as beauty
     */
    public function markAsBeauty(Image $image): bool
    {
        if (!$this->ownsImage($image)) {
            throw new \InvalidArgumentException('Image does not belong to this product');
        }
        
        $image->update(['type' => 'beauty']);
        
        Activity::performedOn($this)
            ->causedBy(auth()->user())
            ->withProperties(['image_id' => $image->id])
            ->log('Marked image as beauty');
        
        return true;
    }

    /**
     * Mark image as gallery (remove from beauty)
     */
    public function markAsGallery(Image $image): bool
    {
        if (!$this->ownsImage($image)) {
            throw new \InvalidArgumentException('Image does not belong to this product');
        }
        
        $image->update(['type' => 'gallery']);
        
        Activity::performedOn($this)
            ->causedBy(auth()->user())
            ->withProperties(['image_id' => $image->id])
            ->log('Marked image as gallery');
        
        return true;
    }

    /**
     * Simple beauty stats
     */
    public function getBeautyCountAttribute(): int
    {
        return $this->beautyImages()->count();
    }

    /**
     * Simple gallery stats  
     */
    public function getGalleryCountAttribute(): int
    {
        return $this->galleryImages()->count();
    }

    /**
     * Controlla se un'immagine appartiene a questo prodotto
     */
    public function ownsImage(Image $image): bool
    {
        return $image->imageable_type === static::class && 
               $image->imageable_id === $this->id;
    }

    // ===================================================================
    // PRIMARY IMAGE METHODS ✨
    // ===================================================================

    /**
     * Imposta un'immagine come principale
     */
    public function setPrimaryImage(Image $image): void
    {
        if ($image->imageable_type !== static::class || $image->imageable_id !== $this->id) {
            throw new \InvalidArgumentException('L\'immagine non appartiene a questo prodotto');
        }

        $this->update(['primary_image_id' => $image->id]);
        
        Activity::performedOn($this)
            ->causedBy(auth()->user())
            ->withProperties(['image_id' => $image->id])
            ->log('Set primary image');
    }

    /**
     * Rimuovi l'immagine principale
     */
    public function clearPrimaryImage(): void
    {
        $this->update(['primary_image_id' => null]);
        
        Activity::performedOn($this)
            ->causedBy(auth()->user())
            ->log('Cleared primary image');
    }

    /**
     * Controlla se un'immagine è quella principale
     */
    public function isPrimaryImage(Image $image): bool
    {
        return $this->primary_image_id === $image->id;
    }

    /**
     * Verifica se ha un'immagine principale
     */
    public function hasPrimaryImage(): bool
    {
        return !is_null($this->primary_image_id) && $this->primaryImage()->exists();
    }

    // ===================================================================
    // HELPER METHODS ✨
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
     * Ottieni il numero totale di immagini
     */
    public function getImageCountAttribute(): int
    {
        return $this->images()->count();
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

    /**
 * Verifica se il prodotto ha opzioni configurabili
 */
public function hasConfigurableOptions(): bool
{
    return $this->has_material_options || $this->has_color_options || $this->has_finish_options;
}

/**
 * Aggiungi opzione materiale
 */
public function addMaterialOption(Tag $materialTag): void
{
    if ($materialTag->type !== 'material') {
        throw new \InvalidArgumentException('Il tag deve essere di tipo "material"');
    }
    $this->tags()->syncWithoutDetaching([$materialTag->id]);
    $this->update(['has_material_options' => true]);
}

/**
 * Aggiungi opzione colore
 */
public function addColorOption(Tag $colorTag): void
{
    if ($colorTag->type !== 'color') {
        throw new \InvalidArgumentException('Il tag deve essere di tipo "color"');
    }
    $this->tags()->syncWithoutDetaching([$colorTag->id]);
    $this->update(['has_color_options' => true]);
}

/**
 * Aggiungi opzione finitura
 */
public function addFinishOption(Tag $finishTag): void
{
    if ($finishTag->type !== 'finish') {
        throw new \InvalidArgumentException('Il tag deve essere di tipo "finish"');
    }
    $this->tags()->syncWithoutDetaching([$finishTag->id]);
    $this->update(['has_finish_options' => true]);
}

/**
 * Imposta materiale di default
 */
public function setDefaultMaterial(Tag $materialTag): void
{
    if ($materialTag->type !== 'material') {
        throw new \InvalidArgumentException('Il tag deve essere di tipo "material"');
    }
    if (!$this->tags()->where('tag_id', $materialTag->id)->exists()) {
        $this->addMaterialOption($materialTag);
    }
    $this->update(['default_material_tag_id' => $materialTag->id]);
}

/**
 * Imposta colore di default
 */
public function setDefaultColor(Tag $colorTag): void
{
    if ($colorTag->type !== 'color') {
        throw new \InvalidArgumentException('Il tag deve essere di tipo "color"');
    }
    if (!$this->tags()->where('tag_id', $colorTag->id)->exists()) {
        $this->addColorOption($colorTag);
    }
    $this->update(['default_color_tag_id' => $colorTag->id]);
}

/**
 * Imposta finitura di default
 */
public function setDefaultFinish(Tag $finishTag): void
{
    if ($finishTag->type !== 'finish') {
        throw new \InvalidArgumentException('Il tag deve essere di tipo "finish"');
    }
    if (!$this->tags()->where('tag_id', $finishTag->id)->exists()) {
        $this->addFinishOption($finishTag);
    }
    $this->update(['default_finish_tag_id' => $finishTag->id]);
}

/**
 * Ottieni tutti i materiali disponibili nel sistema
 */
public static function getAvailableMaterials()
{
    return Tag::byType('material')->active()->ordered()->get();
}

/**
 * Ottieni tutti i colori disponibili nel sistema
 */
public static function getAvailableColors()
{
    return Tag::byType('color')->active()->ordered()->get();
}

/**
 * Ottieni tutte le finiture disponibili nel sistema
 */
public static function getAvailableFinishes()
{
    return Tag::byType('finish')->active()->ordered()->get();
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

    /**
     * Activity Log Configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name', 
                'sku', 
                'base_price', 
                'is_active', 
                'is_featured', 
                'category_id',
                'primary_image_id'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // ===================================================================
    // BOOT METHOD - EVENT HANDLERS ✨
    // ===================================================================

    /**
     * Boot method per eventi automatici
     */
    protected static function boot()
    {
        parent::boot();

        // Genera automaticamente lo slug dal nome
        static::creating(function ($product) {
            if (!$product->slug) {
                $product->slug = \Str::slug($product->name);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && !$product->isDirty('slug')) {
                $product->slug = \Str::slug($product->name);
            }
        });

        // Quando si elimina un prodotto, gestisci le immagini
        static::deleting(function ($product) {
            // Clear primary reference first
            $product->update(['primary_image_id' => null]);
            
            // Then soft delete all images
            $product->images()->delete();
        });

        // Quando si ripristina un prodotto, ripristina le immagini
        static::restoring(function ($product) {
            $product->images()->withTrashed()->restore();
            
            // Try to set first image as primary
            $firstImage = $product->images()->first();
            if ($firstImage && !$product->primary_image_id) {
                $product->update(['primary_image_id' => $firstImage->id]);
            }
        });
    }

    
}