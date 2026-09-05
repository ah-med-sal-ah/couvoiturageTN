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
        Schema::table('publications', function (Blueprint $table) {
            // Availability switch for Driver posts only (see Publication::STATUS_DRIVER).
            // Defaulting to false means every existing row - and every newly
            // created post regardless of status - safely starts "not reserved".
            $table->boolean('reservation_enabled')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->dropColumn('reservation_enabled');
        });
    }
};
