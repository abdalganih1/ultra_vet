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
        Schema::table('independent_teams', function (Blueprint $table) {
            $table->dropColumn('province');
            $table->foreignId('governorate_id')->nullable()->constrained('governorates')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('independent_teams', function (Blueprint $table) {
            $table->string('province');
            $table->dropForeign(['governorate_id']);
            $table->dropColumn('governorate_id');
        });
    }
};