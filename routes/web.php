<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StrayPetController;
use App\Http\Controllers\Admin\IndependentTeamController;
use App\Http\Controllers\Admin\GovernorateController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;

// تعطيل مسار التسجيل العام، مع إبقاء مسارات تسجيل الدخول والخروج
Auth::routes(['register' => true]);

// --- الصفحات العامة ---
Route::get('language/{locale}', [PageController::class, 'switchLang'])->name('language.switch');
Route::get('/', [PageController::class, 'landing'])->name('landing');
Route::get('/about', [PageController::class, 'about'])->name('pages.about');
Route::get('/team', [PageController::class, 'team'])->name('pages.team');
Route::get('/contact', [PageController::class, 'contact'])->name('pages.contact');
Route::get('/public/stray-pets/{uuid}', [StrayPetController::class, 'showPublic'])->name('stray-pets.public-view');


// --- المسارات التي تتطلب تسجيل الدخول ---
Route::middleware(['auth'])->group(function () {
    
    // لوحة التحكم الرئيسية (تعتمد على الدور)
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // --- المسارات المشتركة بين المدير ومدخل البيانات ---
    Route::middleware(['role:admin,data_entry'])->group(function () {
        // سجل الحيوانات
        Route::get('/stray-pets', [StrayPetController::class, 'index'])->name('stray-pets.index');
        
        // إدخال وتعديل بيانات حيوان
        Route::get('/stray-pets/data-entry/{stray_pet}', [StrayPetController::class, 'edit'])->name('stray-pets.data-entry')->middleware('auth', 'pet.data_entry_access');
        Route::post('/stray-pets/save-data', [StrayPetController::class, 'storeOrUpdate'])->name('stray-pets.store-or-update');
        
        // عرض تفاصيل حيوان (للمستخدمين المسجلين)
        Route::get('/stray-pets/show/{uuid}', [StrayPetController::class, 'show'])->name('stray-pets.show');

        // حذف (إرسال إلى سلة المهملات)
        Route::delete('/stray-pets/{uuid}', [StrayPetController::class, 'destroy'])->name('stray-pets.destroy');
        Route::post('/stray-pets/bulk-destroy', [StrayPetController::class, 'bulkDestroy'])->name('stray-pets.bulk-destroy');

        // توليد وطباعة QR
        Route::get('/qrcodes/stray/generate', [StrayPetController::class, 'showQRGenerationForm'])->name('qrcodes.stray.generate.form');
        Route::post('/qrcodes/stray/generate', [StrayPetController::class, 'generateQRCodes'])->name('qrcodes.stray.generate.submit');
        Route::post('/qrcodes/stray/print-pdf', [StrayPetController::class, 'printQRCodes'])->name('qrcodes.stray.print.pdf');
    });

    // --- المسارات الخاصة بالمدير فقط ---
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('teams', IndependentTeamController::class);
        Route::resource('governorates', GovernorateController::class);
        Route::resource('users', UserController::class);
        
        // مسارات سلة المهملات
        Route::get('trash', [StrayPetController::class, 'trashIndex'])->name('trash.index');
        Route::post('trash/{id}/restore', [StrayPetController::class, 'trashRestore'])->name('trash.restore');
        Route::delete('trash/{id}/delete', [StrayPetController::class, 'trashDestroy'])->name('trash.destroy');
    });

    // --- مسارات الملف الشخصي (لكل المستخدمين المسجلين) ---
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
        // طلب ترقية الحساب إلى فريق
        Route::post('/request-team-upgrade', [ProfileController::class, 'requestTeamUpgrade'])->name('request-team-upgrade');
    });

});
