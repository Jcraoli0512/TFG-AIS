@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Perfil de ' . $user->name)

@section('content')

<div class="container mx-auto px-4">
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        {{-- Top section: Name, Bio, Profile Photo --}}
        <div class="flex flex-col md:flex-row items-center md:items-start gap-8 mb-12">
            {{-- Name and Biography --}}
            <div class="flex-1 text-center md:text-left">
                <div class="flex items-center justify-center md:justify-start gap-2 mb-4">
                    <h1 class="text-4xl font-bold text-gray-800">{{ $user->name }}</h1>
                    @auth
                        @if(auth()->user()->id === $user->id)
                            <button id="openEditProfileModal" class="text-gray-600 hover:text-indigo-600 transition-colors duration-200" title="Editar Perfil">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                        @endif
                    @endauth
                </div>
                <p class="text-gray-700 leading-relaxed">{{ $user->biography ?: 'Este usuario aún no ha añadido una biografía.' }}</p>
                 {{-- Optional: Add social media links here if available in the user model --}}

            </div>
            {{-- Profile Photo --}}
            <div class="flex-shrink-0">
                <img src="{{ $user->profile_photo_url }}" alt="Foto de perfil de {{ $user->name }}" class="w-48 h-48 object-cover rounded-lg shadow-md">
            </div>
        </div>

        @if($user->artworks->count() > 0)
            {{-- Artworks Section --}}
            <div class="mb-12">
                <div class="flex items-center gap-2 mb-6">
                    <h2 class="text-3xl font-semibold text-gray-800">Obras</h2>
                    @auth
                        @if(auth()->user()->id === $user->id)
                            <button id="openAddArtworkModal" class="text-gray-600 hover:text-green-600 transition-colors duration-200" title="Añadir Obra">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        @endif
                    @endauth
                </div>

                {{-- Artworks Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($user->artworks as $artwork)
                        <div class="relative group rounded-lg overflow-hidden shadow-md">
                            <img src="{{ $artwork->image_path ? asset('storage/' . $artwork->image_path) : 'https://via.placeholder.com/300x200?text=No+Image' }}" alt="{{ $artwork->title }}" class="w-full h-64 object-cover transition-transform duration-300 group-hover:scale-105">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-300 flex items-center justify-center p-4">
                                <div class="text-white opacity-0 group-hover:opacity-100 text-center">
                                    <h3 class="font-semibold text-lg mb-1">{{ $artwork->title }}</h3>
                                    <p class="text-sm">{{ $artwork->technique }} - {{ $artwork->year }}</p>
                                    
                                    <div class="flex gap-2 justify-center mt-2">
                                        {{-- Botón de ver (visible para todos) --}}
                                        <button onclick="openArtworkModal({{ $artwork->id }})" 
                                                class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                                            Ver
                                        </button>
                                        
                                        {{-- Botón de eliminar (solo visible para el propietario) --}}
                                        @auth
                                            @if(auth()->user()->id === $user->id)
                                                <form action="{{ route('artworks.destroy', $artwork) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta obra?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-sm rounded-md hover:bg-red-700">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        @else
            <div class="flex items-center gap-2 mb-6">
                <h2 class="text-3xl font-semibold text-gray-800">Obras</h2>
                @auth
                    @if(auth()->user()->id === $user->id)
                        <button id="openAddArtworkModal" class="text-gray-600 hover:text-green-600 transition-colors duration-200" title="Añadir tu primera Obra">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </button>
                    @endif
                @endauth
            </div>
            <p class="text-gray-700 text-center mb-12">{{ $user->name }} aún no ha añadido ninguna obra a la plataforma.</p>

        @endif

        {{-- Panoramic Image Section --}}
        <div class="w-full rounded-lg overflow-hidden shadow-lg relative group">
            <img src="{{ $user->panoramic_image ? asset('storage/' . $user->panoramic_image) : 'https://picsum.photos/1200/300' }}" 
                 alt="Imagen Panorámica" 
                 class="w-full h-[200px] object-cover">
            
            @auth
                @if(auth()->user()->id === $user->id)
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-300 flex items-center justify-center">
                        <button onclick="openPanoramicImageModal()" 
                                class="text-white opacity-0 group-hover:opacity-100 bg-black bg-opacity-50 px-4 py-2 rounded-lg hover:bg-opacity-70 transition-all duration-300">
                            Cambiar Imagen Panorámica
                        </button>
                    </div>
                @endif
            @endauth
        </div>

    </div>
</div>

{{-- Edit Profile Modal --}}
<div id="editProfileModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 100;">
    <div class="relative top-20 mx-auto p-5 border max-w-lg w-full shadow-lg rounded-md bg-white">
        <!-- Modal Header -->
        <div class="pb-3">
            <h3 class="text-lg font-bold">Editar Perfil</h3>
        </div>

        <!-- Modal Body - Form will be loaded here -->
        <div id="editProfileModalBody">
            <p>Cargando formulario...</p>
        </div>
    </div>
</div>

{{-- Add Artwork Modal --}}
<div id="addArtworkModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 100;">
    <div class="relative top-20 mx-auto p-5 border max-w-lg w-full shadow-lg rounded-md bg-white">
        <!-- Modal Header -->
        <div class="pb-3">
            <h3 class="text-lg font-bold">Añadir Obra</h3>
        </div>

        <!-- Modal Body - Form will be loaded here -->
        <div id="addArtworkModalBody">
            <p>Cargando formulario...</p>
        </div>
    </div>
</div>

{{-- Artwork View Modal --}}
<div id="artworkViewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 100;">
    <div class="relative top-20 mx-auto p-5 border max-w-4xl w-full shadow-lg rounded-md bg-white">
        <!-- Modal Header -->
        <div class="flex justify-between items-center pb-3">
            <h3 class="text-2xl font-bold" id="artworkTitle"></h3>
            <button onclick="closeArtworkModal()" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="flex flex-col md:flex-row gap-6">
            <div class="md:w-2/3">
                <img id="artworkImage" src="" alt="" class="w-full h-auto rounded-lg shadow-md">
            </div>
            <div class="md:w-1/3">
                <div class="space-y-4">
                    <div>
                        <h4 class="font-semibold text-gray-700">Técnica</h4>
                        <p id="artworkTechnique" class="text-gray-600"></p>
                        <input type="text" id="editArtworkTechnique" class="hidden mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-700">Año</h4>
                        <p id="artworkYear" class="text-gray-600"></p>
                        <input type="text" id="editArtworkYear" class="hidden mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-700">Descripción</h4>
                        <p id="artworkDescription" class="text-gray-600 whitespace-pre-line"></p>
                        <textarea id="editArtworkDescription" rows="3" class="hidden mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                    </div>
                    <div id="editArtworkButtons" class="hidden mt-4">
                        <button id="saveArtworkChanges" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Guardar cambios
                        </button>
                        <button id="cancelArtworkEdit" class="ml-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancelar
                        </button>
                    </div>
                    <div id="editArtworkToggle" class="mt-4">
                        <button id="toggleArtworkEdit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Editar obra
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Panoramic Image Edit Modal --}}
<div id="panoramicImageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 100;">
    <div class="relative top-20 mx-auto p-5 border max-w-lg w-full shadow-lg rounded-md bg-white">
        <!-- Modal Header -->
        <div class="flex justify-between items-center pb-3">
            <h3 class="text-lg font-bold">Cambiar Imagen Panorámica</h3>
            <button onclick="closePanoramicImageModal()" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="panoramicImageForm" action="{{ route('profile.update.panoramic') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label for="panoramic_image" class="block text-sm font-medium text-gray-700">Nueva Imagen Panorámica</label>
                    <input type="file" 
                           name="panoramic_image" 
                           id="panoramic_image" 
                           accept="image/*"
                           class="mt-1 block w-full text-sm text-gray-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-md file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-indigo-50 file:text-indigo-700
                                  hover:file:bg-indigo-100">
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" 
                            onclick="closePanoramicImageModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Guardar
                    </button>
                </div>
            </div>
        </form>
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

