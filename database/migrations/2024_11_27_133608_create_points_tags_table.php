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
        Schema::create('points_point_tags', function (Blueprint $table) {
            $table->id();
            
            //ggh
            $table->foreignId('point_id')->constrained('points');
            $table->foreignId('point_tag_id')->constrained('point_tags');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('points_point_tags');
    }
};
