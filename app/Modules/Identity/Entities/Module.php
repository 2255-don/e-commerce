<?php

namespace Modules\Identity\Entities;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'slug',
        'name',
        'icon',
        'color',
        'order',
        'is_core',
    ];

    protected $casts = [
        'is_core' => 'boolean',
    ];

    /**
     * Get all features for this module
     */
    public function features()
    {
        return $this->hasMany(Feature::class);
    }

    /**
     * Scope to get only core modules
     */
    public function scopeCore($query)
    {
        return $query->where('is_core', true);
    }

    /**
     * Scope to order modules by their order field
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
