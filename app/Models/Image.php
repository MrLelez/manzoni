<?php
// app/Models/Image.php - Beauty con tag Marketing

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Models\Product;

class Image extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        // Core fields
        'clean_name',
        'original_filename',
        'aws_key',
        'aws_url',
        'mime_type',
        'file_size',
        'width',
        'height',
        'hash_md5',
        'type',
        'alt_text',
        'tags',
        'imageable_type',
        'imageable_id',
        'status',
        'is_public',
        'processed_at',
        'variants',
        'uploaded_by',
        
        // Gallery fields
        'sort_order',
        'caption',
        'dominant_color',
        'processing_status',
        
        // Beauty fields
        'beauty_category',
        
        // ✨ NUOVO: Tag marketing per beauty
        'is_marketing',
        'marketing_category',
        'campaign_name',
        'usage_rights',
    ];

    protected $casts = [
        'tags' => 'array',
        'variants' => 'array',
        'file_size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'is_public' => 'boolean',
        'processed_at' => 'datetime',
        'sort_order' => 'integer', 
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'is_marketing' => 'boolean', // ✨ NUOVO
    ];

    /**
     * ✨ Tipi esistenti (rimangono invariati)
     */
    public const TYPES = [
        'gallery' => 'Gallery Prodotto',
        'beauty' => 'Marketing', // ✨ Rinominato a schermo
        'product' => 'Prodotto Standard',
        'category' => 'Categoria',
        'content' => 'Contenuto',
        'temp' => 'Temporanea'
    ];

    /**
     * ✨ Categorie marketing (solo per beauty con is_marketing = true)
     */
    public const MARKETING_CATEGORIES = [
        'hero' => 'Hero Image',
        'banner' => 'Banner',
        'social' => 'Social Media',
        'brochure' => 'Brochure',
        'presentation' => 'Presentazione',
        'web' => 'Web Content',
        'press' => 'Press Kit',
        'advertising' => 'Advertising',
        'events' => 'Eventi',
        'catalog' => 'Catalogo'
    ];

    /**
     * Relazioni esistenti
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Scopes esistenti
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'asc');
    }

    public function scopeCompleted($query)
    {
        return $query->where('processing_status', 'completed');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * ✨ Beauty scopes aggiornati
     */
    public function isBeauty(): bool
    {
        return $this->type === 'beauty';
    }

    public function scopeBeauty($query)
    {
        return $query->where('type', 'beauty');
    }

    /**
     * ✨ NUOVO: Marketing scopes (beauty con tag marketing)
     */
    public function isMarketing(): bool
    {
        return $this->type === 'beauty' && $this->is_marketing === true;
    }

    public function scopeMarketing($query)
    {
        return $query->where('type', 'beauty')->where('is_marketing', true);
    }

    /**
     * ✨ NUOVO: Beauty associate a prodotti (non marketing)
     */
    public function isProductBeauty(): bool
    {
        return $this->type === 'beauty' && $this->is_marketing === false && $this->imageable_id !== null;
    }

    public function scopeProductBeauty($query)
    {
        return $query->where('type', 'beauty')
                     ->where('is_marketing', false)
                     ->whereNotNull('imageable_id');
    }

    /**
     * ✨ NUOVO: Scope per categoria marketing
     */
    public function scopeMarketingCategory($query, $category)
    {
        return $query->where('type', 'beauty')
                     ->where('is_marketing', true)
                     ->where('marketing_category', $category);
    }

    /**
     * ✨ AGGIORNATO: Scope orfane ESCLUDE beauty marketing
     */
    public function scopeOrphan($query)
    {
        return $query->where(function($q) {
            $q->whereNull('imageable_id')
              ->where(function($subQ) {
                  // Esclude beauty marketing dalle orfane
                  $subQ->where('type', '!=', 'beauty')
                       ->orWhere(function($beautyQ) {
                           $beautyQ->where('type', 'beauty')
                                   ->where('is_marketing', false);
                       });
              });
        });
    }

    /**
     * ✨ NUOVO: Toggle marketing per beauty
     */
    public function toggleMarketing(string $marketingCategory = null, string $campaignName = null): bool
    {
        if ($this->type !== 'beauty') {
            return false;
        }

        $newMarketingStatus = !$this->is_marketing;
        
        $updateData = [
            'is_marketing' => $newMarketingStatus,
            'marketing_category' => $newMarketingStatus ? $marketingCategory : null,
            'campaign_name' => $newMarketingStatus ? $campaignName : null,
            'usage_rights' => $newMarketingStatus ? $this->usage_rights : null,
        ];

        return $this->update($updateData);
    }

    /**
     * Accessors esistenti
     */
    public function getAltTextAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }

        if ($this->imageable) {
            $modelName = class_basename($this->imageable_type);
            $identifier = $this->imageable->name ?? $this->imageable->title ?? $this->imageable->id;
            return "{$modelName}: {$identifier}";
        }

        return $this->clean_name ? str_replace('-', ' ', $this->clean_name) : 'Immagine';
    }

    /**
     * ✨ AGGIORNATO: Nome tipo con logic marketing
     */
    public function getTypeNameAttribute(): string
    {
        if ($this->type === 'beauty') {
            return $this->is_marketing ? 'Marketing' : 'Beauty Shot';
        }
        
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    /**
     * ✨ Nome categoria marketing leggibile
     */
    public function getMarketingCategoryNameAttribute(): string
    {
        if (!$this->is_marketing || !$this->marketing_category) {
            return '';
        }
        
        return self::MARKETING_CATEGORIES[$this->marketing_category] ?? ucfirst($this->marketing_category);
    }

    /**
     * ✨ AGGIORNATO: Colore dominante con marketing
     */
    public function getDominantColorAttribute($value): string
    {
        if ($value) {
            return $value;
        }
        
        $colors = [
            'product' => '#3B82F6',
            'category' => '#10B981', 
            'user_avatar' => '#8B5CF6',
            'content' => '#F59E0B',
            'gallery' => '#10B981',
            'temp' => '#6B7280'
        ];

        // ✨ Beauty con colori differenti per marketing
        if ($this->type === 'beauty') {
            return $this->is_marketing ? '#F97316' : '#EC4899'; // Arancione per marketing, Rosa per beauty
        }
        
        return $colors[$this->type] ?? '#6B7280';
    }

    /**
     * ✨ AGGIORNATO: Ricerca include campagna marketing
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('clean_name', 'like', "%{$search}%")
              ->orWhere('original_filename', 'like', "%{$search}%")
              ->orWhere('alt_text', 'like', "%{$search}%")
              ->orWhere('caption', 'like', "%{$search}%")
              ->orWhere('campaign_name', 'like', "%{$search}%");
        });
    }

    // Metodi esistenti rimangono invariati...
    public function getUrlAttribute(): string
    {
        if (str_starts_with($this->aws_url, 's3://')) {
            return \Storage::disk('s3')->url($this->aws_key);
        }
        
        return url('/img/' . $this->clean_name);
    }

    public function getOptimizedUrl($width = null, $height = null, $quality = 80)
    {
        $url = $this->aws_url;
        
        if ($width || $height || $quality !== 80) {
            $params = [];
            if ($width) $params['w'] = $width;
            if ($height) $params['h'] = $height;
            if ($quality !== 80) $params['q'] = $quality;
            
            $url .= '?' . http_build_query($params);
        }
        
        return $url;
    }

    public function getFormattedSizeAttribute(): string
    {
        if ($this->file_size < 1024) {
            return $this->file_size . ' B';
        } elseif ($this->file_size < 1048576) {
            return round($this->file_size / 1024, 1) . ' KB';
        } else {
            return round($this->file_size / 1048576, 1) . ' MB';
        }
    }

    public function getFormattedDimensionsAttribute(): string
    {
        if ($this->width && $this->height) {
            return $this->width . '×' . $this->height;
        }
        return 'N/A';
    }

    /**
     * Activity Log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'clean_name',
                'alt_text',
                'caption',
                'sort_order',
                'status',
                'processing_status',
                'type',
                'beauty_category',
                'is_marketing', // ✨ NUOVO
                'marketing_category',
                'campaign_name'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Boot method per eventi automatici
     */
    protected static function boot()
    {
        parent::boot();

        // Quando si crea una nuova immagine, assegna automaticamente sort_order
        static::creating(function ($image) {
            if (!$image->sort_order) {
                $image->sort_order = static::getNextSortOrder(
                    $image->imageable_type,
                    $image->imageable_id
                );
            }
        });

        // Quando si elimina un'immagine, rimuovi i riferimenti primary/beauty
        static::deleting(function ($image) {
            Product::where('primary_image_id', $image->id)
                   ->update(['primary_image_id' => null]);
            
            Product::where('beauty_image_id', $image->id)
                   ->update(['beauty_image_id' => null]);
        });
    }

    /**
     * Metodi utili esistenti
     */
    public static function getNextSortOrder($imageableType, $imageableId): int
    {
        $maxOrder = static::where('imageable_type', $imageableType)
                          ->where('imageable_id', $imageableId)
                          ->max('sort_order');
        
        return ($maxOrder ?? 0) + 1;
    }

    public function isPrimaryForProduct($productId = null): bool
    {
        if ($productId) {
            return Product::where('id', $productId)
                         ->where('primary_image_id', $this->id)
                         ->exists();
        }
        
        return Product::where('primary_image_id', $this->id)->exists();
    }

    public function isBeautyForProduct($productId = null): bool
    {
        if ($productId) {
            return Product::where('id', $productId)
                         ->where('beauty_image_id', $this->id)
                         ->exists();
        }
        
        return Product::where('beauty_image_id', $this->id)->exists();
    }

    public function productsAsPrimary()
    {
        return Product::where('primary_image_id', $this->id);
    }

    public function productsAsBeauty()
    {
        return Product::where('beauty_image_id', $this->id);
    }

    public function getIsPrimaryAttribute(): bool
    {
        if (isset($this->attributes['is_primary'])) {
            return (bool) $this->attributes['is_primary'];
        }
        
        return $this->isPrimaryForProduct();
    }
}