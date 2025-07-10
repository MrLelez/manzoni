<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

// Import dei Models usati nelle relazioni
use App\Models\Product;
use App\Models\Translation;

class Tag extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'icon',
        'type',
        'is_active',
        'is_featured',
        'sort_order',
        'usage_count',
        'meta_title',
        'meta_description',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'usage_count' => 'integer',
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
     * Prodotti associati al tag
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_tags')
                    ->withTimestamps();
    }

    /**
     * Prodotti attivi associati al tag
     */
    public function activeProducts(): BelongsToMany
    {
        return $this->products()->where('is_active', true);
    }

    /**
     * Traduzioni del tag
     */
    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    // ===================================================================
    // SCOPE QUERIES
    // ===================================================================

    /**
     * Scope per tag attivi
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope per tag in evidenza
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope per tipo di tag
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope per ricerca
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
    }

    /**
     * Scope per tag popolari
     */
    public function scopePopular($query, int $minUsage = 5)
    {
        return $query->where('usage_count', '>=', $minUsage);
    }

    /**
     * Scope per ordinamento
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Scope per ordinamento per popolarità
     */
    public function scopeOrderedByPopularity($query)
    {
        return $query->orderBy('usage_count', 'desc');
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
     * Ottieni il colore CSS
     */
    public function getColorCssAttribute(): string
    {
        return $this->color ?? '#6B7280'; // Default gray
    }

    /**
     * Ottieni l'icona HTML
     */
    public function getIconHtmlAttribute(): string
    {
        if (empty($this->icon)) {
            return '<span class="w-4 h-4 inline-block bg-gray-400 rounded"></span>';
        }
        
        // Se è un emoji
        if (mb_strlen($this->icon) === 1) {
            return '<span class="text-lg">' . $this->icon . '</span>';
        }
        
        // Se è una classe CSS (es. Heroicons)
        return '<i class="' . $this->icon . '"></i>';
    }

    /**
     * Ottieni il badge HTML completo
     */
    public function getBadgeHtmlAttribute(): string
    {
        $color = $this->color_css;
        $icon = $this->icon_html;
        $name = e($this->translated_name);
        
        return sprintf(
            '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white" style="background-color: %s;">%s %s</span>',
            $color,
            $icon,
            $name
        );
    }

    /**
     * Ottieni il tipo formattato
     */
    public function getFormattedTypeAttribute(): string
    {
        return match($this->type) {
            'material' => 'Materiale',
            'color' => 'Colore',
            'style' => 'Stile',
            'feature' => 'Caratteristica',
            'category' => 'Categoria',
            'brand' => 'Brand',
            'collection' => 'Collezione',
            'certification' => 'Certificazione',
            'installation' => 'Installazione',
            'maintenance' => 'Manutenzione',
            default => 'Generico'
        };
    }

    /**
     * Ottieni l'URL del tag
     */
    public function getUrlAttribute(): string
    {
        return route('tags.show', $this->slug);
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

    // ===================================================================
    // HELPER METHODS
    // ===================================================================

    /**
     * Verifica se il tag è attivo
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Verifica se il tag è in evidenza
     */
    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    /**
     * Verifica se il tag è popolare
     */
    public function isPopular(int $threshold = 10): bool
    {
        return $this->usage_count >= $threshold;
    }

    /**
     * Verifica se ha prodotti associati
     */
    public function hasProducts(): bool
    {
        return $this->products()->exists();
    }

    /**
     * Verifica se ha prodotti attivi
     */
    public function hasActiveProducts(): bool
    {
        return $this->activeProducts()->exists();
    }

    /**
     * Ottieni il numero di prodotti associati
     */
    public function getProductsCountAttribute(): int
    {
        return $this->products()->count();
    }

    /**
     * Ottieni il numero di prodotti attivi associati
     */
    public function getActiveProductsCountAttribute(): int
    {
        return $this->activeProducts()->count();
    }

    /**
     * Incrementa il contatore di utilizzo
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Decrementa il contatore di utilizzo
     */
    public function decrementUsage(): void
    {
        $this->decrement('usage_count');
    }

    /**
     * Ricalcola il contatore di utilizzo
     */
    public function recalculateUsage(): void
    {
        $this->usage_count = $this->products()->count();
        $this->save();
    }

    /**
     * Associa il tag a un prodotto
     */
    public function attachToProduct(Product $product): void
    {
        if (!$this->products()->where('product_id', $product->id)->exists()) {
            $this->products()->attach($product->id);
            $this->incrementUsage();
        }
    }

    /**
     * Dissocia il tag da un prodotto
     */
    public function detachFromProduct(Product $product): void
    {
        if ($this->products()->where('product_id', $product->id)->exists()) {
            $this->products()->detach($product->id);
            $this->decrementUsage();
        }
    }

    /**
     * Ottieni tag correlati (basato su prodotti comuni)
     */
    public function getRelatedTags(int $limit = 10)
    {
        return self::whereHas('products', function ($query) {
            $query->whereIn('product_id', $this->products()->pluck('product_id'));
        })
        ->where('id', '!=', $this->id)
        ->withCount('products')
        ->orderBy('products_count', 'desc')
        ->limit($limit)
        ->get();
    }

    /**
     * Ottieni tag simili (stesso tipo)
     */
    public function getSimilarTags(int $limit = 10)
    {
        return self::where('type', $this->type)
                  ->where('id', '!=', $this->id)
                  ->active()
                  ->orderedByPopularity()
                  ->limit($limit)
                  ->get();
    }

    /**
     * Genera suggerimenti per tag simili
     */
    public function getSuggestions(int $limit = 5): array
    {
        $suggestions = [];
        
        // Basato su nome simile
        $similarNames = self::where('name', 'like', '%' . $this->name . '%')
                           ->where('id', '!=', $this->id)
                           ->active()
                           ->limit($limit)
                           ->pluck('name')
                           ->toArray();
        
        $suggestions = array_merge($suggestions, $similarNames);
        
        // Basato su tipo
        $sameType = self::where('type', $this->type)
                       ->where('id', '!=', $this->id)
                       ->active()
                       ->orderedByPopularity()
                       ->limit($limit)
                       ->pluck('name')
                       ->toArray();
        
        $suggestions = array_merge($suggestions, $sameType);
        
        return array_unique(array_slice($suggestions, 0, $limit));
    }

    /**
     * Ottieni statistiche del tag
     */
    public function getStats(): array
    {
        return [
            'total_products' => $this->products_count,
            'active_products' => $this->active_products_count,
            'usage_count' => $this->usage_count,
            'is_popular' => $this->isPopular(),
            'created_at' => $this->created_at,
            'type' => $this->type,
            'color' => $this->color,
        ];
    }

    // ===================================================================
    // STATIC METHODS
    // ===================================================================

    /**
     * Cerca tag per nome
     */
    public static function search(string $query, int $limit = 10)
    {
        return self::active()
                  ->search($query)
                  ->orderedByPopularity()
                  ->limit($limit)
                  ->get();
    }

    /**
     * Ottieni tag popolari
     */
    public static function popular(int $limit = 20, int $minUsage = 5)
    {
        return self::active()
                  ->popular($minUsage)
                  ->orderedByPopularity()
                  ->limit($limit)
                  ->get();
    }

    /**
     * Ottieni tag per tipo
     */
    public static function byType(string $type)
    {
        return self::active()
                  ->byType($type)
                  ->ordered()
                  ->get();
    }

    /**
     * Ottieni tag in evidenza
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
     * Crea tag da stringa (es. "tag1, tag2, tag3")
     */
    public static function createFromString(string $tagString, string $type = 'general'): array
    {
        $tags = [];
        $tagNames = array_map('trim', explode(',', $tagString));
        
        foreach ($tagNames as $tagName) {
            if (empty($tagName)) continue;
            
            $tag = self::firstOrCreate(
                ['name' => $tagName],
                [
                    'slug' => \Str::slug($tagName),
                    'type' => $type,
                    'is_active' => true,
                    'usage_count' => 0,
                ]
            );
            
            $tags[] = $tag;
        }
        
        return $tags;
    }

    /**
     * Ottieni tag più usati per tipo
     */
    public static function topByType(string $type, int $limit = 10)
    {
        return self::active()
                  ->byType($type)
                  ->orderedByPopularity()
                  ->limit($limit)
                  ->get();
    }

    /**
     * Ottieni tutti i tipi di tag disponibili
     */
    public static function getAvailableTypes(): array
    {
        return self::select('type')
                  ->distinct()
                  ->whereNotNull('type')
                  ->pluck('type')
                  ->toArray();
    }

    /**
     * Ottieni statistiche globali
     */
    public static function getGlobalStats(): array
    {
        return [
            'total_tags' => self::count(),
            'active_tags' => self::active()->count(),
            'featured_tags' => self::featured()->count(),
            'popular_tags' => self::popular()->count(),
            'by_type' => self::selectRaw('type, COUNT(*) as count')
                            ->groupBy('type')
                            ->pluck('count', 'type')
                            ->toArray(),
            'most_used' => self::orderBy('usage_count', 'desc')
                              ->limit(10)
                              ->pluck('usage_count', 'name')
                              ->toArray(),
        ];
    }

    /**
     * Pulisci tag inutilizzati
     */
    public static function cleanupUnused(): int
    {
        $deletedCount = self::where('usage_count', 0)
                           ->whereDoesntHave('products')
                           ->delete();
        
        return $deletedCount;
    }

    /**
     * Ricalcola tutti i contatori di utilizzo
     */
    public static function recalculateAllUsageCounts(): void
    {
        self::chunk(100, function ($tags) {
            foreach ($tags as $tag) {
                $tag->recalculateUsage();
            }
        });
    }

    /**
     * Activity Log Configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'slug', 'type', 'is_active', 'is_featured', 'usage_count'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}