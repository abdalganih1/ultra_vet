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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('name');
            $table->string('phone_number')->nullable()->after('email');
            $table->foreignId('governorate_id')->nullable()->constrained('governorates')->after('phone_number');
            $table->foreignId('independent_team_id')->nullable()->constrained('independent_teams')->after('governorate_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['governorate_id']);
            $table->dropForeign(['independent_team_id']);
            $table->dropColumn(['username', 'phone_number', 'governorate_id', 'independent_team_id']);
        });
    }
};