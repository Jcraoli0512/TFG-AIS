@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('header')
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de Usuarios') }}
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
                    {{-- Search and Filter form --}}
                    <div class="mb-6">
                        <form action="{{ route('admin.users.index') }}" method="GET" class="flex gap-4">
                            <div class="flex-1">
                                <input type="text" name="search" value="{{ request('search') }}" 
                                    placeholder="Buscar por nombre o email..." 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            </div>
                            <div>
                                <select name="role" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Todos los roles</option>
                                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrador</option>
                                    <option value="artist" {{ request('role') === 'artist' ? 'selected' : '' }}>Artista</option>
                                </select>
                                            </div>
                            <div>
                                <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Todos los estados</option>
                                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Activos</option>
                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactivos</option>
                                </select>
                                        </div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Buscar
                                                </button>
                                            </form>
                    </div>

                    {{-- User list table --}}
                    <div id="users-table-container">
                        @include('admin.users._users_table')
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit User Modal --}}
    <div id="editUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-4 mx-auto p-5 border max-w-4xl w-full shadow-lg rounded-md bg-white max-h-[90vh] flex flex-col">
            <!-- Modal Header -->
            <div class="pb-3 border-b border-gray-200 flex-shrink-0">
                <h3 class="text-lg font-bold text-gray-900">Editar Usuario</h3>
            </div>

            <!-- Modal Body - Form will be loaded here -->
            <div id="editUserModalBody" class="flex-1 overflow-y-auto py-4">
                <!-- Form content will be loaded dynamically -->
                <div class="flex items-center justify-center h-32">
                    <div class="text-gray-500">
                        <svg class="animate-spin h-8 w-8 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p>Cargando formulario...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="deleteConfirmationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <!-- Modal Header -->
            <div class="pb-3">
                <h3 class="text-lg font-bold">Confirmar Eliminación</h3>
            </div>

            <!-- Modal Body -->
            <div class="py-3">
                <p class="text-gray-700" id="deleteConfirmationMessage"></p>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" class="close-modal inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Cancelar
                </button>
                <button type="button" id="confirmDeleteButton" 
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Eliminar
                </button>
            </div>
        </div>
    </div>

    {{-- Success Modal --}}
    <div id="successModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 100;">
        <div class="relative top-20 mx-auto p-5 border max-w-sm w-full shadow-lg rounded-md bg-white">
            <div class="flex flex-col items-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2" id="successMessage"></h3>
                <button onclick="closeSuccessModal()" 
                        class="mt-4 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Aceptar
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        console.log('Script loaded'); // Debug: Check if script loads

        const editUserModal = document.getElementById('editUserModal');
        const editUserModalBody = document.getElementById('editUserModalBody');
        const deleteConfirmationModal = document.getElementById('deleteConfirmationModal');
        const deleteConfirmationMessage = document.getElementById('deleteConfirmationMessage');
        let userIdToDelete = null;

        // Function to close edit modal
        function closeEditModal() {
            editUserModal.classList.add('hidden');
            document.body.style.overflow = ''; // Restore background scroll
            editUserModalBody.innerHTML = `
                <div class="flex items-center justify-center h-32">
                    <div class="text-gray-500">
                        <svg class="animate-spin h-8 w-8 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p>Cargando formulario...</p>
                    </div>
                </div>
            `;
        }

        // Function to close delete modal
        function closeDeleteModal() {
            deleteConfirmationModal.classList.add('hidden');
            deleteConfirmationMessage.textContent = '';
            userIdToDelete = null;
        }

        // Function to close success modal
        function closeSuccessModal() {
            const modal = document.getElementById('successModal');
            modal.classList.add('hidden');
        }

        // Function to show success modal
        function showSuccessModal(message) {
            const modal = document.getElementById('successModal');
            const messageElement = document.getElementById('successMessage');
            messageElement.textContent = message;
            modal.classList.remove('hidden');
        }

        // Open modal and load form
        function attachEditModalListeners() {
            const openEditModalButtons = document.querySelectorAll('.open-edit-modal');
            
            openEditModalButtons.forEach(button => {
                button.addEventListener('click', async function() {
                    const userId = this.getAttribute('data-user-id');
                    console.log('Opening modal for user:', userId);

                    // Show loading message with spinner
                    editUserModalBody.innerHTML = `
                        <div class="flex items-center justify-center h-32">
                            <div class="text-gray-500">
                                <svg class="animate-spin h-8 w-8 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p>Cargando formulario...</p>
                            </div>
                        </div>
                    `;

                    // Show modal first
                    editUserModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden'; // Prevent background scroll

                    // Fetch user edit form via AJAX
                    try {
                        const response = await fetch(`/admin/users/${userId}/edit`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await response.json();

                        if (response.ok) {
                            editUserModalBody.innerHTML = data.html;
                            // Scroll to top of modal content
                            editUserModalBody.scrollTop = 0;
                        } else {
                            editUserModalBody.innerHTML = '<p class="text-red-600 text-center py-8">Error al cargar el formulario.</p>';
                            console.error('Error loading user edit form:', data);
                        }
                    } catch (error) {
                        editUserModalBody.innerHTML = '<p class="text-red-600 text-center py-8">Error de red al cargar el formulario.</p>';
                        console.error('Network error loading user edit form:', error);
                    }
                });
            });
        }

        // Handle delete confirmation modal
        function attachDeleteModalListeners() {
            const openDeleteModalButtons = document.querySelectorAll('.open-delete-modal');
            
            openDeleteModalButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    const userName = this.getAttribute('data-user-name');
                    console.log('Delete button clicked for user:', { userId, userName });

                    userIdToDelete = userId;
                    deleteConfirmationMessage.textContent = `¿Estás seguro de que quieres eliminar al usuario "${userName}"? Esta acción no se puede deshacer.`;
                    deleteConfirmationModal.classList.remove('hidden');
                });
            });
        }

        // Handle confirm delete button
        const confirmDeleteButton = document.getElementById('confirmDeleteButton');
        if (confirmDeleteButton) {
            confirmDeleteButton.addEventListener('click', async function() {
                if (!userIdToDelete) return;

                try {
                    let response;
                    if (userIdToDelete.type === 'artwork') {
                        response = await fetch(`/admin/users/${userIdToDelete.userId}/artworks/${userIdToDelete.artworkId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                    } else if (userIdToDelete.type === 'panoramic') {
                        response = await fetch(`/admin/users/${userIdToDelete.userId}/panoramic-image`, {
                            method: 'DELETE',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                    } else {
                        // Eliminación de usuario
                        response = await fetch(`/admin/users/${userIdToDelete}`, {
                            method: 'DELETE',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                    }

                    const data = await response.json();

                    if (response.ok) {
                        showSuccessModal(data.message);
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        throw new Error(data.message || 'Error al eliminar');
                    }
                } catch (error) {
                    showSuccessModal(error.message);
                } finally {
                    closeDeleteModal();
                }
            });
        }

        // Close modals when clicking close-modal class elements
        document.addEventListener('click', function(event) {
            if (event.target.closest('.close-modal')) {
                if (!editUserModal.classList.contains('hidden')) {
                    closeEditModal();
                }
                if (!deleteConfirmationModal.classList.contains('hidden')) {
                    closeDeleteModal();
                }
            }
        });

        // Close modals when clicking outside of them
        document.addEventListener('click', function(event) {
            if (event.target === editUserModal) {
                closeEditModal();
            }
            if (event.target === deleteConfirmationModal) {
                closeDeleteModal();
            }
            if (event.target === document.getElementById('successModal')) {
                closeSuccessModal();
            }
        });

        // Handle delete artwork function
        function handleDeleteArtwork(userId, artworkId) {
            userIdToDelete = { type: 'artwork', userId, artworkId };
            deleteConfirmationMessage.textContent = '¿Estás seguro de que deseas eliminar esta obra?';
            deleteConfirmationModal.classList.remove('hidden');
        }

        // Handle delete panoramic image function
        function handleDeletePanoramic(userId) {
            userIdToDelete = { type: 'panoramic', userId };
            deleteConfirmationMessage.textContent = '¿Estás seguro de que deseas eliminar la imagen panorámica?';
            deleteConfirmationModal.classList.remove('hidden');
        }

        // Make functions globally available
        window.closeEditModal = closeEditModal;
        window.handleDeleteArtwork = handleDeleteArtwork;
        window.handleDeletePanoramic = handleDeletePanoramic;

        // Attach listeners on initial page load
        document.addEventListener('DOMContentLoaded', function() {
            attachEditModalListeners();
            attachDeleteModalListeners();
        });

        // --- Asynchronous Search and Filtering ---
        const searchForm = document.querySelector('form[action="{{ route('admin.users.index') }}"]');
        const usersTableContainer = document.getElementById('users-table-container');

        if (searchForm && usersTableContainer) {
            searchForm.addEventListener('submit', async function(event) {
                event.preventDefault();

                const formData = new FormData(this);
                const queryString = new URLSearchParams(formData).toString();
                const url = `{{ route('admin.users.index') }}?${queryString}`;

                try {
                    usersTableContainer.innerHTML = '<p class="text-center text-gray-500">Cargando...</p>';

                    const response = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();
                    usersTableContainer.innerHTML = data.html;

                    // Re-attach event listeners for new buttons
                    attachEditModalListeners();
                    attachDeleteModalListeners();

                } catch (error) {
                    console.error('Error fetching users:', error);
                    usersTableContainer.innerHTML = '<p class="text-center text-red-600">Error al cargar los usuarios.</p>';
                }
            });
        }
    </script>
    @endpush
@endsection 