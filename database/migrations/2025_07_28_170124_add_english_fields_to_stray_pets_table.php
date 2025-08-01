<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stray_pets', function (Blueprint $table) {
            $table->string('breed_name_en')->nullable()->after('breed_name');
            $table->string('color_en')->nullable()->after('color');
            $table->text('distinguishing_marks_en')->nullable()->after('distinguishing_marks');
            $table->string('city_province_en')->nullable()->after('city_province');
            $table->string('relocation_place_en')->nullable()->after('relocation_place');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stray_pets', function (Blueprint $table) {
            $table->dropColumn([
                'breed_name_en',
                'color_en',
                'distinguishing_marks_en',
                'city_province_en',
                'relocation_place_en',
            ]);
        });
    }
};