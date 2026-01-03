<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use App\Models\School;
use Illuminate\Support\Collection;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->name} {$this->surname}";
    }

    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(School::class);
    }

    // public function canAccessPanel(Panel $panel): bool
    // {
    //     if (! $this->is_active) {
    //         return false;
    //     }

    //     if ($panel->getId() === 'admin') {
    //         return $this->hasRole('super_admin');
    //     }

    //     if ($panel->getId() === 'school') {
    //         if ($this->hasRole('super_admin')) {
    //             return true;
    //         }

    //         return $this->schools()->where('is_active', true)->exists();
    //     }

    //     return false;
    // }

    // public function getTenants(Panel $panel): Collection
    // {
    //     if ($this->hasRole('super_admin')) {
    //         return School::all();
    //     }

    //     return $this->schools()->where('is_active', true)->get();
    // }

    // public function canAccessTenant(Model $tenant): bool
    // {
    //     if ($this->hasRole('super_admin')) {
    //         return true;
    //     }

    //     return $this->schools->contains($tenant);
    // }
}
