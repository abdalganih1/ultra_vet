<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens; // أزل التعليق إذا كنت تستخدم Sanctum

class User extends Authenticatable
{
    // use HasApiTokens, HasFactory, Notifiable; // أزل التعليق إذا كنت تستخدم Sanctum
    use HasFactory, Notifiable; // إذا لم تستخدم Sanctum

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isAdmin() { return $this->role === 'admin'; }
    public function isDataEntry() { return $this->role === 'data_entry'; }
    public function isRegularUser() { return $this->role === 'regular_user'; }
    // أضف هذه الدالة للضيف (guest_user) إذا أردت تمييزه عن المستخدم العادي المسجل
    public function isGuestUser() { return $this->role === 'guest_user'; }

    public function createdStrayPets()
    {
        return $this->hasMany(StrayPet::class, 'created_by');
    }
}