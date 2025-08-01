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
            $table->string('supervising_association')->nullable()->after('uuid');
            $table->boolean('data_entered_status')->default(false)->after('supervising_association');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stray_pets', function (Blueprint $table) {
            $table->dropColumn('supervising_association');
            $table->dropColumn('data_entered_status');
            $table->dropSoftDeletes();
        });
    }
};