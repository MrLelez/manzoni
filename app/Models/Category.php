<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

// Import dei Models usati nelle relazioni
use App\Models\Product;
use App\Models\Translation;
use App\Models\Image;

class Category extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'sort_order',
        'is_active',
        'is_featured',
        'icon',
        'color',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'banner_text',
        'banner_color',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
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
    // RELAZIONI GERARCHICHE
    // ===================================================================

    /**
     * Categoria genitore
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Categorie figlie dirette
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')
                    ->orderBy('sort_order')
                    ->orderBy('name');
    }

    /**
     * Categorie figlie attive
     */
    public function activeChildren(): HasMany
    {
        return $this->children()->where('is_active', true);
    }

    /**
     * Tutti i discendenti (ricorsivo)
     */
    public function descendants(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->with('descendants');
    }

    /**
     * Tutti gli antenati (ricorsivo)
     */
    public function ancestors(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id')->with('ancestors');
    }

    // ===================================================================
    // RELAZIONI PRODOTTI E MEDIA
    // ===================================================================

    /**
     * Prodotti di questa categoria
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Prodotti attivi di questa categoria
     */
    public function activeProducts(): HasMany
    {
        return $this->products()->where('is_active', true);
    }

    /**
     * Immagini collegate alla categoria
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable')
                    ->where('type', 'category')
                    ->where('status', 'active');
    }

    /**
     * Immagine principale della categoria
     */
    public function mainImage(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')
                    ->where('type', 'category')
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
     * Scope per categorie attive
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope per categorie in evidenza
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope per categorie root (senza genitore)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope per categorie con prodotti
     */
    public function scopeWithProducts($query)
    {
        return $query->has('products');
    }

    /**
     * Scope per ricerca per nome
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
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
     * Ottieni il percorso completo della categoria (breadcrumb)
     */
    public function getBreadcrumbAttribute(): array
    {
        $breadcrumb = [];
        $category = $this;
        
        while ($category) {
            array_unshift($breadcrumb, $category);
            $category = $category->parent;
        }
        
        return $breadcrumb;
    }

    /**
     * Ottieni il percorso come stringa
     */
    public function getBreadcrumbStringAttribute(): string
    {
        return implode(' > ', array_map(fn($cat) => $cat->name, $this->breadcrumb));
    }

    /**
     * Ottieni il livello nella gerarchia (0 = root)
     */
    public function getLevelAttribute(): int
    {
        return count($this->breadcrumb) - 1;
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
            ->where('locale', $locale)
            ->first();

        return $translation?->value ?? null;
    }

    // ===================================================================
    // HELPER METHODS
    // ===================================================================

    /**
     * Verifica se è una categoria root
     */
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Verifica se ha categorie figlie
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Verifica se ha prodotti
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
     * Conta tutti i prodotti (inclusi quelli delle sottocategorie)
     */
    public function getTotalProductsCountAttribute(): int
    {
        $count = $this->products()->count();
        
        foreach ($this->children as $child) {
            $count += $child->total_products_count;
        }
        
        return $count;
    }

    /**
     * Conta tutti i prodotti attivi (inclusi quelli delle sottocategorie)
     */
    public function getTotalActiveProductsCountAttribute(): int
    {
        $count = $this->activeProducts()->count();
        
        foreach ($this->children as $child) {
            $count += $child->total_active_products_count;
        }
        
        return $count;
    }

    /**
     * Ottieni tutti i prodotti inclusi quelli delle sottocategorie
     */
    public function getAllProducts()
    {
        $products = $this->products();
        
        foreach ($this->descendants as $descendant) {
            $products = $products->union($descendant->products());
        }
        
        return $products;
    }

    /**
     * Ottieni tutti i prodotti attivi inclusi quelli delle sottocategorie
     */
    public function getAllActiveProducts()
    {
        return $this->getAllProducts()->where('is_active', true);
    }

    /**
     * Verifica se può essere genitore di un'altra categoria
     */
    public function canBeParentOf(Category $category): bool
    {
        // Non può essere genitore di se stessa
        if ($this->id === $category->id) {
            return false;
        }
        
        // Non può essere genitore di un suo antenato (evita loop)
        $ancestors = $this->breadcrumb;
        foreach ($ancestors as $ancestor) {
            if ($ancestor->id === $category->id) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Sposta la categoria sotto un nuovo genitore
     */
    public function moveToParent(Category $newParent = null): bool
    {
        if ($newParent && !$newParent->canBeParentOf($this)) {
            return false;
        }
        
        $this->parent_id = $newParent?->id;
        return $this->save();
    }

    /**
     * Ottieni le categorie sorelle (stesso genitore)
     */
    public function getSiblings()
    {
        return self::where('parent_id', $this->parent_id)
                   ->where('id', '!=', $this->id)
                   ->ordered()
                   ->get();
    }

    /**
     * Ottieni la categoria precedente (stesso livello)
     */
    public function getPrevious(): ?Category
    {
        return self::where('parent_id', $this->parent_id)
                   ->where('sort_order', '<', $this->sort_order)
                   ->orderBy('sort_order', 'desc')
                   ->first();
    }

    /**
     * Ottieni la categoria successiva (stesso livello)
     */
    public function getNext(): ?Category
    {
        return self::where('parent_id', $this->parent_id)
                   ->where('sort_order', '>', $this->sort_order)
                   ->orderBy('sort_order', 'asc')
                   ->first();
    }

    /**
     * Riordina le categorie figlie
     */
    public function reorderChildren(array $sortOrder): void
    {
        foreach ($sortOrder as $order => $categoryId) {
            $this->children()->where('id', $categoryId)->update(['sort_order' => $order]);
        }
    }

    /**
     * Genera un albero HTML delle categorie
     */
    public function toHtmlTree(int $maxDepth = null, int $currentDepth = 0): string
    {
        if ($maxDepth && $currentDepth >= $maxDepth) {
            return '';
        }
        
        $html = '<li>';
        $html .= '<a href="' . route('categories.show', $this->slug) . '">';
        $html .= e($this->name);
        $html .= '</a>';
        
        if ($this->hasChildren()) {
            $html .= '<ul>';
            foreach ($this->activeChildren as $child) {
                $html .= $child->toHtmlTree($maxDepth, $currentDepth + 1);
            }
            $html .= '</ul>';
        }
        
        $html .= '</li>';
        
        return $html;
    }

    /**
     * Genera un array ricorsivo per JSON
     */
    public function toTreeArray(int $maxDepth = null, int $currentDepth = 0): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'level' => $this->level,
            'products_count' => $this->total_active_products_count,
            'has_children' => $this->hasChildren(),
        ];
        
        if ($this->hasChildren() && (!$maxDepth || $currentDepth < $maxDepth)) {
            $data['children'] = $this->activeChildren->map(function ($child) use ($maxDepth, $currentDepth) {
                return $child->toTreeArray($maxDepth, $currentDepth + 1);
            })->toArray();
        }
        
        return $data;
    }

    /**
     * Ottieni statistiche della categoria
     */
    public function getStats(): array
    {
        return [
            'total_products' => $this->total_products_count,
            'active_products' => $this->total_active_products_count,
            'direct_products' => $this->products()->count(),
            'children_count' => $this->children()->count(),
            'level' => $this->level,
            'has_image' => !is_null($this->mainImage),
        ];
    }

    // ===================================================================
    // STATIC METHODS
    // ===================================================================

    /**
     * Ottieni l'albero completo delle categorie
     */
    public static function getTree(int $maxDepth = null): \Illuminate\Support\Collection
    {
        return self::with('children')
                   ->root()
                   ->active()
                   ->ordered()
                   ->get()
                   ->map(function ($category) use ($maxDepth) {
                       return $category->toTreeArray($maxDepth);
                   });
    }

    /**
     * Ottieni l'albero HTML completo
     */
    public static function getHtmlTree(int $maxDepth = null): string
    {
        $html = '<ul class="category-tree">';
        
        $rootCategories = self::root()->active()->ordered()->get();
        
        foreach ($rootCategories as $category) {
            $html .= $category->toHtmlTree($maxDepth);
        }
        
        $html .= '</ul>';
        
        return $html;
    }

    /**
     * Ottieni categorie per breadcrumb
     */
    public static function getBreadcrumb(string $slug): array
    {
        $category = self::where('slug', $slug)->first();
        
        if (!$category) {
            return [];
        }
        
        return $category->breadcrumb;
    }

    /**
     * Ricerca nelle categorie
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
     * Activity Log Configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'slug', 'parent_id', 'is_active', 'is_featured', 'sort_order'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}