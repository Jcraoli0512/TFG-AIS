@extends('layouts.app')

@section('title', 'Solicitudes de Exhibición')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ ('Solicitudes de Exhibición') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Solicitudes Pendientes</h3>

                    @if($requests->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Obra</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Solicitada</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Solicitud</th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Acciones</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($requests as $request)
                                        <tr data-request-id="{{ $request->id }}">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ $request->artwork->image_path ? asset('storage/' . $request->artwork->image_path) : asset('img/placeholder.jpg') }}" alt="Obra Imagen">
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ $request->artwork->title }}</div>
                                                        <div class="text-sm text-gray-500">{{ $request->artwork->technique }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $request->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $request->user->email }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($request->display_date)->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $request->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                {{-- Botones de Acción --}}
                                                <button type="button" 
                                                        onclick="approveRequest({{ $request->id }})"
                                                        class="text-green-600 hover:text-green-900 mr-4">Aprobar</button>
                                                <button type="button" 
                                                        onclick="rejectRequest({{ $request->id }})"
                                                        class="text-red-600 hover:text-red-900">Rechazar</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-600">No hay solicitudes de exhibición pendientes.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modales (para confirmación de aprobación/rechazo, mensajes, etc.) --}}
    {{-- Puedes reutilizar los modales de éxito/error o crear nuevos si necesitas más control --}}

@endsection

@push('scripts')
<script>
    function approveRequest(id) {
        if (confirm('¿Estás seguro de que deseas aprobar esta solicitud?')) {
            fetch(`/admin/exhibition-requests/${id}/approve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Remove the row from the table
                    const row = document.querySelector(`tr[data-request-id="${id}"]`);
                    if (row) {
                        row.remove();
                    }
                    // Show success message
                    alert('Solicitud aprobada correctamente');
                } else {
                    throw new Error(data.message || 'Error al aprobar la solicitud');
                }
            })
            .catch(error => {
                console.error('Error approving request:', error);
                alert('Error al aprobar la solicitud: ' + error.message);
            });
        }
    }

    function rejectRequest(requestId) {
         if (confirm('¿Estás seguro de que deseas rechazar esta solicitud?')) {
            // Implementar lógica de rechazo (podría ser una ruta DELETE o POST con un flag)
            // Por ahora, usaremos una ruta DELETE asumiendo que eliminar el registro significa rechazar
            fetch(`/api/artwork-display-dates/${requestId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                window.location.reload(); // Recargar la página para actualizar la lista
            })
            .catch(error => {
                console.error('Error rejecting request:', error);
                alert('Error al rechazar la solicitud.');
            });
        }
    }
</script>
@endpush 