<?php

namespace App\Models;

// Note: Illuminate\Foundation\Auth\User already extends
// Illuminate\Database\Eloquent\Model and implements Authenticatable.
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'profile_photo',
        'first_name',
        'last_name',
        'cin',
        'age',
        'username',
        'password',
        'gender',
        'is_admin',
        'language',
    ];

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
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'age' => 'integer',
        ];
    }

    /**
     * The publications published by this user.
     *
     * @return HasMany<Publication>
     */
    public function publications(): HasMany
    {
        return $this->hasMany(Publication::class);
    }

    /**
     * The full name of the user, derived from first and last name.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * The public URL of the user's profile photo, if any.
     */
    public function getProfilePhotoUrlAttribute(): ?string
    {
        if (! $this->profile_photo) {
            return null;
        }

        return asset('storage/'.$this->profile_photo);
    }
}
