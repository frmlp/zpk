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
        Schema::create('paths_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('path_id')->constrained('paths');
            $table->foreignId('point_id')->constrained('points');
            $table->integer('position');
            $table->timestamps();
            $table->unique(['path_id', 'point_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paths_points');
    }
};
