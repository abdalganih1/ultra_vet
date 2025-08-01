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
            $table->foreignId('independent_team_id')->nullable()->constrained('independent_teams')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stray_pets', function (Blueprint $table) {
            $table->dropForeign(['independent_team_id']);
            $table->dropColumn('independent_team_id');
        });
    }
};