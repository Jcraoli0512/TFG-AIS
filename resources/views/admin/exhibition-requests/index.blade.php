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
            {{-- Contenedor para mensajes asíncronos --}}
            <div id="async-message-container" class="mb-4"></div>

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

                    @if($requestsGrouped->count() > 0)
                        @foreach($requestsGrouped as $groupKey => $requestsInGroup)
                            @php
                                $firstRequest = $requestsInGroup->first();
                                $userId = $firstRequest->user->id;
                                $userName = $firstRequest->user->name;
                                $displayDate = \Carbon\Carbon::parse($firstRequest->display_date)->format('d/m/Y');
                                $groupRequestId = $groupKey; // Usaremos la clave del grupo como identificador temporal del lote
                            @endphp
                            <div class="bg-gray-100 rounded-lg shadow-sm p-4 mb-6" data-group-id="{{ $groupRequestId }}">
                                <div class="flex justify-between items-center mb-4 border-b pb-3">
                                    <h4 class="text-md font-semibold text-gray-800">Solicitud de {{ $userName }} para el {{ $displayDate }}</h4>
                                    <div>
                                         {{-- Botones de Acción para el Lote --}}
                                        <button type="button" 
                                                onclick="approveBatch('{{ $groupRequestId }}')"
                                                class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 mr-2">
                                            Aprobar Lote
                                        </button>
                                        <button type="button" 
                                                onclick="rejectBatch('{{ $groupRequestId }}')"
                                                class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            Rechazar Lote
                                        </button>
                                    </div>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Obra</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Solicitud</th>
                                                 <th scope="col" class="relative px-6 py-3">
                                                    <span class="sr-only">Acciones Individuales</span>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($requestsInGroup as $request)
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
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $request->created_at->format('d/m/Y H:i') }}
                                                    </td>
                                                     <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        {{-- Botones de Acción Individual (opcional, si aún quieres control por obra) --}}
                                                        {{-- Puedes mantener estos si quieres permitir aprobar/rechazar obras específicas dentro del lote --}}
                                                        <button type="button" 
                                                                onclick="approveRequest({{ $request->id }})"
                                                                class="text-green-600 hover:text-green-900 mr-4">Aprobar Individual</button>
                                                        <button type="button" 
                                                                onclick="rejectRequest({{ $request->id }})"
                                                                class="text-red-600 hover:text-red-900">Rechazar Individual</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-600">No hay solicitudes de exhibición pendientes.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modales (para confirmación de aprobación/rechazo, mensajes, etc.) --}}
    {{-- Puedes reutilizar los modales de éxito/error o crear nuevos si necesitas más control --}}

    {{-- Modal de Confirmación --}}
    <div id="confirmationModalAdmin" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="confirmationModalTitle"></h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500" id="confirmationModalMessage"></p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="cancelConfirmationButton" class="px-4 py-2 mr-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md w-1/2 shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200">
                        Cancelar
                    </button>
                    <button id="confirmActionButton" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-1/2 shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    // Función para mostrar mensajes asíncronos
    function showAsyncMessage(message, type = 'success') {
        const container = document.getElementById('async-message-container');
        if (!container) return;

        // Determinar clases de estilo según el tipo de mensaje
        let bgColor, borderColor, textColor;
        if (type === 'success') {
            bgColor = 'bg-green-100';
            borderColor = 'border-green-400';
            textColor = 'text-green-700';
        } else if (type === 'error') {
            bgColor = 'bg-red-100';
            borderColor = 'border-red-400';
            textColor = 'text-red-700';
        } else if (type === 'warning') {
             bgColor = 'bg-yellow-100';
            borderColor = 'border-yellow-400';
            textColor = 'text-yellow-700';
        }

        const messageDiv = document.createElement('div');
        messageDiv.classList.add(bgColor, borderColor, textColor, 'px-4', 'py-3', 'rounded', 'relative', 'mb-4');
        messageDiv.setAttribute('role', 'alert');
        messageDiv.innerHTML = `<span class="block sm:inline">${message}</span>`;

        // Limpiar mensajes anteriores y añadir el nuevo
        container.innerHTML = '';
        container.appendChild(messageDiv);

        // Opcional: auto-ocultar el mensaje después de unos segundos
        setTimeout(() => {
            messageDiv.remove();
        }, 5000); // Eliminar después de 5 segundos
    }

    // Referencias al modal de confirmación y sus botones
    const confirmationModalAdmin = document.getElementById('confirmationModalAdmin');
    const confirmationModalTitle = document.getElementById('confirmationModalTitle');
    const confirmationModalMessage = document.getElementById('confirmationModalMessage');
    const cancelConfirmationButton = document.getElementById('cancelConfirmationButton');
    const confirmActionButton = document.getElementById('confirmActionButton');

    let currentAction = null; // Para almacenar la acción a realizar (aprobar/rechazar)
    let currentRequestId = null; // Para almacenar el ID de la solicitud

    // Función para mostrar el modal de confirmación
    function showConfirmationModalAdmin(title, message, requestId, action) {
        confirmationModalTitle.textContent = title;
        confirmationModalMessage.textContent = message;
        currentRequestId = requestId;
        currentAction = action; // 'approve' o 'reject'
        
        // Ajustar color del botón de confirmar según la acción
        if (action === 'approve') {
            confirmActionButton.classList.remove('bg-red-600', 'hover:bg-red-700', 'focus:ring-red-300');
            confirmActionButton.classList.add('bg-green-600', 'hover:bg-green-700', 'focus:ring-green-300');
        } else if (action === 'reject') {
            confirmActionButton.classList.remove('bg-green-600', 'hover:bg-green-700', 'focus:ring-green-300');
            confirmActionButton.classList.add('bg-red-600', 'hover:bg-red-700', 'focus:ring-red-300');
        }

        confirmationModalAdmin.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    // Función para cerrar el modal de confirmación
    function closeConfirmationModalAdmin() {
        confirmationModalAdmin.classList.add('hidden');
        document.body.style.overflow = '';
        // Resetear variables
        currentAction = null;
        currentRequestId = null;
    }

    // Listener para el botón Cancelar del modal
    cancelConfirmationButton.addEventListener('click', function() {
        closeConfirmationModalAdmin();
    });

    // Modificar el listener del botón de Confirmar para manejar acciones de Lote
    confirmActionButton.addEventListener('click', function() {
        if (currentAction && currentRequestId !== null) {
            if (currentAction === 'approve') {
                executeApprovedRequest(currentRequestId);
            } else if (currentAction === 'reject') {
                executeRejectRequest(currentRequestId);
            } else if (currentAction === 'approveBatch') {
                 executeApproveBatch(currentRequestId); // currentRequestId aquí es el groupKey
            } else if (currentAction === 'rejectBatch') {
                 executeRejectBatch(currentRequestId); // currentRequestId aquí es el groupKey
            }
        }
        closeConfirmationModalAdmin();
    });

    // Cerrar modal haciendo clic fuera
    confirmationModalAdmin.addEventListener('click', function(event) {
        if (event.target === confirmationModalAdmin) {
            closeConfirmationModalAdmin();
        }
    });

    // Funciones que ejecutan la petición real (anteriormente dentro del confirm)
    function executeApprovedRequest(id) {
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
                 return response.json().then(data => {
                     const errorMessage = data.message || data.error || 'Error desconocido al aprobar la solicitud';
                     throw new Error(errorMessage);
                 }).catch(() => {
                     throw new Error(`Error ${response.status}: ${response.statusText}`);
                 });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const row = document.querySelector(`tr[data-request-id="${id}"]`);
                if (row) {
                    row.remove();
                }
                showAsyncMessage(data.message || 'Solicitud aprobada correctamente', 'success');
            } else {
                showAsyncMessage(data.message || 'Error al aprobar la solicitud', 'error');
            }
        })
        .catch(error => {
            console.error('Error approving request:', error);
            showAsyncMessage('Error al aprobar la solicitud: ' + error.message, 'error');
        });
    }

    function executeRejectRequest(requestId) {
        fetch(`/admin/exhibition-requests/${requestId}/reject`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    const errorMessage = data.message || data.error || 'Error desconocido al rechazar la solicitud';
                     throw new Error(errorMessage);
                }).catch(() => {
                     throw new Error(`Error ${response.status}: ${response.statusText}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const row = document.querySelector(`tr[data-request-id="${requestId}"]`);
                if (row) {
                    row.remove();
                }
                showAsyncMessage(data.message || 'Solicitud rechazada correctamente', 'success');
            } else {
                showAsyncMessage(data.message || 'Error al rechazar la solicitud', 'error');
            }
        })
        .catch(error => {
            console.error('Error rejecting request:', error);
            showAsyncMessage(error.message, 'error');
        });
    }

    // Modificar las funciones originales para mostrar el modal en su lugar
    function approveRequest(id) {
        showConfirmationModalAdmin('Aprobar Solicitud Individual', '¿Estás seguro de que deseas aprobar esta solicitud de exhibición individual?', id, 'approve');
    }

    function rejectRequest(requestId) {
        showConfirmationModalAdmin('Rechazar Solicitud Individual', '¿Estás seguro de que deseas rechazar esta solicitud de exhibición individual?', requestId, 'reject');
    }

    // --- Funciones para acciones de Lote ---

    function approveBatch(groupKey) {
         showConfirmationModalAdmin('Aprobar Lote', '¿Estás seguro de que deseas aprobar todas las solicitudes para este usuario y fecha?', groupKey, 'approveBatch');
    }

     function rejectBatch(groupKey) {
         showConfirmationModalAdmin('Rechazar Lote', '¿Estás seguro de que deseas rechazar todas las solicitudes para este usuario y fecha?', groupKey, 'rejectBatch');
    }

    // Función para ejecutar la aprobación del lote
    function executeApproveBatch(groupKey) {
        fetch(`/admin/exhibition-requests/${groupKey}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                 return response.json().then(data => {
                     const errorMessage = data.message || data.error || 'Error desconocido al aprobar el lote';
                     throw new Error(errorMessage);
                 }).catch(() => {
                     throw new Error(`Error ${response.status}: ${response.statusText}`);
                 });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Remove the entire group block from the DOM
                const groupBlock = document.querySelector(`div[data-group-id="${groupKey}"]`);
                if (groupBlock) {
                    groupBlock.remove();
                }
                showAsyncMessage(data.message || 'Lote de solicitudes aprobado correctamente', 'success');
            } else {
                showAsyncMessage(data.message || 'Error al aprobar el lote', 'error');
            }
        })
        .catch(error => {
            console.error('Error approving batch:', error);
            showAsyncMessage('Error al aprobar el lote: ' + error.message, 'error');
        });
    }

    // Función para ejecutar el rechazo del lote
     function executeRejectBatch(groupKey) {
        fetch(`/admin/exhibition-requests/${groupKey}/reject`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                 return response.json().then(data => {
                     const errorMessage = data.message || data.error || 'Error desconocido al rechazar el lote';
                     throw new Error(errorMessage);
                 }).catch(() => {
                     throw new Error(`Error ${response.status}: ${response.statusText}`);
                 });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Remove the entire group block from the DOM
                const groupBlock = document.querySelector(`div[data-group-id="${groupKey}"]`);
                if (groupBlock) {
                    groupBlock.remove();
                }
                 showAsyncMessage(data.message || 'Lote de solicitudes rechazado correctamente', 'success');
            } else {
                showAsyncMessage(data.message || 'Error al rechazar el lote', 'error');
            }
        })
        .catch(error => {
            console.error('Error rejecting batch:', error);
            showAsyncMessage(error.message, 'error');
        });
    }

</script>
@endpush 