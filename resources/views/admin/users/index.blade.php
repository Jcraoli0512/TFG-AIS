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
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de registro</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($users as $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded-full" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->role === 'admin' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $user->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-3">
                                                {{-- Edit button/link to open modal --}}
                                                <button type="button" 
                                                        class="open-edit-modal inline-flex items-center px-3 py-1.5 bg-indigo-100 text-indigo-700 rounded-md hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                                        data-user-id="{{ $user->id }}">
                                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                    Editar
                                                </button>

                                                @if($user->id !== auth()->id())
                                                    <form action="{{ route('admin.users.delete', $user) }}" method="POST" class="inline" id="delete-form-{{ $user->id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" 
                                                            class="open-delete-modal inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 rounded-md hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                                            data-user-id="{{ $user->id }}"
                                                            data-user-name="{{ $user->name }}">
                                                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No se encontraron usuarios.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit User Modal --}}
    <div id="editUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border max-w-xl w-full shadow-lg rounded-md bg-white">
            <!-- Modal Header -->
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-lg font-bold">Editar Usuario</h3>
            </div>

            <!-- Modal Body - Form will be loaded here -->
            <div id="editUserModalBody">
                <!-- Form content will be loaded dynamically -->
                <p>Cargando formulario...</p>
            </div>

            <!-- Modal Footer (optional, can include Save/Cancel if not part of the form) -->
            <div class="flex justify-end pt-4">
                 {{-- Buttons will be part of the form loaded in modal body --}}
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
    </script>
    @endpush

    {{-- Delete Confirmation Modal --}}
    <div id="deleteConfirmationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <!-- Modal Header -->
            <div class="flex justify-between items-center pb-3">
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
@endsection 