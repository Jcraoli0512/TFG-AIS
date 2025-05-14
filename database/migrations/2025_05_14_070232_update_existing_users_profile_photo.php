<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Actualizar todos los usuarios existentes para que tengan profile_photo como null
        User::whereNotNull('profile_photo')
            ->orWhere('profile_photo', '!=', '')
            ->update(['profile_photo' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No es necesario hacer nada en el down ya que no podemos recuperar los valores anteriores
    }
};
