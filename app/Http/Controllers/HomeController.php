<?php

namespace App\Http\Controllers;

use App\Models\Pet; // إذا كنت لا تزال تستخدم موديل Pet القديم
use App\Models\StrayPet; // الموديل الجديد
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin() || $user->isDataEntry()) {
            $totalStrayPets = StrayPet::count();
            // يمكنك إضافة منطق للمهام والتنبيهات
            $upcomingTasks = 2; // Dummy data
            $healthAlerts = 1; // Dummy data
            return view('home', compact('totalStrayPets', 'upcomingTasks', 'healthAlerts'));
        } else {
            // للمستخدم العادي (Regular User) أو الضيف (Guest User)
            // قد يعرض بياناته الخاصة أو نسخة مبسطة جداً من لوحة التحكم
            $totalStrayPets = StrayPet::where('created_by', $user->id)->count(); // أو لا شيء على الإطلاق
            return view('home', compact('totalStrayPets'));
        }
    }
}