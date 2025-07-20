<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stray_pets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique(); // Unique identifier for QR link
            $table->string('serial_number')->nullable(); // الرقم التسلسلي من النموذج
            $table->string('city_province')->nullable();
            $table->string('relocation_place')->nullable();
            $table->string('animal_type')->nullable(); // e.g., 'dog_baladi', 'cat_breed', 'other'
            $table->string('custom_animal_type')->nullable(); // لـ 'أخرى'
            $table->string('breed_name')->nullable();
            $table->string('gender')->nullable();
            $table->string('estimated_age')->nullable();
            $table->string('color')->nullable();
            $table->text('distinguishing_marks')->nullable();
            $table->string('image_path')->nullable(); // صورة الحيوان

            // حقول البيانات المعقدة المخزنة كـ JSON
            $table->json('medical_procedures')->nullable(); // العمليات الجراحية، علاجات أخرى
            $table->json('parasite_treatments')->nullable(); // طفيليات داخلية وخارجية
            $table->json('vaccinations_details')->nullable(); // اللقاحات المتعددة
            $table->json('medical_supervisor_info')->nullable(); // الطبيب/المؤسسة/الجمعية
            $table->string('emergency_contact_phone')->nullable();

            // حقول إضافية لتتبع من أدخل البيانات
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('last_updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stray_pets');
    }
};