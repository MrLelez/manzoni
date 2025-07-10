<?php

namespace App\Traits;

use App\Models\Translation;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasTranslations
{
    /**
     * Get all translations for this model
     */
    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    /**
     * Get translation for specific locale and field
     */
    public function getTranslation(string $field, string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();
        
        $translation = $this->translations()
                          ->where('locale', $locale)
                          ->where('field', $field)
                          ->where('is_active', true)
                          ->first();
        
        return $translation ? $translation->value : $this->getAttribute($field);
    }

    /**
     * Set translation for specific locale and field
     */
    public function setTranslation(string $field, string $value, string $locale = null): void
    {
        $locale = $locale ?: app()->getLocale();
        
        $this->translations()->updateOrCreate(
            [
                'locale' => $locale,
                'field' => $field,
            ],
            [
                'value' => $value,
                'is_active' => true,
                'created_by' => auth()->id(),
            ]
        );
    }

    /**
     * Get all translations for a field
     */
    public function getTranslations(string $field): array
    {
        return $this->translations()
                   ->where('field', $field)
                   ->where('is_active', true)
                   ->pluck('value', 'locale')
                   ->toArray();
    }

    /**
     * Check if translation exists
     */
    public function hasTranslation(string $field, string $locale = null): bool
    {
        $locale = $locale ?: app()->getLocale();
        
        return $this->translations()
                   ->where('locale', $locale)
                   ->where('field', $field)
                   ->where('is_active', true)
                   ->exists();
    }

    /**
     * Get localized name (helper method)
     */
    public function getLocalizedName(string $locale = null): string
    {
        return $this->getTranslation('name', $locale) ?: $this->name;
    }

    /**
     * Get localized description (helper method)
     */
    public function getLocalizedDescription(string $locale = null): string
    {
        return $this->getTranslation('description', $locale) ?: $this->description;
    }

    /**
     * Dynamic attribute accessor for translations
     */
    public function getAttribute($key)
    {
        // Se è una richiesta per una traduzione specifica (es: name_fr, description_de)
        if (preg_match('/^(.+)_([a-z]{2}(?:-[A-Z]{2})?)$/', $key, $matches)) {
            $field = $matches[1];
            $locale = $matches[2];
            return $this->getTranslation($field, $locale);
        }

        // Se è una richiesta normale ma abbiamo traduzioni attive
        if (in_array($key, $this->translatable ?? []) && app()->getLocale() !== config('app.fallback_locale')) {
            $translation = $this->getTranslation($key);
            if ($translation && $translation !== $this->getOriginal($key)) {
                return $translation;
            }
        }

        return parent::getAttribute($key);
    }

    /**
     * Scope for searching in translations
     */
    public function scopeWithTranslation($query, string $field, string $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        
        return $query->whereHas('translations', function ($q) use ($field, $locale) {
            $q->where('field', $field)
              ->where('locale', $locale)
              ->where('is_active', true);
        });
    }

    /**
     * Scope for searching in translations with term
     */
    public function scopeSearchTranslation($query, string $field, string $term, string $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        
        return $query->whereHas('translations', function ($q) use ($field, $term, $locale) {
            $q->where('field', $field)
              ->where('locale', $locale)
              ->where('value', 'LIKE', "%{$term}%")
              ->where('is_active', true);
        });
    }

    /**
     * Boot the trait
     */
    protected static function bootHasTranslations()
    {
        // Quando il model viene eliminato, elimina anche le traduzioni
        static::deleting(function ($model) {
            $model->translations()->delete();
        });
    }
}