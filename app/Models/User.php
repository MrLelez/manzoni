<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_name',
        'level', // Per rivenditori (1-5)
        'phone',
        'address',
        'vat_number',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'level' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get discount percentage based on level (for rivenditori)
     */
    public function getDiscountAttribute(): int
    {
        if (!$this->hasRole('rivenditore')) {
            return 0;
        }

        // Mapping livelli -> sconto percentuale
        $discounts = [
            1 => 5,   // 5% sconto
            2 => 10,  // 10% sconto
            3 => 15,  // 15% sconto
            4 => 20,  // 20% sconto
            5 => 25,  // 25% sconto (top level)
        ];

        return $discounts[$this->level] ?? 0;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is rivenditore
     */
    public function isRivenditore(): bool
    {
        return $this->hasRole('rivenditore');
    }

    /**
     * Check if user is agente
     */
    public function isAgente(): bool
    {
        return $this->hasRole('agente');
    }

    /**
     * Get level name for rivenditori
     */
    public function getLevelNameAttribute(): string
    {
        if (!$this->hasRole('rivenditore')) {
            return '';
        }

        $levelNames = [
            1 => 'Nuovo',
            2 => 'Consolidato',
            3 => 'Fedele',
            4 => 'Premium',
            5 => 'Top Partner'
        ];

        return $levelNames[$this->level] ?? '';
    }

    /**
     * Activity log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'company_name', 'level', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}