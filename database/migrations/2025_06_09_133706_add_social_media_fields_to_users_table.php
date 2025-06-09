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
        Schema::table('users', function (Blueprint $table) {
            $table->string('tiktok')->nullable()->after('twitter');
            $table->string('youtube')->nullable()->after('tiktok');
            $table->string('pinterest')->nullable()->after('youtube');
            $table->string('behance')->nullable()->after('pinterest');
            $table->string('deviantart')->nullable()->after('behance');
            $table->string('artstation')->nullable()->after('deviantart');
            $table->string('linkedin')->nullable()->after('artstation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'tiktok',
                'youtube', 
                'pinterest',
                'behance',
                'deviantart',
                'artstation',
                'linkedin'
            ]);
        });
    }
};
