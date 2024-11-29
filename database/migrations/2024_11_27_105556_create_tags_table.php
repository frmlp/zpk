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
        Schema::create('point_tags', function (Blueprint $table) {
            $table->id();

            //ggh
            $table->string('tag');

            $table->timestamps();
        });


        //ggh
        Schema::create('path_tags', function(Blueprint $table){
            $table->id();

            $table->string('tag');

            $table->timestamps();
        });
    }

   

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_tags');
        Schema::dropIfExists('path_tags');
    }
};
