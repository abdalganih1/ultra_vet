<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndependentTeam extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'governorate_id',
    ];

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function strayPets()
    {
        return $this->hasMany(StrayPet::class, 'independent_team_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'independent_team_id');
    }
}