@endsection

@push('scripts')
<script>
    // Get modals and buttons
    const editProfileModal = document.getElementById('editProfileModal');
    const addArtworkModal = document.getElementById('addArtworkModal');
    const openEditProfileModalButton = document.getElementById('openEditProfileModal');
    const openAddArtworkModalButton = document.getElementById('openAddArtworkModal');

    // Function to open modal and load content
    async function openModal(modalElement, modalBodyElement, fetchUrl) {
        // Show loading message
        modalBodyElement.innerHTML = '<p>Cargando formulario...</p>';
        modalElement.classList.remove('hidden');

        // Fetch form content via AJAX
        try {
            console.log('Fetching URL:', fetchUrl);
            const response = await fetch(fetchUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html, application/xhtml+xml',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                credentials: 'same-origin'
            });
            console.log('Response status:', response.status);
            const data = await response.text();
            console.log('Response data:', data);

            if (response.ok) {
                modalBodyElement.innerHTML = data;
                
                // Añadir manejador de envío del formulario
                const form = modalBodyElement.querySelector('form');
                if (form) {
                    form.addEventListener('submit', async function(e) {
                        e.preventDefault();
                        
                        try {
                            const formData = new FormData(form);
                            const response = await fetch(form.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                                },
                                credentials: 'same-origin'
                            });

                            const result = await response.json();

                            if (response.ok) {
                                // Mostrar mensaje de éxito
                                modalBodyElement.innerHTML = `
                                    <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">
                                        <p>${result.message || 'Operación completada correctamente.'}</p>
                                    </div>
                                `;
                                
                                // Recargar la página después de 2 segundos
                                setTimeout(() => {
                                    window.location.reload();
                                }, 2000);
                            } else {
                                // Mostrar errores de validación
                                if (result.errors) {
                                    // Limpiar errores anteriores
                                    const errorMessages = modalBodyElement.querySelectorAll('.text-red-600');
                                    errorMessages.forEach(el => el.remove());
                                    
                                    // Mostrar nuevos errores
                                    Object.keys(result.errors).forEach(field => {
                                        const input = form.querySelector(`[name="${field}"]`);
                                        if (input) {
                                            input.classList.add('border-red-500');
                                            const errorDiv = document.createElement('p');
                                            errorDiv.className = 'mt-1 text-sm text-red-600';
                                            errorDiv.textContent = result.errors[field][0];
                                            input.parentNode.appendChild(errorDiv);
                                        }
                                    });
                                } else {
                                    modalBodyElement.innerHTML = `
                                        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
                                            <p>${result.message || 'Error al procesar la solicitud. Por favor, inténtalo de nuevo.'}</p>
                                        </div>
                                    `;
                                }
                            }
                        } catch (error) {
                            console.error('Error submitting form:', error);
                            modalBodyElement.innerHTML = `
                                <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
                                    <p>Error al procesar la solicitud. Por favor, inténtalo de nuevo.</p>
                                </div>
                            `;
                        }
                    });
                }

                // Añadir manejador para los botones de cancelar
                const cancelButtons = modalBodyElement.querySelectorAll('.close-modal');
                cancelButtons.forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        closeModal(modalElement);
                    });
                });
            } else {
                modalBodyElement.innerHTML = `<p class="text-red-600">Error al cargar el formulario (${response.status}): ${response.statusText}</p>`;
                console.error('Error loading form:', response.statusText);
            }
        } catch (error) {
            modalBodyElement.innerHTML = '<p class="text-red-600">Error de red al cargar el formulario.</p>';
            console.error('Network error loading form:', error);
        }
    }

    // Function to close modal
    function closeModal(modalElement) {
        modalElement.classList.add('hidden');
        const modalBody = modalElement.querySelector('.modal-body, [id$="ModalBody"]');
        if (modalBody) {
            modalBody.innerHTML = '<p>Cargando formulario...</p>';
        }
    }

    // Event listener for Edit Profile button
    if (openEditProfileModalButton) {
        openEditProfileModalButton.addEventListener('click', function(event) {
            event.stopPropagation();
            const url = '{{ route('profile.edit') }}';
            console.log('Generated URL:', url);
            openModal(editProfileModal, document.getElementById('editProfileModalBody'), url);
        });
    }

    // Event listener for Add Artwork button
    if (openAddArtworkModalButton) {
        openAddArtworkModalButton.addEventListener('click', function(event) {
            event.stopPropagation();
            openModal(addArtworkModal, document.getElementById('addArtworkModalBody'), '{{ route('artworks.create') }}');
        });
    }

    // Close modals when clicking outside of them
    window.addEventListener('click', function(event) {
        // Verificar si el clic fue fuera del contenido del modal (no solo el overlay)
        const clickedInsideEditModalContent = editProfileModal.contains(event.target) && event.target !== editProfileModal;
        const clickedInsideAddModalContent = addArtworkModal.contains(event.target) && event.target !== addArtworkModal;

        if (event.target === editProfileModal && !clickedInsideEditModalContent) {
            closeModal(editProfileModal);
        }
        if (event.target === addArtworkModal && !clickedInsideAddModalContent) {
            closeModal(addArtworkModal);
        }
    });

    // Funciones para el modal de vista de obra
    function openArtworkModal(artworkId) {
        const modal = document.getElementById('artworkViewModal');
        modal.classList.remove('hidden');
        
        // Cargar los datos de la obra
        fetch(`/artworks/${artworkId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('artworkTitle').textContent = data.title;
                document.getElementById('artworkImage').src = data.image_url;
                document.getElementById('artworkImage').alt = data.title;
                document.getElementById('artworkTechnique').textContent = data.technique;
                document.getElementById('artworkYear').textContent = data.year;
                document.getElementById('artworkDescription').textContent = data.description;
                
                // Si el usuario es el artista, mostrar el botón de editar
                if (data.is_owner) {
                    document.getElementById('editArtworkToggle').classList.remove('hidden');
                    // Guardar el ID de la obra para la edición
                    document.getElementById('editArtworkToggle').setAttribute('data-artwork-id', artworkId);
                } else {
                    document.getElementById('editArtworkToggle').classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    function closeArtworkModal() {
        const modal = document.getElementById('artworkViewModal');
        modal.classList.add('hidden');
        // Resetear el estado de edición
        toggleEditMode(false);
    }

    // Función para alternar el modo de edición
    function toggleEditMode(show) {
        const techniqueText = document.getElementById('artworkTechnique');
        const techniqueInput = document.getElementById('editArtworkTechnique');
        const yearText = document.getElementById('artworkYear');
        const yearInput = document.getElementById('editArtworkYear');
        const descriptionText = document.getElementById('artworkDescription');
        const descriptionInput = document.getElementById('editArtworkDescription');
        const editButtons = document.getElementById('editArtworkButtons');
        const toggleButton = document.getElementById('toggleArtworkEdit');

        if (show) {
            // Cambiar a modo edición
            techniqueText.classList.add('hidden');
            techniqueInput.classList.remove('hidden');
            techniqueInput.value = techniqueText.textContent;
            
            yearText.classList.add('hidden');
            yearInput.classList.remove('hidden');
            yearInput.value = yearText.textContent;
            
            descriptionText.classList.add('hidden');
            descriptionInput.classList.remove('hidden');
            descriptionInput.value = descriptionText.textContent;
            
            editButtons.classList.remove('hidden');
            toggleButton.classList.add('hidden');
        } else {
            // Cambiar a modo visualización
            techniqueText.classList.remove('hidden');
            techniqueInput.classList.add('hidden');
            
            yearText.classList.remove('hidden');
            yearInput.classList.add('hidden');
            
            descriptionText.classList.remove('hidden');
            descriptionInput.classList.add('hidden');
            
            editButtons.classList.add('hidden');
            toggleButton.classList.remove('hidden');
        }
    }

    // Event listeners para los botones de edición
    document.getElementById('toggleArtworkEdit').addEventListener('click', function() {
        toggleEditMode(true);
    });

    document.getElementById('cancelArtworkEdit').addEventListener('click', function() {
        toggleEditMode(false);
    });

    document.getElementById('saveArtworkChanges').addEventListener('click', function() {
        const artworkId = document.getElementById('editArtworkToggle').getAttribute('data-artwork-id');
        
        if (!artworkId) {
            showSuccessModal('No se pudo identificar la obra.');
            return;
        }

        const technique = document.getElementById('editArtworkTechnique').value;
        const year = document.getElementById('editArtworkYear').value;
        const description = document.getElementById('editArtworkDescription').value;

        fetch(`/artworks/${artworkId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                technique: technique,
                year: year,
                description: description
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar los textos en la modal
                document.getElementById('artworkTechnique').textContent = technique;
                document.getElementById('artworkYear').textContent = year;
                document.getElementById('artworkDescription').textContent = description;
                
                // Volver al modo visualización
                toggleEditMode(false);
                
                // Mostrar mensaje de éxito
                showSuccessModal('Cambios guardados con éxito');
            } else {
                showSuccessModal(data.message || 'Error al guardar los cambios');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showSuccessModal('Error al guardar los cambios');
        });
    });

    // Funciones para el modal de imagen panorámica
    function openPanoramicImageModal() {
        const modal = document.getElementById('panoramicImageModal');
        modal.classList.remove('hidden');
    }

    function closePanoramicImageModal() {
        const modal = document.getElementById('panoramicImageModal');
        modal.classList.add('hidden');
    }

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

    // Manejar el envío del formulario de imagen panorámica
    document.getElementById('panoramicImageForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = 'Guardando...';
        
        try {
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (response.ok) {
                // Actualizar la imagen en la página
                const panoramicImage = document.querySelector('.w-full.rounded-lg.overflow-hidden.shadow-lg img');
                if (panoramicImage) {
                    panoramicImage.src = data.image_url;
                }
                
                // Cerrar el modal de edición
                closePanoramicImageModal();
                
                // Mostrar modal de éxito
                showSuccessModal('Imagen panorámica actualizada correctamente');
            } else {
                throw new Error(data.message || 'Error al actualizar la imagen panorámica');
            }
        } catch (error) {
            console.error('Error:', error);
            showSuccessModal(error.message || 'Error al actualizar la imagen panorámica');
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = 'Guardar';
        }
    });

    // Cerrar el modal de éxito al hacer clic fuera
    window.addEventListener('click', function(event) {
        const successModal = document.getElementById('successModal');
        if (event.target === successModal) {
            closeSuccessModal();
        }
    });
</script>
@endpush 