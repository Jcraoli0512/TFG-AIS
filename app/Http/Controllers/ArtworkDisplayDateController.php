<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\ArtworkDisplayDate;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ArtworkDisplayDateController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'artwork_id' => 'required|exists:artworks,id',
            'display_date' => 'required|date|after:today',
        ]);

        // Verificar que el usuario es el propietario de la obra
        $artwork = Artwork::findOrFail($validated['artwork_id']);
        if ($artwork->user_id !== auth()->id()) {
            return response()->json(['error' => 'No tienes permiso para modificar esta obra'], 403);
        }

        // Verificar que no hay más de 3 obras programadas para esa fecha
        $dateCount = ArtworkDisplayDate::where('display_date', $validated['display_date'])
            ->where('is_approved', true)
            ->count();

        if ($dateCount >= 3) {
            return response()->json(['error' => 'Ya hay 3 obras programadas para esta fecha'], 422);
        }

        // Crear la fecha de exhibición
        $displayDate = ArtworkDisplayDate::create([
            'artwork_id' => $validated['artwork_id'],
            'user_id' => auth()->id(),
            'display_date' => $validated['display_date'],
            'is_approved' => false // Requiere aprobación del administrador
        ]);

        return response()->json([
            'message' => 'Fecha de exhibición solicitada correctamente',
            'display_date' => $displayDate
        ]);
    }

    public function destroy(ArtworkDisplayDate $displayDate)
    {
        // Verificar que el usuario es el propietario de la obra
        if ($displayDate->user_id !== auth()->id()) {
            return response()->json(['error' => 'No tienes permiso para eliminar esta fecha'], 403);
        }

        $displayDate->delete();

        return response()->json(['message' => 'Fecha de exhibición eliminada correctamente']);
    }

    public function approve(ArtworkDisplayDate $displayDate)
    {
        // Solo los administradores pueden aprobar
        if (!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'No tienes permiso para aprobar fechas'], 403);
        }

        $displayDate->update(['is_approved' => true]);

        return response()->json(['message' => 'Fecha de exhibición aprobada correctamente']);
    }
} 