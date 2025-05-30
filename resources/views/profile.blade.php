@extends('layouts.app')

@section('title', 'Perfil de ' . $user->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        {{-- Top section: Name, Bio, Profile Photo --}}
        <div class="flex flex-col md:flex-row items-center md:items-start gap-8 mb-12">
            {{-- Name and Biography --}}
            <div class="flex-1 text-center md:text-left">
                <h1 class="text-4xl font-bold text-gray-800 mb-4">{{ $user->name }}</h1>
                <p class="text-gray-700 leading-relaxed">{{ $user->biography ?: 'Este usuario aún no ha añadido una biografía.' }}</p>
                 {{-- Optional: Add social media links here if available in the user model --}}

                 {{-- Edit Profile Button (Visible only to the profile owner) --}}
                 @auth
                     @if(auth()->user()->id === $user->id)
                         <div class="mt-6">
                             <button id="openEditProfileModal" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                 Editar Perfil
                             </button>
                         </div>
                     @endif
                 @endauth

            </div>
            {{-- Profile Photo --}}
            <div class="flex-shrink-0">
                 {{-- Use a conditional to check if profile_photo exists --}}
                @if($user->profile_photo)
                    <img src="{{ asset($user->profile_photo) }}" alt="Foto de perfil de {{ $user->name }}" class="w-48 h-48 object-cover rounded-lg shadow-md">
                @else
                    {{-- Placeholder or default image if no profile photo --}}
                    <img src="{{ asset('img_web/default-profile.png') }}" alt="Foto de perfil por defecto" class="w-48 h-48 object-cover rounded-lg shadow-md">
                @endif
            </div>
        </div>

        @if($user->artworks->count() > 0)
            {{-- Artworks Section --}}
            <div class="mb-12">
                <h2 class="text-3xl font-semibold text-gray-800 mb-6">Obras</h2>

                {{-- Add Artwork Button (Visible only to the profile owner) --}}
                 @auth
                     @if(auth()->user()->id === $user->id)
                         <div class="mb-6">
                             <button id="openAddArtworkModal" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                 Añadir Obra
                             </button>
                         </div>
                     @endif
                 @endauth

                {{-- Artworks Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($user->artworks as $artwork)
                        <div class="relative group rounded-lg overflow-hidden shadow-md">
                            <img src="{{ asset($artwork->image_path) }}" alt="{{ $artwork->title }}" class="w-full h-64 object-cover transition-transform duration-300 group-hover:scale-105">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-300 flex items-center justify-center p-4">
                                <div class="text-white opacity-0 group-hover:opacity-100 text-center">
                                    <h3 class="font-semibold text-lg mb-1">{{ $artwork->title }}</h3>
                                    <p class="text-sm">{{ $artwork->technique }} - {{ $artwork->year }}</p>
                                     {{-- Optional: Link to individual artwork page --}}
                                     {{-- <a href="{{ route('artworks.show', $artwork) }}" class="mt-2 inline-block text-indigo-400 hover:text-indigo-600 text-sm font-medium">Ver obra</a> --}}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        @else
            <p class="text-gray-700 text-center mb-12">{{ $user->name }} aún no ha añadido ninguna obra a la plataforma.</p>
             {{-- Add Artwork Button (Visible only to the profile owner, even if no artworks) --}}
             @auth
                 @if(auth()->user()->id === $user->id)
                     <div class="mb-6 text-center">
                         <button id="openAddArtworkModal" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                             Añadir tu primera Obra
                         </button>
                     </div>
                 @endif
             @endauth

        @endif

        {{-- Panoramic Image Section (Placeholder) --}}
        {{-- Replace with dynamic image if available in user model or elsewhere --}}
        <div class="w-full rounded-lg overflow-hidden shadow-lg">
            <img src="https://picsum.photos/1200/400" alt="Imagen Panorámica" class="w-full h-auto object-cover">
        </div>

    </div>
</div>

{{-- Edit Profile Modal --}}
<div id="editProfileModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border max-w-lg w-full shadow-lg rounded-md bg-white">
        <!-- Modal Header -->
        <div class="flex justify-between items-center pb-3">
            <h3 class="text-lg font-bold">Editar Perfil</h3>
            <button class="close-modal text-gray-500 hover:text-gray-700 text-2xl font-semibold">&times;</button>
        </div>

        <!-- Modal Body - Form will be loaded here -->
        <div id="editProfileModalBody">
            <p>Cargando formulario...</p>
        </div>

    </div>
</div>

{{-- Add Artwork Modal --}}
<div id="addArtworkModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border max-w-lg w-full shadow-lg rounded-md bg-white">
        <!-- Modal Header -->
        <div class="flex justify-between items-center pb-3">
            <h3 class="text-lg font-bold">Añadir Obra</h3>
            <button class="close-modal text-gray-500 hover:text-gray-700 text-2xl font-semibold">&times;</button>
        </div>

        <!-- Modal Body - Form will be loaded here -->
        <div id="addArtworkModalBody">
            <p>Cargando formulario...</p>
        </div>

    </div>
</div>

@push('scripts')
<script>
    // Get modals and buttons
    const editProfileModal = document.getElementById('editProfileModal');
    const addArtworkModal = document.getElementById('addArtworkModal');
    const openEditProfileModalButton = document.getElementById('openEditProfileModal');
    const openAddArtworkModalButton = document.getElementById('openAddArtworkModal');
    const closeModalButtons = document.querySelectorAll('.close-modal');

    // Function to open modal and load content
    async function openModal(modalElement, modalBodyElement, fetchUrl) {
        // Show loading message
        modalBodyElement.innerHTML = '<p>Cargando formulario...</p>';
        modalElement.classList.remove('hidden');

        // Fetch form content via AJAX
        try {
            console.log('Fetching URL:', fetchUrl); // Debug log
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
                                        <p>Perfil actualizado correctamente.</p>
                                    </div>
                                `;
                                
                                // Recargar la página después de 2 segundos
                                setTimeout(() => {
                                    window.location.reload();
                                }, 2000);
                            } else {
                                // Mostrar errores de validación
                                const errors = result.errors;
                                Object.keys(errors).forEach(field => {
                                    const input = form.querySelector(`[name="${field}"]`);
                                    if (input) {
                                        input.classList.add('border-red-500');
                                        const errorDiv = document.createElement('p');
                                        errorDiv.className = 'mt-1 text-sm text-red-600';
                                        errorDiv.textContent = errors[field][0];
                                        input.parentNode.appendChild(errorDiv);
                                    }
                                });
                            }
                        } catch (error) {
                            console.error('Error submitting form:', error);
                            modalBodyElement.innerHTML = `
                                <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
                                    <p>Error al actualizar el perfil. Por favor, inténtalo de nuevo.</p>
                                </div>
                            `;
                        }
                    });
                }
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
        // Optional: Clear modal body content on close
        // modalElement.querySelector('.modal-body').innerHTML = '';
    }

    // Event listener for Edit Profile button
    if (openEditProfileModalButton) {
        openEditProfileModalButton.addEventListener('click', function() {
            // Usar la ruta nombrada para asegurar la URL correcta
            const url = '{{ route('profile.edit') }}';
            console.log('Generated URL:', url); // Debug log
            openModal(editProfileModal, document.getElementById('editProfileModalBody'), url);
        });
    }

    // Event listener for Add Artwork button
    if (openAddArtworkModalButton) {
        openAddArtworkModalButton.addEventListener('click', function() {
            openModal(addArtworkModal, document.getElementById('addArtworkModalBody'), '{{ route('artworks.create') }}');
        });
    }

    // Event listeners for close modal buttons
    closeModalButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Find the closest modal parent and close it
            const modal = this.closest('.fixed');
            if (modal) {
                closeModal(modal);
            }
        });
    });

    // Close modals when clicking outside of them
    window.addEventListener('click', function(event) {
        if (event.target === editProfileModal) {
            closeModal(editProfileModal);
        }
         if (event.target === addArtworkModal) {
            closeModal(addArtworkModal);
        }
    });

</script>
@endpush 