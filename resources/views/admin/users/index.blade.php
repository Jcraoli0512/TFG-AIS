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
                    // Cerrar el modal de confirmación
                    deleteConfirmationModal.classList.add('hidden');
                    userIdToDelete = null;
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
        document.addEventListener('click', function(event) {
            const editModal = document.getElementById('editUserModal');
            const deleteModal = document.getElementById('deleteConfirmationModal');
            const successModal = document.getElementById('successModal');

            // Verificar si el clic fue fuera del contenido de cada modal
            const clickedInsideEditModalContent = editModal && editModal.contains(event.target) && event.target !== editModal;
            const clickedInsideDeleteModalContent = deleteModal && deleteModal.contains(event.target) && event.target !== deleteModal;
            const clickedInsideSuccessModalContent = successModal && successModal.contains(event.target) && event.target !== successModal;

            if (editModal && event.target === editModal && !clickedInsideEditModalContent) {
                closeEditModal();
            }
            if (deleteModal && event.target === deleteModal && !clickedInsideDeleteModalContent) {
                closeDeleteModal();
            }
            if (successModal && event.target === successModal && !clickedInsideSuccessModalContent) {
                closeSuccessModal();
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

        // Funciones para el modal de éxito
        function showSuccessModal(message) {
            const modal = document.getElementById('successModal');
            const messageElement = document.getElementById('successMessage');
            messageElement.textContent = message;
            modal.classList.remove('hidden');
        }

        function closeSuccessModal() {
            const modal = document.getElementById('successModal');
            modal.classList.add('hidden');
        }

        // Función para cerrar el modal de edición
        function closeEditModal() {
            const modal = document.getElementById('editUserModal');
            const modalBody = document.getElementById('editUserModalBody');
            modal.classList.add('hidden');
            modalBody.innerHTML = '<p>Cargando formulario...</p>';
        }

        // Función para manejar la edición de usuarios
        async function handleEditUser(userId) {
            const modal = document.getElementById('editUserModal');
            const modalBody = document.getElementById('editUserModalBody');

            try {
                modalBody.innerHTML = '<p class="text-center py-4">Cargando formulario...</p>';
                modal.classList.remove('hidden');

                const response = await fetch(`/admin/users/${userId}/edit`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Error al cargar el formulario');
                }

                modalBody.innerHTML = data.html;

                // Agregar el manejador del formulario después de cargar el contenido
                const form = modalBody.querySelector('form');
                if (form) {
                    form.addEventListener('submit', async function(e) {
                        e.preventDefault();
                        
                        const submitButton = form.querySelector('button[type="submit"]');
                        const originalButtonText = submitButton.textContent;
                        
                        try {
                            submitButton.disabled = true;
                            submitButton.textContent = 'Guardando...';
                            
                            const formData = new FormData(form);
                            const response = await fetch(form.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });

                            const data = await response.json();

                            if (response.ok) {
                                showSuccessModal(data.message);
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1500);
                            } else {
                                throw new Error(data.message || 'Error al actualizar el usuario');
                            }
                        } catch (error) {
                            showSuccessModal(error.message);
                        } finally {
                            submitButton.disabled = false;
                            submitButton.textContent = originalButtonText;
                        }
                    });
                }
            } catch (error) {
                modalBody.innerHTML = `<p class="text-red-600 text-center py-4">${error.message}</p>`;
                console.error('Error loading user edit form:', error);
            }
        }

        // Función para manejar la eliminación de obras
        async function handleDeleteArtwork(userId, artworkId) {
            userIdToDelete = { type: 'artwork', userId, artworkId };
            deleteConfirmationMessage.textContent = '¿Estás seguro de que deseas eliminar esta obra?';
            deleteConfirmationModal.classList.remove('hidden');
        }

        // Función para manejar la eliminación de la imagen panorámica
        async function handleDeletePanoramic(userId) {
            userIdToDelete = { type: 'panoramic', userId };
            deleteConfirmationMessage.textContent = '¿Estás seguro de que deseas eliminar la imagen panorámica?';
            deleteConfirmationModal.classList.remove('hidden');
        }

        // Función para manejar la eliminación de usuarios
        async function handleDeleteUser(userId, userName) {
            userIdToDelete = userId;
            deleteConfirmationMessage.textContent = `¿Estás seguro de que quieres eliminar al usuario "${userName}"? Esta acción no se puede deshacer.`;
            deleteConfirmationModal.classList.remove('hidden');
        }

        // Función para adjuntar event listeners
        function attachEventListeners() {
            // Botones de editar
            document.querySelectorAll('.open-edit-modal').forEach(button => {
                button.onclick = function() {
                    const userId = this.getAttribute('data-user-id');
                    handleEditUser(userId);
                };
            });

            // Botones de eliminar
            document.querySelectorAll('.open-delete-modal').forEach(button => {
                button.onclick = function() {
                    const userId = this.getAttribute('data-user-id');
                    const userName = this.getAttribute('data-user-name');
                    handleDeleteUser(userId, userName);
                };
            });

            // Botones de eliminar obra
            document.querySelectorAll('[data-delete-artwork]').forEach(button => {
                button.onclick = function() {
                    const userId = this.getAttribute('data-user-id');
                    const artworkId = this.getAttribute('data-artwork-id');
                    handleDeleteArtwork(userId, artworkId);
                };
            });

            // Botones de eliminar imagen panorámica
            document.querySelectorAll('[data-delete-panoramic]').forEach(button => {
                button.onclick = function() {
                    const userId = this.getAttribute('data-user-id');
                    handleDeletePanoramic(userId);
                };
            });

            // Botones de cerrar modal
            document.querySelectorAll('.close-modal').forEach(button => {
                button.onclick = function() {
                    deleteConfirmationModal.classList.add('hidden');
                    userIdToDelete = null;
                };
            });
        }

        // Adjuntar event listeners cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', attachEventListeners);

        // Cerrar modal al hacer clic fuera
        window.addEventListener('click', function(event) {
            if (event.target === deleteConfirmationModal) {
                deleteConfirmationModal.classList.add('hidden');
                userIdToDelete = null;
            }
        });

        // Función para abrir el modal de edición
        async function openEditModal(userId) {
            console.log('Opening modal for user:', userId);
            const modal = document.getElementById('editUserModal');
            const modalBody = document.getElementById('editUserModalBody');

            // Show loading message
            modalBody.innerHTML = '<p>Cargando formulario...</p>';
            modal.classList.remove('hidden');

            try {
                const response = await fetch(`/admin/users/${userId}/edit`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();

                if (response.ok) {
                    modalBody.innerHTML = data.html;
                } else {
                    modalBody.innerHTML = '<p class="text-red-600">Error al cargar el formulario.</p>';
                    console.error('Error loading user edit form:', data);
                }
            } catch (error) {
                modalBody.innerHTML = '<p class="text-red-600">Error de red al cargar el formulario.</p>';
                console.error('Network error loading user edit form:', error);
            }
        }

        // Función para cerrar el modal de edición
        function closeEditModal() {
            const modal = document.getElementById('editUserModal');
            const modalBody = document.getElementById('editUserModalBody');
            modal.classList.add('hidden');
            modalBody.innerHTML = '<p>Cargando formulario...</p>';
        }

        // Función para cerrar el modal de confirmación
        function closeDeleteModal() {
            const modal = document.getElementById('deleteConfirmationModal');
            const message = document.getElementById('deleteConfirmationMessage');
            modal.classList.add('hidden');
            message.textContent = '';
            userIdToDelete = null;
        }

        // Event Listeners para los botones de editar
        document.addEventListener('click', function(event) {
            const editButton = event.target.closest('.open-edit-modal');
            if (editButton) {
                // No prevenir el comportamiento por defecto aquí si el botón es un enlace o tiene acción propia
                // event.preventDefault(); // Eliminar o comentar si causa problemas
                const userId = editButton.getAttribute('data-user-id');
                openEditModal(userId);
            }
        });

        // Event Listeners para los botones de cerrar
        document.addEventListener('click', function(event) {
            const closeButton = event.target.closest('.close-modal');
            if (closeButton) {
                event.preventDefault();
                event.stopPropagation();
                closeEditModal();
                closeDeleteModal();
            }
        });

        // Event Listener para cerrar modales al hacer clic fuera
        document.addEventListener('click', function(event) {
            const editModal = document.getElementById('editUserModal');
            const deleteModal = document.getElementById('deleteConfirmationModal');
            const successModal = document.getElementById('successModal');

            if (event.target === editModal) {
                closeEditModal();
            } else if (event.target === deleteModal) {
                closeDeleteModal();
            } else if (event.target === successModal) {
                closeSuccessModal();
            }
        });

        // Prevenir que los clics dentro del modal lo cierren
        document.addEventListener('click', function(event) {
            const modalContent = event.target.closest('.relative.top-20.mx-auto');
            if (modalContent) {
                event.stopPropagation();
            }
        });
    </script>
    @endpush
@endsection 