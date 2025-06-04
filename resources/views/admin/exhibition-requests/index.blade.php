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
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($requestsGrouped as $groupKey => $requestsInGroup)
                                @php
                                    $firstRequest = $requestsInGroup->first();
                                    $userId = $firstRequest->user->id;
                                    $userName = $firstRequest->user->name;
                                    $displayDate = \Carbon\Carbon::parse($firstRequest->display_date)->format('d/m/Y');
                                    $groupRequestId = $groupKey;
                                    $artworkCount = $requestsInGroup->count();
                                @endphp
                                <div class="bg-gray-100 rounded-lg shadow-sm p-4 cursor-pointer hover:bg-gray-200 transition-colors duration-200"
                                     onclick="showArtworksModal('{{ $groupRequestId }}', '{{ $userName }}', '{{ $displayDate }}')"
                                     data-group-id="{{ $groupRequestId }}">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="text-md font-semibold text-gray-800">{{ $userName }}</h4>
                                        <span class="text-sm text-gray-500">{{ $displayDate }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">{{ $artworkCount }} obra(s)</span>
                                        <div class="flex space-x-2">
                                            <button type="button" 
                                                    onclick="event.stopPropagation(); approveBatch('{{ $groupRequestId }}')"
                                                    class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                Aprobar
                                            </button>
                                            <button type="button" 
                                                    onclick="event.stopPropagation(); rejectBatch('{{ $groupRequestId }}')"
                                                    class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                Rechazar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">No hay solicitudes de exhibición pendientes.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

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

    {{-- Modal de Obras --}}
    <div id="artworksModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="artworksModalTitle"></h3>
                <button onclick="closeArtworksModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="mt-2 px-7 py-3">
                <div id="artworksGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <!-- Las imágenes se cargarán aquí dinámicamente -->
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

        container.innerHTML = '';
        container.appendChild(messageDiv);

        setTimeout(() => {
            messageDiv.remove();
        }, 5000);
    }

    // Referencias a los modales
    const confirmationModalAdmin = document.getElementById('confirmationModalAdmin');
    const artworksModal = document.getElementById('artworksModal');
    const confirmationModalTitle = document.getElementById('confirmationModalTitle');
    const confirmationModalMessage = document.getElementById('confirmationModalMessage');
    const artworksModalTitle = document.getElementById('artworksModalTitle');
    const artworksGrid = document.getElementById('artworksGrid');
    const cancelConfirmationButton = document.getElementById('cancelConfirmationButton');
    const confirmActionButton = document.getElementById('confirmActionButton');

    let currentAction = null;
    let currentRequestId = null;

    // Funciones para el modal de confirmación
    function showConfirmationModalAdmin(title, message, requestId, action) {
        confirmationModalTitle.textContent = title;
        confirmationModalMessage.textContent = message;
        currentRequestId = requestId;
        currentAction = action;
        
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

    function closeConfirmationModalAdmin() {
        confirmationModalAdmin.classList.add('hidden');
        document.body.style.overflow = '';
        currentAction = null;
        currentRequestId = null;
    }

    // Funciones para el modal de obras
    function showArtworksModal(groupKey, userName, displayDate) {
        artworksModalTitle.textContent = `Obras de ${userName} para el ${displayDate}`;
        artworksGrid.innerHTML = ''; // Limpiar el grid

        // Obtener las obras del grupo
        const groupElement = document.querySelector(`div[data-group-id="${groupKey}"]`);
        if (!groupElement) return;

        // Aquí deberías hacer una petición al servidor para obtener las obras
        // Por ahora, mostraremos un mensaje de carga
        artworksGrid.innerHTML = '<div class="col-span-full text-center">Cargando obras...</div>';

        // Mostrar el modal
        artworksModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        // Hacer la petición al servidor
        fetch(`/admin/exhibition-requests/${groupKey}/artworks`)
            .then(response => response.json())
            .then(data => {
                artworksGrid.innerHTML = ''; // Limpiar el mensaje de carga
                data.artworks.forEach(artwork => {
                    const artworkDiv = document.createElement('div');
                    artworkDiv.className = 'relative group';
                    artworkDiv.innerHTML = `
                        <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-lg bg-gray-200">
                            <img src="${artwork.image_path}" alt="${artwork.title}" class="h-full w-full object-cover object-center">
                        </div>
                        <div class="mt-2">
                            <h3 class="text-sm font-medium text-gray-900">${artwork.title}</h3>
                            <p class="text-sm text-gray-500">${artwork.technique}</p>
                        </div>
                    `;
                    artworksGrid.appendChild(artworkDiv);
                });
            })
            .catch(error => {
                console.error('Error loading artworks:', error);
                artworksGrid.innerHTML = '<div class="col-span-full text-center text-red-600">Error al cargar las obras</div>';
            });
    }

    function closeArtworksModal() {
        artworksModal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Event Listeners
    cancelConfirmationButton.addEventListener('click', closeConfirmationModalAdmin);
    confirmActionButton.addEventListener('click', function() {
        if (currentAction && currentRequestId !== null) {
            if (currentAction === 'approveBatch') {
                executeApproveBatch(currentRequestId);
            } else if (currentAction === 'rejectBatch') {
                executeRejectBatch(currentRequestId);
            }
        }
        closeConfirmationModalAdmin();
    });

    // Funciones de aprobación/rechazo
    function approveBatch(groupKey) {
        showConfirmationModalAdmin('Aprobar Solicitud', '¿Estás seguro de que deseas aprobar todas las obras de esta solicitud?', groupKey, 'approveBatch');
    }

    function rejectBatch(groupKey) {
        showConfirmationModalAdmin('Rechazar Solicitud', '¿Estás seguro de que deseas rechazar todas las obras de esta solicitud?', groupKey, 'rejectBatch');
    }

    function executeApproveBatch(groupKey) {
        // Extraer userId y date del groupKey
        const parts = groupKey.split('-');
        const userId = parts[0];
        const date = parts.slice(1).join('-'); // Unir las partes restantes para formar la fecha
        
        console.log('Aprobando solicitud:', {
            groupKey,
            userId,
            date,
            url: `/admin/exhibition-requests/batch/${userId}/${date}/approve`
        });
        
        fetch(`/admin/exhibition-requests/batch/${userId}/${date}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                return response.json().then(data => {
                    console.error('Error response:', data);
                    throw new Error(data.message || `Error ${response.status}: ${response.statusText}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Success response:', data);
            if (data.success) {
                const groupElement = document.querySelector(`div[data-group-id="${groupKey}"]`);
                if (groupElement) {
                    groupElement.remove();
                }
                showAsyncMessage(data.message, 'success');
            } else {
                console.error('Error data:', data);
                showAsyncMessage(data.message || 'Error al aprobar la solicitud', 'error');
            }
        })
        .catch(error => {
            console.error('Error approving batch:', error);
            showAsyncMessage('Error al aprobar la solicitud: ' + error.message, 'error');
        });
    }

    function executeRejectBatch(groupKey) {
        // Extraer userId y date del groupKey
        const parts = groupKey.split('-');
        const userId = parts[0];
        const date = parts.slice(1).join('-'); // Unir las partes restantes para formar la fecha
        
        console.log('Rechazando solicitud:', {
            groupKey,
            userId,
            date,
            url: `/admin/exhibition-requests/batch/${userId}/${date}/reject`
        });
        
        fetch(`/admin/exhibition-requests/batch/${userId}/${date}/reject`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                return response.json().then(data => {
                    console.error('Error response:', data);
                    throw new Error(data.message || `Error ${response.status}: ${response.statusText}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Success response:', data);
            if (data.success) {
                const groupElement = document.querySelector(`div[data-group-id="${groupKey}"]`);
                if (groupElement) {
                    groupElement.remove();
                }
                showAsyncMessage(data.message, 'success');
            } else {
                console.error('Error data:', data);
                showAsyncMessage(data.message || 'Error al rechazar la solicitud', 'error');
            }
        })
        .catch(error => {
            console.error('Error rejecting batch:', error);
            showAsyncMessage('Error al rechazar la solicitud: ' + error.message, 'error');
        });
    }
</script>
@endpush 