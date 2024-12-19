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
            $table->foreignId('point_id')->constrained('points');
            $table->foreignId('point_tag_id')->constrained('point_tags');
            // $table->unique(['point_id', 'point_tag_id']);
            $table->timestamps();
        });
        
        /* ggh: jeżeli będzie potrzebne tagowanie tras:
            Schema::create('paths_path_tags', function (Blueprint $table) {
                $table->id();
                $table->foreignId('path_id')->constrained('paths');
                $table->foreignId('path_tag_id')->constrained('path_tags');
                $table->timestamps();
            });
        */

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('points_point_tags');
        Schema::dropIfExists('paths_path_tags');
    }
};
