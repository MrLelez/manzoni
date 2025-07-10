<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

// Import dei Models usati nelle relazioni
use App\Models\Locale;
use App\Models\User;

class Translation extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'locale_id',
        'locale_code',
        'translatable_type',
        'translatable_id',
        'field',
        'value',
        'is_active',
        'is_published',
        'is_auto_translated',
        'translation_status',
        'translated_by',
        'reviewed_by',
        'reviewed_at',
        'version',
        'notes',
        'context',
        'meta_data',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_published' => 'boolean',
        'is_auto_translated' => 'boolean',
        'reviewed_at' => 'datetime',
        'version' => 'integer',
        'meta_data' => 'json',
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
     * Locale di riferimento
     */
    public function locale(): BelongsTo
    {
        return $this->belongsTo(Locale::class);
    }

    /**
     * Modello traducibile (polymorphic)
     */
    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Utente che ha tradotto
     */
    public function translator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'translated_by');
    }

    /**
     * Utente che ha revisionato
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ===================================================================
    // SCOPE QUERIES
    // ===================================================================

    /**
     * Scope per traduzioni attive
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope per traduzioni pubblicate
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope per locale specifico
     */
    public function scopeForLocale($query, string $localeCode)
    {
        return $query->where('locale_code', $localeCode);
    }

    /**
     * Scope per campo specifico
     */
    public function scopeForField($query, string $field)
    {
        return $query->where('field', $field);
    }

    /**
     * Scope per modello specifico
     */
    public function scopeForModel($query, string $modelType, int $modelId)
    {
        return $query->where('translatable_type', $modelType)
                    ->where('translatable_id', $modelId);
    }

    /**
     * Scope per stato traduzione
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('translation_status', $status);
    }

    /**
     * Scope per traduzioni da rivedere
     */
    public function scopePendingReview($query)
    {
        return $query->where('translation_status', 'pending_review');
    }

    /**
     * Scope per traduzioni automatiche
     */
    public function scopeAutoTranslated($query)
    {
        return $query->where('is_auto_translated', true);
    }

    /**
     * Scope per traduzioni manuali
     */
    public function scopeManualTranslated($query)
    {
        return $query->where('is_auto_translated', false);
    }

    /**
     * Scope per ricerca nel testo
     */
    public function scopeSearchValue($query, string $search)
    {
        return $query->where('value', 'like', "%{$search}%");
    }

    /**
     * Scope per versione specifica
     */
    public function scopeVersion($query, int $version)
    {
        return $query->where('version', $version);
    }

    /**
     * Scope per ultima versione
     */
    public function scopeLatestVersion($query)
    {
        return $query->orderBy('version', 'desc');
    }

    // ===================================================================
    // ACCESSORS & MUTATORS
    // ===================================================================

    /**
     * Ottieni il valore formattato
     */
    public function getFormattedValueAttribute(): string
    {
        if (empty($this->value)) {
            return '';
        }

        // Se è JSON, formatta
        if ($this->isJson()) {
            return json_encode(json_decode($this->value), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        return $this->value;
    }

    /**
     * Ottieni lo stato della traduzione con colore
     */
    public function getStatusBadgeAttribute(): array
    {
        return match($this->translation_status) {
            'draft' => ['text' => 'Bozza', 'color' => 'gray'],
            'pending_review' => ['text' => 'In revisione', 'color' => 'yellow'],
            'reviewed' => ['text' => 'Revisionato', 'color' => 'green'],
            'published' => ['text' => 'Pubblicato', 'color' => 'blue'],
            'outdated' => ['text' => 'Obsoleto', 'color' => 'red'],
            default => ['text' => 'Sconosciuto', 'color' => 'gray']
        };
    }

    /**
     * Ottieni la percentuale di completamento
     */
    public function getCompletenessPercentageAttribute(): float
    {
        if (empty($this->value)) {
            return 0;
        }

        // Calcola lunghezza rispetto al testo originale
        $originalModel = $this->translatable;
        if (!$originalModel) {
            return 100;
        }

        $originalValue = $originalModel->getAttribute($this->field);
        if (empty($originalValue)) {
            return 100;
        }

        $originalLength = strlen($originalValue);
        $translatedLength = strlen($this->value);

        // Calcola percentuale basata sulla lunghezza
        $percentage = ($translatedLength / $originalLength) * 100;
        return min(100, $percentage);
    }

    /**
     * Ottieni il numero di parole
     */
    public function getWordCountAttribute(): int
    {
        if (empty($this->value)) {
            return 0;
        }

        return str_word_count(strip_tags($this->value));
    }

    /**
     * Ottieni il numero di caratteri
     */
    public function getCharacterCountAttribute(): int
    {
        if (empty($this->value)) {
            return 0;
        }

        return mb_strlen(strip_tags($this->value));
    }

    /**
     * Ottieni il nome del locale
     */
    public function getLocaleNameAttribute(): string
    {
        return $this->locale->name ?? $this->locale_code;
    }

    /**
     * Ottieni la bandiera del locale
     */
    public function getLocaleFlagAttribute(): string
    {
        return $this->locale->flag ?? '🏳️';
    }

    /**
     * Ottieni il contesto formattato
     */
    public function getFormattedContextAttribute(): string
    {
        if (empty($this->context)) {
            return '';
        }

        return ucfirst(str_replace('_', ' ', $this->context));
    }

    /**
     * Ottieni i metadati specifici
     */
    public function getMetaValue(string $key): mixed
    {
        return $this->meta_data[$key] ?? null;
    }

    /**
     * Imposta i metadati specifici
     */
    public function setMetaValue(string $key, mixed $value): void
    {
        $metadata = $this->meta_data ?? [];
        $metadata[$key] = $value;
        $this->meta_data = $metadata;
    }

    // ===================================================================
    // HELPER METHODS
    // ===================================================================

    /**
     * Verifica se la traduzione è attiva
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Verifica se la traduzione è pubblicata
     */
    public function isPublished(): bool
    {
        return $this->is_published;
    }

    /**
     * Verifica se è una traduzione automatica
     */
    public function isAutoTranslated(): bool
    {
        return $this->is_auto_translated;
    }

    /**
     * Verifica se è una traduzione manuale
     */
    public function isManualTranslated(): bool
    {
        return !$this->is_auto_translated;
    }

    /**
     * Verifica se è stata revisionata
     */
    public function isReviewed(): bool
    {
        return $this->translation_status === 'reviewed' || $this->translation_status === 'published';
    }

    /**
     * Verifica se è in attesa di revisione
     */
    public function isPendingReview(): bool
    {
        return $this->translation_status === 'pending_review';
    }

    /**
     * Verifica se è obsoleta
     */
    public function isOutdated(): bool
    {
        return $this->translation_status === 'outdated';
    }

    /**
     * Verifica se è una bozza
     */
    public function isDraft(): bool
    {
        return $this->translation_status === 'draft';
    }

    /**
     * Verifica se il valore è JSON
     */
    public function isJson(): bool
    {
        if (empty($this->value)) {
            return false;
        }

        json_decode($this->value);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Verifica se il valore è HTML
     */
    public function isHtml(): bool
    {
        if (empty($this->value)) {
            return false;
        }

        return $this->value !== strip_tags($this->value);
    }

    /**
     * Verifica se è completa
     */
    public function isComplete(): bool
    {
        return !empty($this->value) && $this->completeness_percentage >= 80;
    }

    /**
     * Verifica se ha bisogno di revisione
     */
    public function needsReview(): bool
    {
        return $this->is_auto_translated || $this->isPendingReview();
    }

    /**
     * Pubblica la traduzione
     */
    public function publish(): bool
    {
        $this->is_published = true;
        $this->translation_status = 'published';
        
        return $this->save();
    }

    /**
     * Revoca la pubblicazione
     */
    public function unpublish(): bool
    {
        $this->is_published = false;
        $this->translation_status = 'draft';
        
        return $this->save();
    }

    /**
     * Marca come revisionata
     */
    public function markAsReviewed(int $reviewerId = null): bool
    {
        $this->translation_status = 'reviewed';
        $this->reviewed_by = $reviewerId ?? auth()->id();
        $this->reviewed_at = now();
        
        return $this->save();
    }

    /**
     * Marca come obsoleta
     */
    public function markAsOutdated(): bool
    {
        $this->translation_status = 'outdated';
        $this->is_published = false;
        
        return $this->save();
    }

    /**
     * Crea una nuova versione
     */
    public function createNewVersion(string $newValue, int $translatorId = null): self
    {
        $newTranslation = $this->replicate();
        $newTranslation->value = $newValue;
        $newTranslation->version = $this->version + 1;
        $newTranslation->translation_status = 'draft';
        $newTranslation->is_published = false;
        $newTranslation->translated_by = $translatorId ?? auth()->id();
        $newTranslation->reviewed_by = null;
        $newTranslation->reviewed_at = null;
        $newTranslation->save();

        // Marca la versione precedente come obsoleta
        $this->markAsOutdated();

        return $newTranslation;
    }

    /**
     * Ottieni le versioni precedenti
     */
    public function getPreviousVersions()
    {
        return self::where('translatable_type', $this->translatable_type)
                  ->where('translatable_id', $this->translatable_id)
                  ->where('field', $this->field)
                  ->where('locale_code', $this->locale_code)
                  ->where('version', '<', $this->version)
                  ->orderBy('version', 'desc')
                  ->get();
    }

    /**
     * Ottieni la versione successiva
     */
    public function getNextVersion(): ?self
    {
        return self::where('translatable_type', $this->translatable_type)
                  ->where('translatable_id', $this->translatable_id)
                  ->where('field', $this->field)
                  ->where('locale_code', $this->locale_code)
                  ->where('version', '>', $this->version)
                  ->orderBy('version', 'asc')
                  ->first();
    }

    /**
     * Ottieni la versione più recente
     */
    public function getLatestVersion(): ?self
    {
        return self::where('translatable_type', $this->translatable_type)
                  ->where('translatable_id', $this->translatable_id)
                  ->where('field', $this->field)
                  ->where('locale_code', $this->locale_code)
                  ->orderBy('version', 'desc')
                  ->first();
    }

    /**
     * Confronta con un'altra traduzione
     */
    public function diffWith(Translation $other): array
    {
        return [
            'added' => array_diff(explode(' ', $this->value), explode(' ', $other->value)),
            'removed' => array_diff(explode(' ', $other->value), explode(' ', $this->value)),
            'word_count_diff' => $this->word_count - $other->word_count,
            'character_count_diff' => $this->character_count - $other->character_count,
        ];
    }

    /**
     * Calcola la similarità con un'altra traduzione
     */
    public function calculateSimilarity(Translation $other): float
    {
        if (empty($this->value) || empty($other->value)) {
            return 0;
        }

        // Usa la formula di Jaro-Winkler o simile
        similar_text($this->value, $other->value, $percent);
        return $percent;
    }

    /**
     * Ottieni statistiche di traduzione
     */
    public function getTranslationStats(): array
    {
        return [
            'word_count' => $this->word_count,
            'character_count' => $this->character_count,
            'completeness' => $this->completeness_percentage,
            'status' => $this->translation_status,
            'is_auto_translated' => $this->is_auto_translated,
            'is_reviewed' => $this->isReviewed(),
            'version' => $this->version,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    // ===================================================================
    // STATIC METHODS
    // ===================================================================

    /**
     * Ottieni traduzione per modello e campo
     */
    public static function getTranslation(string $modelType, int $modelId, string $field, string $locale = null): ?self
    {
        $locale = $locale ?? app()->getLocale();
        
        return self::where('translatable_type', $modelType)
                  ->where('translatable_id', $modelId)
                  ->where('field', $field)
                  ->where('locale_code', $locale)
                  ->active()
                  ->published()
                  ->latestVersion()
                  ->first();
    }

    /**
     * Ottieni tutte le traduzioni per un modello
     */
    public static function getTranslationsForModel(string $modelType, int $modelId, string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        
        return self::where('translatable_type', $modelType)
                  ->where('translatable_id', $modelId)
                  ->where('locale_code', $locale)
                  ->active()
                  ->published()
                  ->latestVersion()
                  ->get()
                  ->keyBy('field');
    }

    /**
     * Crea o aggiorna una traduzione
     */
    public static function createOrUpdate(
        string $modelType,
        int $modelId,
        string $field,
        string $value,
        string $locale = null,
        int $translatorId = null
    ): self {
        $locale = $locale ?? app()->getLocale();
        
        $translation = self::where('translatable_type', $modelType)
                          ->where('translatable_id', $modelId)
                          ->where('field', $field)
                          ->where('locale_code', $locale)
                          ->latestVersion()
                          ->first();

        if ($translation) {
            // Se esiste, crea una nuova versione
            return $translation->createNewVersion($value, $translatorId);
        } else {
            // Se non esiste, crea una nuova traduzione
            return self::create([
                'translatable_type' => $modelType,
                'translatable_id' => $modelId,
                'field' => $field,
                'value' => $value,
                'locale_code' => $locale,
                'translated_by' => $translatorId ?? auth()->id(),
                'version' => 1,
                'translation_status' => 'draft',
                'is_active' => true,
                'is_published' => false,
                'is_auto_translated' => false,
            ]);
        }
    }

    /**
     * Ottieni statistiche globali di traduzione
     */
    public static function getGlobalStats(): array
    {
        return [
            'total_translations' => self::count(),
            'published_translations' => self::published()->count(),
            'pending_review' => self::pendingReview()->count(),
            'auto_translated' => self::autoTranslated()->count(),
            'manual_translated' => self::manualTranslated()->count(),
            'by_locale' => self::selectRaw('locale_code, COUNT(*) as count')
                              ->groupBy('locale_code')
                              ->pluck('count', 'locale_code')
                              ->toArray(),
            'by_status' => self::selectRaw('translation_status, COUNT(*) as count')
                              ->groupBy('translation_status')
                              ->pluck('count', 'translation_status')
                              ->toArray(),
        ];
    }

    /**
     * Ottieni traduzioni mancanti per un locale
     */
    public static function getMissingTranslations(string $locale, string $modelType = null): array
    {
        $query = self::select('translatable_type', 'translatable_id', 'field')
                    ->distinct();

        if ($modelType) {
            $query->where('translatable_type', $modelType);
        }

        $existingTranslations = $query->get();
        
        $missingTranslations = [];
        
        foreach ($existingTranslations as $translation) {
            $exists = self::where('translatable_type', $translation->translatable_type)
                         ->where('translatable_id', $translation->translatable_id)
                         ->where('field', $translation->field)
                         ->where('locale_code', $locale)
                         ->exists();
            
            if (!$exists) {
                $missingTranslations[] = [
                    'model_type' => $translation->translatable_type,
                    'model_id' => $translation->translatable_id,
                    'field' => $translation->field,
                    'locale' => $locale,
                ];
            }
        }
        
        return $missingTranslations;
    }

    /**
     * Activity Log Configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['field', 'value', 'locale_code', 'translation_status', 'is_published'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}