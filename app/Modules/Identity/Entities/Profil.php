<?php

namespace Modules\Identity\Entities;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profil extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'libelle',
    ];

    /**
     * Get all users with this profile
     */
    public function users()
    {
        return $this->hasMany(User::class, 'profil_id');
    }
}
