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
        Schema::create('map_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('map_file_id')->constrained('map_files')->onDelete('cascade');
            $table->integer('page');
            $table->float('coeff_a');
            $table->float('coeff_b');
            $table->float('coeff_c');
            $table->float('coeff_d');
            $table->float('coeff_e');
            $table->float('coeff_f');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('map_pages');
    }
};
