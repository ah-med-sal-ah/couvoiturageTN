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
        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['passenger', 'driver']);
            $table->foreignId('departure_location_id')->constrained('locations')->cascadeOnDelete();
            $table->foreignId('arrival_location_id')->constrained('locations')->cascadeOnDelete();
            $table->unsignedTinyInteger('available_seats');
            $table->text('remarks')->nullable();
            $table->date('departure_date');
            $table->time('departure_time');
            $table->string('phone');
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->timestamps();

            $table->index(['status', 'departure_location_id', 'arrival_location_id']);
            $table->index('departure_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publications');
    }
};
