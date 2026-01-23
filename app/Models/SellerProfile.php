<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerProfile extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'shop_name',
        'licence_paid_at',
        'licence_expire_at',
        'commission_rate',
        'is_active',
    ];

    protected $casts = [
        'licence_paid_at' => 'datetime',
        'licence_expire_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the license is currently active.
     */
    public function isLicenseActive()
    {
        return $this->is_active && $this->licence_expire_at && $this->licence_expire_at->isFuture();
    }
}
