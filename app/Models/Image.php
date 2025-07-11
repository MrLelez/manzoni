<?php
// app/Models/Image.php - CLEANED VERSION (Remove primary logic)

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

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
    ];

    /**
     * Relazione polimorfica con qualsiasi model
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Relazione con l'utente che ha caricato l'immagine
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Scope: Solo immagini attive
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Ordinate per sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'asc');
    }

    /**
     * Scope: Immagini completate (processing_status = completed)
     */
    public function scopeCompleted($query)
    {
        return $query->where('processing_status', 'completed');
    }

    /**
     * Scope: Per tipo di immagine
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Pubbliche
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function isBeauty(): bool
    {
        return $this->type === 'beauty';
    }

    public function scopeBeauty($query)
    {
        return $query->where('type', 'beauty');
    }

    /**
     * Genera automaticamente l'alt text se non presente
     */
    public function getAltTextAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }

        // Se non c'è alt text, genera uno basato sul clean_name e model correlato
        if ($this->imageable) {
            $modelName = class_basename($this->imageable_type);
            $identifier = $this->imageable->name ?? $this->imageable->title ?? $this->imageable->id;
            return "{$modelName}: {$identifier}";
        }

        return $this->clean_name ? str_replace('-', ' ', $this->clean_name) : 'Immagine';
    }

    /**
     * URL pulito per il frontend
     */
    public function getUrlAttribute(): string
    {
        // Se aws_url inizia con s3://, genera l'URL HTTP
        if (str_starts_with($this->aws_url, 's3://')) {
            return \Storage::disk('s3')->url($this->aws_key);
        }
        
        // Altrimenti usa il sistema di clean URLs
        return url('/img/' . $this->clean_name);
    }

    /**
     * URL ottimizzato con parametri
     */
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

    /**
     * Genera URL responsive per srcset
     */
    public function getResponsiveSrcset(): string
    {
        if (!$this->width) {
            return $this->aws_url;
        }

        $breakpoints = [320, 640, 768, 1024, 1280, 1536];
        $srcset = [];

        foreach ($breakpoints as $width) {
            if ($width <= $this->width) {
                $srcset[] = $this->getOptimizedUrl($width) . " {$width}w";
            }
        }

        $srcset[] = $this->aws_url . " {$this->width}w";
        return implode(', ', $srcset);
    }

    /**
     * Controlla se l'immagine è in elaborazione
     */
    public function isProcessing(): bool
    {
        return in_array($this->processing_status, ['pending', 'processing']);
    }

    /**
     * Controlla se l'immagine è pronta per l'uso
     */
    public function isReady(): bool
    {
        return $this->processing_status === 'completed' && $this->status === 'active';
    }

    /**
     * Ottieni la dimensione formattata
     */
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

    /**
     * Ottieni le dimensioni formattate
     */
    public function getFormattedDimensionsAttribute(): string
    {
        if ($this->width && $this->height) {
            return $this->width . '×' . $this->height;
        }
        return 'N/A';
    }

    /**
     * ✨ REMOVED: is_primary logic - ora è gestita dal Product model
     */

    /**
     * Aggiorna l'ordine delle immagini
     */
    public static function updateOrder(array $imageIds): void
    {
        foreach ($imageIds as $index => $imageId) {
            static::where('id', $imageId)->update(['sort_order' => $index + 1]);
        }
    }

    /**
     * Ottieni la prossima posizione sort_order per questo oggetto
     */
    public static function getNextSortOrder($imageableType, $imageableId): int
    {
        $maxOrder = static::where('imageable_type', $imageableType)
                          ->where('imageable_id', $imageableId)
                          ->max('sort_order');
        
        return ($maxOrder ?? 0) + 1;
    }

    /**
     * Ottieni il colore dominante dell'immagine
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
            'temp' => '#6B7280'
        ];
        
        return $colors[$this->type] ?? '#6B7280';
    }

    /**
     * Scope: Cerca immagini
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('clean_name', 'like', "%{$search}%")
              ->orWhere('original_filename', 'like', "%{$search}%")
              ->orWhere('alt_text', 'like', "%{$search}%")
              ->orWhere('caption', 'like', "%{$search}%");
        });
    }

    /**
     * ✨ NEW: Check if this image is primary for any product
     */
    public function isPrimaryForProduct($productId = null): bool
    {
        if ($productId) {
            return Product::where('id', $productId)
                         ->where('primary_image_id', $this->id)
                         ->exists();
        }
        
        // Check if it's primary for ANY product
        return Product::where('primary_image_id', $this->id)->exists();
    }

    /**
     * ✨ NEW: Check if this image is beauty for any product
     */
    public function isBeautyForProduct($productId = null): bool
    {
        if ($productId) {
            return Product::where('id', $productId)
                         ->where('beauty_image_id', $this->id)
                         ->exists();
        }
        
        return Product::where('beauty_image_id', $this->id)->exists();
    }

    /**
     * ✨ NEW: Get products where this image is primary
     */
    public function productsAsPrimary()
    {
        return Product::where('primary_image_id', $this->id);
    }

    /**
     * ✨ NEW: Get products where this image is beauty
     */
    public function productsAsBeauty()
    {
        return Product::where('beauty_image_id', $this->id);
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
                'processing_status'
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

        // ✨ NEW: Quando si elimina un'immagine, rimuovi i riferimenti primary/beauty
        static::deleting(function ($image) {
            // Rimuovi riferimenti primary_image_id
            Product::where('primary_image_id', $image->id)
                   ->update(['primary_image_id' => null]);
            
            // Rimuovi riferimenti beauty_image_id
            Product::where('beauty_image_id', $image->id)
                   ->update(['beauty_image_id' => null]);
        });
    }
}