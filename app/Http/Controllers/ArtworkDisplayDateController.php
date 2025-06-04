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
                'display_date' => 'required|date|after_or_equal:today',
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

            // Verificar si ya hay solicitudes aprobadas para esta fecha
            $existingApprovedDates = ArtworkDisplayDate::where('display_date', $displayDate)
                ->where('is_approved', true)
                ->get();

            if ($existingApprovedDates->isNotEmpty()) {
                Log::warning('ArtworkDisplayDateController@store: Ya existen solicitudes aprobadas para esta fecha', [
                    'displayDate' => $displayDate,
                    'existingCount' => $existingApprovedDates->count()
                ]);
                return response()->json([
                    'error' => 'Ya hay una exhibición programada para este día. Por favor, selecciona otra fecha.'
                ], 422);
            }

            // Verificar si el usuario ya tiene solicitudes pendientes para esta fecha
            $existingPendingDates = ArtworkDisplayDate::where('display_date', $displayDate)
                ->where('user_id', $userId)
                ->where('is_approved', false)
                ->get();

            if ($existingPendingDates->isNotEmpty()) {
                Log::warning('ArtworkDisplayDateController@store: Usuario ya tiene solicitudes pendientes para esta fecha', [
                    'userId' => $userId,
                    'displayDate' => $displayDate,
                    'existingCount' => $existingPendingDates->count()
                ]);
                return response()->json([
                    'error' => 'Ya tienes solicitudes pendientes para esta fecha. Por favor, espera a que sean aprobadas o rechazadas antes de hacer una nueva solicitud.'
                ], 422);
            }

            // Verificar que no se exceda el límite de obras por fecha
            $existingCount = ArtworkDisplayDate::where('display_date', $displayDate)
                ->where('is_approved', true)
                ->count();

            if ($existingCount + count($artworkIds) > 10) {
                Log::warning('ArtworkDisplayDateController@store: Límite de obras excedido para la fecha', ['displayDate' => $displayDate, 'existingCount' => $existingCount, 'newCount' => count($artworkIds)]);
                return response()->json(['error' => 'No se pueden programar más de 10 obras (incluyendo las ya existentes) para esta fecha'], 422);
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
        Log::info('ArtworkDisplayDateController@approve: Inicio', ['requestId' => $displayDate->id]);

        $user = Auth::user();
        if (!$user) {
            Log::warning('ArtworkDisplayDateController@approve: Usuario no autenticado');
            return response()->json(['error' => 'Usuario no autenticado.'], 401); // Unauthenticated
        }
        Log::info('ArtworkDisplayDateController@approve: Usuario autenticado', ['userId' => $user->id, 'isAdmin' => $user->isAdmin()]);

        // Solo los administradores pueden aprobar
        if (!$user->isAdmin()) {
            Log::warning('ArtworkDisplayDateController@approve: Intento de aprobación sin permisos de admin', ['userId' => $user->id]);
            return response()->json(['error' => 'No tienes permiso para aprobar fechas'], 403);
        }
        Log::info('ArtworkDisplayDateController@approve: Usuario tiene permisos de admin');

        try {
            $displayDate->update(['is_approved' => true]);
            Log::info('ArtworkDisplayDateController@approve: Solicitud aprobada', ['requestId' => $displayDate->id]);

            return response()->json(['message' => 'Fecha de exhibición aprobada correctamente']);
        } catch (\Exception $e) {
            Log::error('ArtworkDisplayDateController@approve: Error al aprobar solicitud', [
                'requestId' => $displayDate->id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Error interno del servidor al aprobar la solicitud.'], 500);
        }
    }

    /**
     * Cancel an approved exhibition display date by the user.
     *
     * @param  \App\Models\ArtworkDisplayDate  $displayDate
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(ArtworkDisplayDate $displayDate)
    {
        $user = Auth::user();

        // Verificar que el usuario es el propietario de la fecha de exhibición o es administrador
        if ($displayDate->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['error' => 'No tienes permiso para cancelar esta fecha de exhibición.'], 403);
        }

        // Verificar que la solicitud ya ha sido aprobada
        if (!$displayDate->is_approved) {
            return response()->json(['error' => 'Esta solicitud de exhibición aún no ha sido aprobada.'], 400);
        }

        try {
            $displayDate->delete();
            return response()->json(['message' => 'Fecha de exhibición cancelada correctamente.']);
        } catch (\Exception $e) {
            Log::error('Error cancelling artwork display date:', [
                'displayDateId' => $displayDate->id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Ha ocurrido un error al cancelar la fecha de exhibición.'], 500);
        }
    }

    public function cancelAll($date)
    {
        Log::info('ArtworkDisplayDateController@cancelAll: Inicio', ['date' => $date]);
        
        $user = Auth::user();
        
        try {
            // Parsear la fecha y asegurarse de que está en el formato correcto
            $date = Carbon::parse($date)->format('Y-m-d');
            Log::info('ArtworkDisplayDateController@cancelAll: Fecha parseada', ['formattedDate' => $date]);

            // Obtener todas las fechas de exhibición para el día especificado
            $displayDates = ArtworkDisplayDate::whereDate('display_date', $date)
                ->where('is_approved', true);

            // Si no es admin, solo puede cancelar sus propias exhibiciones
            if (!$user->isAdmin()) {
                $displayDates->where('user_id', $user->id);
            }

            $count = $displayDates->count();
            Log::info('ArtworkDisplayDateController@cancelAll: Exhibiciones encontradas', ['count' => $count]);

            if ($count === 0) {
                Log::warning('ArtworkDisplayDateController@cancelAll: No hay exhibiciones para cancelar', ['date' => $date]);
                return response()->json([
                    'error' => 'No hay exhibiciones para cancelar en esta fecha.'
                ], 404);
            }

            // Eliminar todas las exhibiciones encontradas
            $displayDates->delete();
            Log::info('ArtworkDisplayDateController@cancelAll: Exhibiciones eliminadas', ['count' => $count]);

            return response()->json([
                'message' => 'Se han cancelado ' . $count . ' exhibiciones correctamente.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error cancelling all artwork display dates:', [
                'date' => $date,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Ha ocurrido un error al cancelar las exhibiciones.'
            ], 500);
        }
    }
} 