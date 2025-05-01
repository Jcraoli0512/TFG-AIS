<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_photo')->nullable()->after('password');
            $table->text('biography')->nullable()->after('profile_photo');
            $table->string('website')->nullable()->after('biography');
            $table->string('instagram')->nullable()->after('website');
            $table->string('facebook')->nullable()->after('instagram');
            $table->string('twitter')->nullable()->after('facebook');
            $table->enum('role', ['artist', 'admin'])->default('artist')->after('twitter');
            $table->boolean('is_active')->default(true)->after('role');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'profile_photo',
                'biography',
                'website',
                'instagram',
                'facebook',
                'twitter',
                'role',
                'is_active'
            ]);
        });
    }
}; 