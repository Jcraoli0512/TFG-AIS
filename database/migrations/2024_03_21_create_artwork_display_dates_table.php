<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('artwork_display_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artwork_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('display_date');
            $table->boolean('is_approved')->default(false);
            $table->timestamps();

            // Índice compuesto para búsquedas eficientes
            $table->index(['display_date', 'is_approved']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('artwork_display_dates');
    }
}; 