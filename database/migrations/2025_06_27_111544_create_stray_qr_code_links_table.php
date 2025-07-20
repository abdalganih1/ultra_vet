<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stray_qr_code_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stray_pet_id')->constrained()->onDelete('cascade');
            $table->string('qr_identifier')->unique(); // This will be the UUID from the StrayPet table
            $table->string('qr_image_path')->nullable(); // Path to the generated QR code image
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stray_qr_code_links');
    }
};