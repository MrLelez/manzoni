<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Image extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
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
    ];

    protected $casts = [
        'tags' => 'array',
        'variants' => 'array',
        'is_public' => 'boolean',
        'processed_at' => 'datetime',
        'file_size' => 'integer',
    ];

    protected $dates = ['deleted_at'];

    // ====================================
    // RELATIONSHIPS
    // ====================================

    /**
     * Get the owning imageable model (Product, Category, etc.)
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who uploaded this image
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // ====================================
    // SCOPES
    // ====================================

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // ====================================
    // ACCESSORS & METHODS
    // ====================================

    /**
     * Get clean URL: manzoniarredourbano.it/img/cestino-roma-blue.jpg
     */
    public function getCleanUrlAttribute(): string
    {
        return url("/img/{$this->clean_name}.jpg");
    }

    /**
     * Get file extension from mime type
     */
    public function getExtensionAttribute(): string
    {
        return match($this->mime_type) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            default => 'jpg'
        };
    }

    /**
     * Get human readable file size
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get image dimensions as string
     */
    public function getDimensionsAttribute(): string
    {
        if ($this->width && $this->height) {
            return "{$this->width}x{$this->height}";
        }
        return 'Unknown';
    }

    /**
     * Check if image has variants (thumbnails, etc.)
     */
    public function hasVariants(): bool
    {
        return !empty($this->variants);
    }

    /**
     * Get specific variant URL
     */
    public function getVariantUrl(string $variant = 'thumbnail'): ?string
    {
        if (!$this->hasVariants() || !isset($this->variants[$variant])) {
            return null;
        }

        return Storage::disk('s3')->url($this->variants[$variant]['aws_key']);
    }

    /**
     * Activity Log Configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['clean_name', 'status', 'type', 'is_public'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}