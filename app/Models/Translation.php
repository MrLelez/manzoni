<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Locale extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'code',
        'name',
        'native_name',
        'flag',
        'is_active',
        'is_default',
        'sort_order',
        'date_format',
        'time_format',
        'datetime_format',
        'currency_code',
        'currency_symbol',
        'currency_position',
        'decimal_separator',
        'thousands_separator',
        'decimal_places',
        'direction',
        'charset',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
        'decimal_places' => 'integer',
    ];

    // ====================================
    // SCOPES
    // ====================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // ====================================
    // METHODS
    // ====================================

    /**
     * Format price according to locale settings
     */
    public function formatPrice(float $price): string
    {
        $formatted = number_format(
            $price,
            $this->decimal_places,
            $this->decimal_separator,
            $this->thousands_separator
        );

        if ($this->currency_position === 'before') {
            return $this->currency_symbol . $formatted;
        }

        return $formatted . $this->currency_symbol;
    }

    /**
     * Format date according to locale settings
     */
    public function formatDate(\DateTime $date): string
    {
        return $date->format($this->date_format);
    }

    /**
     * Format datetime according to locale settings
     */
    public function formatDateTime(\DateTime $date): string
    {
        return $date->format($this->datetime_format);
    }

    /**
     * Get flag URL
     */
    public function getFlagUrlAttribute(): string
    {
        return asset("flags/{$this->flag}.svg");
    }

    /**
     * Check if locale is RTL
     */
    public function isRtl(): bool
    {
        return $this->direction === 'rtl';
    }

    /**
     * Get locale for Carbon
     */
    public function getCarbonLocale(): string
    {
        return str_replace('-', '_', $this->code);
    }

    /**
     * Activity Log Configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['code', 'name', 'is_active', 'is_default'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}