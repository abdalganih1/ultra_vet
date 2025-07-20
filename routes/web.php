<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StrayPetController;

// صفحة الهبوط الافتراضية
Route::get('/', function () {
    return view('welcome');
});

// مسارات المصادقة الجاهزة (Login, Register, Logout)
Auth::routes();

// مسار عرض الملف العام للحيوان الشارد (متاح للجميع، حتى الضيوف)
// هذا هو الرابط الذي يجب أن يكون في الـ QR Code
Route::get('/public/stray-pets/{uuid}', [StrayPetController::class, 'showPublic'])->name('stray-pets.public-view');

// جميع المسارات التي تتطلب تسجيل الدخول (Authentication)
Route::middleware(['auth'])->group(function () {
    
    // لوحة التحكم (Dashboard)
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // المسارات المخصصة للمدير ومدخل البيانات فقط
    Route::middleware(['role:admin,data_entry'])->group(function () {

        // صفحة سجل الباركودات / قائمة الحيوانات الشاردة
        Route::get('/stray-pets', [StrayPetController::class, 'index'])->name('stray-pets.index');
        Route::get('/stray-pets/log', [StrayPetController::class, 'index'])->name('stray-pets.log'); 

        // مسار عرض نموذج إدخال/تعديل البيانات
        // هذا المسار لا يمكن الوصول إليه كضيف
        Route::get('/stray-pets/data-entry/{uuid}', [StrayPetController::class, 'create'])->name('stray-pets.data-entry-form');
        
        // مسار POST لحفظ البيانات من نموذج الإدخال/التعديل
        Route::post('/stray-pets/save-data', [StrayPetController::class, 'storeOrUpdate'])->name('stray-pets.store-or-update');
        
        // مسار عرض تفاصيل حيوان (الزر في السجل)، يوجه إلى صفحة التعديل
        Route::get('/stray-pets/show/{uuid}', [StrayPetController::class, 'show'])->name('stray-pets.show');

        // مسارات حذف الحيوانات الشاردة (فردي وجماعي)
        Route::delete('/stray-pets/{uuid}', [StrayPetController::class, 'destroy'])->name('stray-pets.destroy');
        Route::delete('/stray-pets/bulk-destroy', [StrayPetController::class, 'bulkDestroy'])->name('stray-pets.bulk-destroy');
        
        // مسارات توليد أكواد QR
        Route::get('/qrcodes/stray/generate', [StrayPetController::class, 'showQRGenerationForm'])->name('qrcodes.stray.generate.form');
        Route::post('/qrcodes/stray/generate', [StrayPetController::class, 'generateQRCodes'])->name('qrcodes.stray.generate.submit');
        
        // مسار طباعة أكواد QR إلى PDF
        Route::post('/qrcodes/stray/print-pdf', [StrayPetController::class, 'printQRCodes'])->name('qrcodes.stray.print.pdf');
    });

});