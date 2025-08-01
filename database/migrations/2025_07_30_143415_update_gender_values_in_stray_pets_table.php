<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('stray_pets')->where('gender', 'ذكر')->update(['gender' => 'male']);
        DB::table('stray_pets')->where('gender', 'أنثى')->update(['gender' => 'female']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('stray_pets')->where('gender', 'male')->update(['gender' => 'ذكر']);
        DB::table('stray_pets')->where('gender', 'female')->update(['gender' => 'أنثى']);
    }
};