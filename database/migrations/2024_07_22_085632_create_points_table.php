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
        Schema::create('points', function (Blueprint $table) {
            $table->id();
            $table->string('code', length: 10)->unique();
            $table->text('description');
            $table->double('easting');
            $table->double('northing');
            $table->boolean('pointVirtual')->default('true');   
            // dodatkowa tablica analogiczna do paths z obszarami
            // ggh todo: $table->string('obszar'); => wywalamy do oddzielnej tablicy

            // ggh todo: url do punktu, nie obowiązkowe

            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('points');
    }
};
