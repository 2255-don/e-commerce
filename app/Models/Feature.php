<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'module_id',
        'slug',
        'name',
        'type',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the module that owns the feature
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Get all permissions linked to this feature
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_feature');
    }

    /**
     * Scope to filter by feature type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter by module
     */
    public function scopeForModule($query, $moduleId)
    {
        return $query->where('module_id', $moduleId);
    }
}
