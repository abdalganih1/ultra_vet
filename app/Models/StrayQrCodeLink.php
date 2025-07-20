<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StrayQrCodeLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'stray_pet_id', 'qr_identifier', 'qr_image_path'
    ];

    public function strayPet()
    {
        return $this->belongsTo(StrayPet::class);
    }
}