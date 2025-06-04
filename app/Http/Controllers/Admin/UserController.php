<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function edit(User $user)
    {
        try {
            // Cargar las imágenes del usuario
            $user->load('images');
            
            if (request()->ajax()) {
                $view = view('admin.users._edit_form', compact('user'))->render();
                return response()->json(['html' => $view]);
            }

            return view('admin.users.edit', compact('user'));
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['message' => 'Error al cargar el formulario: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
    }
} 