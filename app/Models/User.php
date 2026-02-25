<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids, TwoFactorAuthenticatable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'profil_id',
        'kyc_status',
        'kyc_document_path',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
        ];
    }

    /**
     * Get the URL to the user's profile photo.
     *
     * @return string
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            return asset('storage/' . $this->profile_photo_path);
        }
        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=7367f0&background=f8f7ff';
    }

    public function isSeller()
    {
        return $this->user_type === 'seller';
    }

    public function cartItemsCount()
    {
        $cart = $this->cart()->first();
        return $cart ? $cart->items()->sum('quantity') : 0;
    }

    public function cart()
    {
        return $this->hasOne(\App\Models\Cart::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function sellerProfile()
    {
        return $this->hasOne(SellerProfile::class);
    }

    public function profil()
    {
        return $this->belongsTo(Profil::class, 'profil_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_details', 'user_id', 'role_id')
            ->withTimestamps();
    }

    /**
     * Check if user is Super Admin
     */
    public function isSuperAdmin()
    {
        return $this->profil && $this->profil->libelle === 'Super-Admin';
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($slug)
    {
        return $this->roles()->where('slug', $slug)->exists();
    }

    /**
     * Assign a role to the user
     */
    public function assignRole($roleSlug)
    {
        $role = Role::where('slug', $roleSlug)->first();
        if ($role && !$this->hasRole($roleSlug)) {
            $this->roles()->attach($role->id);
        }
        return $this;
    }

    /**
     * Remove a role from the user
     */
    public function removeRole($roleSlug)
    {
        $role = Role::where('slug', $roleSlug)->first();
        if ($role) {
            $this->roles()->detach($role->id);
        }
        return $this;
    }
}
