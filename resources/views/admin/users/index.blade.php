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
    <div id="editUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border max-w-xl w-full shadow-lg rounded-md bg-white">
            <!-- Modal Header -->
            <div class="pb-3">
                <h3 class="text-lg font-bold">Editar Usuario</h3>
            </div>

            <!-- Modal Body - Form will be loaded here -->
            <div id="editUserModalBody">
                <!-- Form content will be loaded dynamically -->
                <p>Cargando formulario...</p>
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

    @push('scripts')
    <script>
        console.log('Script loaded'); // Debug: Check if script loads

        const editUserModal = document.getElementById('editUserModal');
        const editUserModalBody = document.getElementById('editUserModalBody');
        const openEditModalButtons = document.querySelectorAll('.open-edit-modal');
        const closeModalButtons = document.querySelectorAll('.close-modal');

        // Delete Confirmation Modal Logic
        const deleteConfirmationModal = document.getElementById('deleteConfirmationModal');
        console.log('Delete modal element:', deleteConfirmationModal); // Debug: Check if modal element exists

        const deleteConfirmationMessage = document.getElementById('deleteConfirmationMessage');
        let userIdToDelete = null;

        // Open delete confirmation modal
        const openDeleteModalButtons = document.querySelectorAll('.open-delete-modal');
        console.log('Delete buttons found:', openDeleteModalButtons.length); // Debug: Check if buttons are found

        openDeleteModalButtons.forEach(button => {
            button.addEventListener('click', function() {
                console.log('Delete button clicked'); // Debug: Check if click handler is triggered
                const userId = this.getAttribute('data-user-id');
                const userName = this.getAttribute('data-user-name');
                console.log('User to delete:', { userId, userName }); // Debug: Check user data

                userIdToDelete = userId;
                deleteConfirmationMessage.textContent = `¿Estás seguro de que quieres eliminar al usuario "${userName}"? Esta acción no se puede deshacer.`;

                // Show the delete modal
                deleteConfirmationModal.classList.remove('hidden');
                console.log('Modal should be visible now'); // Debug: Check if modal visibility is changed
            });
        });

        // Handle click on the confirm delete button inside the modal
        const confirmDeleteButton = document.getElementById('confirmDeleteButton');
        if (confirmDeleteButton) {
            confirmDeleteButton.addEventListener('click', function() {
                if (userIdToDelete) {
                    document.getElementById(`delete-form-${userIdToDelete}`).submit();
                }
            });
        }

        // Open modal and load form
        openEditModalButtons.forEach(button => {
            button.addEventListener('click', async function() {
                const userId = this.getAttribute('data-user-id');
                console.log('Opening modal for user:', userId);

                // Show loading message
                editUserModalBody.innerHTML = '<p>Cargando formulario...</p>';

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
                    } else {
                        editUserModalBody.innerHTML = '<p class="text-red-600">Error al cargar el formulario.</p>';
                        console.error('Error loading user edit form:', data);
                    }
                } catch (error) {
                    editUserModalBody.innerHTML = '<p class="text-red-600">Error de red al cargar el formulario.</p>';
                    console.error('Network error loading user edit form:', error);
                }

                editUserModal.classList.remove('hidden');
            });
        });

        // Close modals when clicking close-modal class elements (using event delegation)
        document.addEventListener('click', function(event) {
            if (event.target.closest('.close-modal')) {
                // Close edit modal
                if (!editUserModal.classList.contains('hidden')) {
                    editUserModal.classList.add('hidden');
                    editUserModalBody.innerHTML = '<p>Cargando formulario...</p>';
                }
                // Close delete modal
                if (!deleteConfirmationModal.classList.contains('hidden')) {
                    deleteConfirmationModal.classList.add('hidden');
                    deleteConfirmationMessage.textContent = '';
                    userIdToDelete = null;
                }
            }
        });

        // Close modals when clicking outside of them
        window.addEventListener('click', function(event) {
            if (event.target === editUserModal) {
                editUserModal.classList.add('hidden');
                editUserModalBody.innerHTML = '<p>Cargando formulario...</p>';
            } else if (event.target === deleteConfirmationModal) {
                deleteConfirmationModal.classList.add('hidden');
                deleteConfirmationMessage.textContent = '';
                userIdToDelete = null;
            }
        });

        // --- Asynchronous Search and Filtering ---
        const searchForm = document.querySelector('form[action="{{ route('admin.users.index') }}"]');
        const usersTableContainer = document.getElementById('users-table-container');

        if (searchForm && usersTableContainer) {
            searchForm.addEventListener('submit', async function(event) {
                event.preventDefault(); // Prevent default form submission

                const formData = new FormData(this);
                const queryString = new URLSearchParams(formData).toString();
                const url = `{{ route('admin.users.index') }}?${queryString}`;

                try {
                    // Optional: Show a loading indicator
                    usersTableContainer.innerHTML = '<p class="text-center text-gray-500">Cargando...</p>';

                    const response = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest' // Indicate AJAX request
                        }
                    });
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    // Update the table content
                    usersTableContainer.innerHTML = data.html;

                    // Re-attach event listeners for new buttons (edit/delete) - IMPORTANT!
                    attachModalListeners();

                } catch (error) {
                    console.error('Error fetching users:', error);
                    usersTableContainer.innerHTML = '<p class="text-center text-red-600">Error al cargar los usuarios.</p>';
                }
            });
        }

        // Define handler functions for modal opening
        async function openEditModalHandler() {
            const userId = this.getAttribute('data-user-id');
            console.log('Opening modal for user:', userId); // Debugging

            // Show loading message
            editUserModalBody.innerHTML = '<p>Cargando formulario...</p>';

            // Fetch user edit form via AJAX
            try {
                const response = await fetch(`/admin/users/${userId}/edit`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest' // Indicate it's an AJAX request
                    }
                });
                const data = await response.json();

                if (response.ok) {
                    editUserModalBody.innerHTML = data.html; // Load the form HTML
                } else {
                    editUserModalBody.innerHTML = '<p class="text-red-600">Error al cargar el formulario.</p>';
                    console.error('Error loading user edit form:', data);
                }
            } catch (error) {
                editUserModalBody.innerHTML = '<p class="text-red-600">Error de red al cargar el formulario.</p>';
                console.error('Network error loading user edit form:', error);
            }

            editUserModal.classList.remove('hidden');
        }

        function openDeleteModalHandler() {
            const userId = this.getAttribute('data-user-id');
            const userName = this.getAttribute('data-user-name');

            userIdToDelete = userId;
            deleteConfirmationMessage.textContent = `¿Estás seguro de que quieres eliminar al usuario "${userName}"? Esta acción no se puede deshacer.`;

            // Show the delete modal
            deleteConfirmationModal.classList.remove('hidden');
        }

        // Function to attach modal listeners (called on initial load and after AJAX updates)
        function attachModalListeners() {
            // Re-select buttons from the updated DOM
            const openEditModalButtons = document.querySelectorAll('.open-edit-modal');
            const openDeleteModalButtons = document.querySelectorAll('.open-delete-modal');

            // Attach edit modal listeners
            openEditModalButtons.forEach(button => {
                 // Ensure no duplicate listeners by removing first (if they exist)
                 button.removeEventListener('click', openEditModalHandler);
                 button.addEventListener('click', openEditModalHandler);
            });

            // Attach delete modal listeners
            openDeleteModalButtons.forEach(button => {
                 // Ensure no duplicate listeners by removing first (if they exist)
                 button.removeEventListener('click', openDeleteModalHandler);
                 button.addEventListener('click', openDeleteModalHandler);
            });

             console.log('Modal listeners re-attached'); // Debugging
        }

        // Attach listeners on initial page load
        document.addEventListener('DOMContentLoaded', function() {
            attachModalListeners(); // Initial attachment
        });

    </script>
    @endpush
@endsection 