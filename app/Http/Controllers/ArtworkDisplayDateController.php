<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\ArtworkDisplayDate;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ArtworkDisplayDateController extends Controller
{
    public function store(Request $request)
    {
        Log::info('ArtworkDisplayDateController@store: Inicio', ['requestData' => $request->all()]);

        try {
            $validated = $request->validate([
                'artwork_ids' => 'required|array',
                'artwork_ids.*' => 'exists:artworks,id',
                'display_date' => 'required|date|after:today',
            ]);
            Log::info('ArtworkDisplayDateController@store: Validación exitosa', ['validatedData' => $validated]);

            $displayDate = $validated['display_date'];
            $artworkIds = $validated['artwork_ids'];
            $userId = Auth::id();
            $createdDates = [];

            // Verificar que el usuario es el propietario de todas las obras seleccionadas
            $artworks = Artwork::whereIn('id', $artworkIds)->get();
            if ($artworks->count() !== count($artworkIds) || $artworks->where('user_id', '!==', $userId)->count() > 0) {
                Log::warning('ArtworkDisplayDateController@store: Intento de seleccionar obra(s) no propias', ['userId' => $userId, 'artworkIds' => $artworkIds]);
                return response()->json(['error' => 'No tienes permiso para seleccionar una o varias de estas obras'], 403);
            }
            Log::info('ArtworkDisplayDateController@store: Verificación de propiedad exitosa');

            // Verificar que no se exceda el límite de 3 obras por fecha
            $existingCount = ArtworkDisplayDate::where('display_date', $displayDate)
                ->where('is_approved', true)
                ->count();

            if ($existingCount + count($artworkIds) > 3) {
                Log::warning('ArtworkDisplayDateController@store: Límite de obras excedido para la fecha', ['displayDate' => $displayDate, 'existingCount' => $existingCount, 'newCount' => count($artworkIds)]);
                return response()->json(['error' => 'No se pueden programar más de 3 obras (incluyendo las ya existentes) para esta fecha'], 422);
            }
            Log::info('ArtworkDisplayDateController@store: Verificación de límite de obras exitosa');

            // Crear las fechas de exhibición para cada obra seleccionada
            foreach ($artworkIds as $artworkId) {
                $displayDateRecord = ArtworkDisplayDate::create([
                    'artwork_id' => $artworkId,
                    'user_id' => $userId,
                    'display_date' => $validated['display_date'],
                    'is_approved' => false // Requiere aprobación del administrador
                ]);
                $createdDates[] = $displayDateRecord;
                Log::info('ArtworkDisplayDateController@store: Fecha de exhibición creada', ['displayDateId' => $displayDateRecord->id, 'artworkId' => $artworkId, 'date' => $validated['display_date']]);
            }

            Log::info('ArtworkDisplayDateController@store: Proceso completado exitosamente');
            return response()->json([
                'message' => 'Fechas de exhibición solicitadas correctamente',
                'display_dates' => $createdDates
            ]);

        } catch (ValidationException $e) {
            Log::error('ArtworkDisplayDateController@store: Error de validación', ['errors' => $e->errors()]);
            // Devolver los errores de validación específicos al frontend
            return response()->json(['error' => 'Error de validación', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('ArtworkDisplayDateController@store: Error inesperado', ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
            return response()->json(['error' => 'Ha ocurrido un error inesperado al procesar tu solicitud'], 500);
        }
    }

    public function destroy(ArtworkDisplayDate $displayDate)
    {
        // Verificar que el usuario es el propietario de la obra
        if ($displayDate->user_id !== Auth::id()) {
            return response()->json(['error' => 'No tienes permiso para eliminar esta fecha'], 403);
        }

        $displayDate->delete();

        return response()->json(['message' => 'Fecha de exhibición eliminada correctamente']);
    }

    public function approve(ArtworkDisplayDate $displayDate)
    {
        // Solo los administradores pueden aprobar
        if (!Auth::user()->is_admin) {
            return response()->json(['error' => 'No tienes permiso para aprobar fechas'], 403);
        }

        $displayDate->update(['is_approved' => true]);

        return response()->json(['message' => 'Fecha de exhibición aprobada correctamente']);
    }
} 