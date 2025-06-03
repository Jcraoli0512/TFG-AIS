<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo',
        'biography',
        'website',
        'instagram',
        'facebook',
        'twitter',
        'role',
        'is_active',
        'google_id',
        'panoramic_image',
        'last_active_at',
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
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_active_at' => 'datetime',
    ];

    public function artworks()
    {
        return $this->hasMany(Artwork::class);
    }

    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    public function exhibitions()
    {
        return $this->hasMany(Exhibition::class);
    }

    public function isAdmin()
    {
        Log::info('User::isAdmin', [
            'id' => $this->id,
            'email' => $this->email,
            'role' => $this->role,
            'is_active' => $this->is_active
        ]);
        return $this->role === 'admin' && $this->is_active;
    }

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            $url = '/storage/' . $this->profile_photo;
            Log::info('Generando URL de foto de perfil', [
                'profile_photo' => $this->profile_photo,
                'url' => $url
            ]);
            return $url;
        }
        return asset('img_web/default-profile.png');
    }
}
