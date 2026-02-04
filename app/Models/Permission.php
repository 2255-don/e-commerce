<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'slug',
        'name',
        'description',
    ];

    /**
     * Get all features linked to this permission
     */
    public function features()
    {
        return $this->belongsToMany(Feature::class, 'permission_feature');
    }

    /**
     * Get all roles that have this permission
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }

    /**
     * Attach multiple features to this permission
     */
    public function attachFeatures(array $featureIds)
    {
        $this->features()->syncWithoutDetaching($featureIds);
    }

    /**
     * Detach multiple features from this permission
     */
    public function detachFeatures(array $featureIds)
    {
        $this->features()->detach($featureIds);
    }
}
