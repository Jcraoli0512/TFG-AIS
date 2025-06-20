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
        Schema::table('artworks', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
            $table->string('technique')->nullable()->change();
            $table->integer('year')->nullable()->change();
            $table->string('image_path')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            $table->text('description')->nullable(false)->change();
            $table->string('technique')->nullable(false)->change();
            $table->integer('year')->nullable(false)->change();
            $table->string('image_path')->nullable(false)->change();
        });
    }
};
