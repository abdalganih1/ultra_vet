<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str; // لاستخدام UUID

class StrayPet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid', 'serial_number', 'city_province', 'relocation_place',
        'animal_type', 'animal_type_en', 'custom_animal_type', 'breed_name', 'gender',
        'estimated_age', 'color', 'distinguishing_marks', 'image_path',
        'medical_procedures', 'parasite_treatments', 'vaccinations_details',
        'medical_supervisor_info', 'emergency_contact_phone',
        'created_by', 'last_updated_by', 'independent_team_id',
        'data_entered_status',
        // English fields
        'breed_name_en', 'color_en', 'distinguishing_marks_en',
        'city_province_en', 'relocation_place_en',
    ];

    protected $casts = [
        'medical_procedures' => 'array',
        'parasite_treatments' => 'array',
        'vaccinations_details' => 'array',
        'medical_supervisor_info' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($strayPet) {
            if (empty($strayPet->uuid)) {
                $strayPet->uuid = (string) Str::uuid();
            }
            // تعيين من أنشأ السجل تلقائياً إذا كان المستخدم مسجلاً
            if (auth()->check()) {
                $strayPet->created_by = auth()->id();
                $strayPet->last_updated_by = auth()->id();
            }
        });

        static::updating(function ($strayPet) {
            // تعيين من قام بالتحديث تلقائياً
            if (auth()->check()) {
                $strayPet->last_updated_by = auth()->id();
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

    public function qrCodeLink()
    {
        return $this->hasOne(StrayQrCodeLink::class);
    }

    public function independentTeam()
    {
        return $this->belongsTo(IndependentTeam::class, 'independent_team_id');
    }
}