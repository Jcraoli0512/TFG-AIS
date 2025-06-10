@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
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
            </div>
            
            {{-- Profile Photo and Social Media --}}
            <div class="flex-shrink-0 text-center">
                <img src="{{ $user->profile_photo_url }}" alt="Foto de perfil de {{ $user->name }}" class="w-48 h-48 object-cover rounded-lg shadow-md mb-4">
                
                {{-- Redes Sociales --}}
                @php
                    $socialLinks = [
                        'instagram' => ['icon' => 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z', 'label' => 'Instagram'],
                        'twitter' => ['icon' => 'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z', 'label' => 'Twitter'],
                        'tiktok' => ['icon' => 'M12.53.02C13.84 0 15.14.01 16.44 0c.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z', 'label' => 'TikTok'],
                        'youtube' => ['icon' => 'M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z', 'label' => 'YouTube'],
                        'pinterest' => ['icon' => 'M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.878-2.878-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001.012.001z', 'label' => 'Pinterest'],
                        'linkedin' => ['icon' => 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z', 'label' => 'LinkedIn']
                    ];
                    
                    $hasSocialLinks = false;
                    foreach ($socialLinks as $field => $info) {
                        if ($user->$field) {
                            $hasSocialLinks = true;
                            break;
                        }
                    }
                @endphp
                
                @if($hasSocialLinks)
                    <div class="flex justify-center gap-2">
                        @foreach($socialLinks as $field => $info)
                            @if($user->$field)
                                <a href="{{ $user->$field }}" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors duration-200"
                                   title="{{ $info['label'] }}">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="{{ $info['icon'] }}"/>
                                    </svg>
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif
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
                                                <button type="button" 
                                                        onclick="showConfirmationModal('¿Eliminar obra?', '¿Estás seguro de que deseas eliminar esta obra? Esta acción no se puede deshacer.', '{{ $artwork->id }}', 'deleteArtwork')"
                                                        class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-sm rounded-md hover:bg-red-700">
                                                    Eliminar
                                                </button>
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
    <div class="relative top-10 mx-auto p-5 border max-w-2xl w-full shadow-lg rounded-md bg-white max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="pb-3 flex-shrink-0">
            <h3 class="text-lg font-bold">Editar Perfil</h3>
        </div>

        <!-- Modal Body - Form will be loaded here -->
        <div id="editProfileModalBody" class="flex-1 overflow-y-auto">
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

{{-- Confirmation Modal --}}
<div id="confirmationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
    <div class="relative mx-auto p-6 border w-11/12 max-w-md shadow-xl rounded-lg bg-white transform transition-all">
        <div class="flex items-center justify-center mb-4">
            <div id="confirmationModalIcon" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4">
                <!-- Icon will be set dynamically -->
            </div>
        </div>
        <div class="text-center">
            <h3 class="text-lg font-semibold text-gray-900 mb-2" id="confirmationModalTitle"></h3>
            <p class="text-sm text-gray-600" id="confirmationModalMessage"></p>
        </div>
        <div class="flex justify-center space-x-3 mt-6">
            <button id="cancelConfirmationButton" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                Cancelar
            </button>
            <button id="confirmActionButton" class="px-4 py-2 text-sm font-medium text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2">
                Confirmar
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
        console.log('openModal llamado con URL:', fetchUrl);
        
        // Show loading message
        modalBodyElement.innerHTML = '<p>Cargando formulario...</p>';
        modalElement.classList.remove('hidden');
        console.log('Modal mostrado');

        // Fetch form content via AJAX
        try {
            console.log('Iniciando fetch a:', fetchUrl);
            const response = await fetch(fetchUrl, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html, application/xhtml+xml',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Cache-Control': 'no-cache'
                },
                credentials: 'include'
            });
            console.log('Response status:', response.status);
            
            const data = await response.text();
            console.log('Response data length:', data.length);

            if (response.ok) {
                console.log('Response OK, insertando HTML');
                modalBodyElement.innerHTML = data;
                console.log('HTML insertado correctamente');
                
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
                console.error('Response not OK:', response.status, response.statusText);
                modalBodyElement.innerHTML = `<p class="text-red-600">Error al cargar el formulario (${response.status}): ${response.statusText}</p>`;
            }
        } catch (error) {
            console.error('Error en fetch:', error);
            modalBodyElement.innerHTML = '<p class="text-red-600">Error de red al cargar el formulario.</p>';
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
            console.log('Botón de añadir obra clickeado');
            const url = '{{ route('artworks.create.form') }}';
            console.log('URL a cargar:', url);
            openModal(addArtworkModal, document.getElementById('addArtworkModalBody'), url);
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

    // Variables para la modal de confirmación
    const confirmationModal = document.getElementById('confirmationModal');
    const confirmationModalTitle = document.getElementById('confirmationModalTitle');
    const confirmationModalMessage = document.getElementById('confirmationModalMessage');
    const confirmationModalIcon = document.getElementById('confirmationModalIcon');
    const cancelConfirmationButton = document.getElementById('cancelConfirmationButton');
    const confirmActionButton = document.getElementById('confirmActionButton');

    let currentAction = null;
    let currentArtworkId = null;

    // Funciones para el modal de confirmación
    function showConfirmationModal(title, message, artworkId, action) {
        // Limpiar el modal antes de configurarlo
        resetConfirmationModal();
        
        if (action === 'deleteArtwork') {
            // Primero verificar si la obra está siendo exhibida
            checkArtworkExhibitions(artworkId).then(hasExhibitions => {
                if (hasExhibitions) {
                    // Si está siendo exhibida, mostrar mensaje de advertencia
                    confirmationModalTitle.textContent = '⚠️ Obra en exhibición';
                    confirmationModalMessage.textContent = 'Esta obra no se puede eliminar porque está siendo exhibida.\n\nPara eliminar esta obra, primero debes cancelar todas sus exhibiciones desde el calendario.';
                    confirmActionButton.style.display = 'none'; // Ocultar botón de eliminar
                    cancelConfirmationButton.textContent = 'Entendido';
                    
                    // Cambiar el icono a advertencia
                    confirmationModalIcon.classList.add('bg-yellow-100');
                    confirmationModalIcon.innerHTML = `
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    `;
                } else {
                    // Si no está siendo exhibida, mostrar confirmación normal
                    confirmationModalTitle.textContent = title;
                    confirmationModalMessage.textContent = message;
                    confirmActionButton.style.display = 'inline-flex'; // Mostrar botón de eliminar
                    cancelConfirmationButton.textContent = 'Cancelar';
                    
                    // Configurar para eliminación
                    confirmActionButton.classList.add('bg-red-600', 'hover:bg-red-700', 'focus:ring-red-300');
                    confirmActionButton.textContent = 'Eliminar';
                    confirmationModalIcon.classList.add('bg-red-100');
                    confirmationModalIcon.innerHTML = `
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    `;
                }
                
                currentArtworkId = artworkId;
                currentAction = action;
                confirmationModal.classList.remove('hidden');
            });
        } else {
            // Para otras acciones, usar el comportamiento normal
            confirmationModalTitle.textContent = title;
            confirmationModalMessage.textContent = message;
            currentArtworkId = artworkId;
            currentAction = action;
            confirmationModal.classList.remove('hidden');
        }
    }

    function closeConfirmationModal() {
        confirmationModal.classList.add('hidden');
        currentAction = null;
        currentArtworkId = null;
    }

    // Event listeners para la modal de confirmación
    cancelConfirmationButton.addEventListener('click', closeConfirmationModal);
    confirmActionButton.addEventListener('click', function() {
        if (currentAction === 'deleteArtwork' && currentArtworkId) {
            deleteArtwork(currentArtworkId);
        }
        closeConfirmationModal();
    });

    // Función para eliminar la obra
    function deleteArtwork(artworkId) {
        fetch(`/artworks/${artworkId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessModal(data.message || 'Obra eliminada correctamente');
                // Recargar la página después de 2 segundos para mostrar los cambios
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                throw new Error(data.message || 'Error al eliminar la obra');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Mostrar mensaje específico si es sobre exhibiciones
            if (error.message.includes('exhibida')) {
                showSuccessModal('⚠️ ' + error.message + '\n\nPara eliminar esta obra, primero debes cancelar todas sus exhibiciones desde el calendario.');
            } else {
                showSuccessModal('❌ ' + error.message);
            }
        });
    }

    // Función para verificar si una obra está siendo exhibida
    function checkArtworkExhibitions(artworkId) {
        return fetch(`/artworks/${artworkId}/check-exhibitions`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => data.has_exhibitions)
        .catch(error => {
            console.error('Error checking exhibitions:', error);
            return false; // En caso de error, asumir que no está exhibida
        });
    }

    // Función para limpiar el modal de confirmación
    function resetConfirmationModal() {
        // Limpiar clases del botón
        confirmActionButton.classList.remove('bg-red-600', 'hover:bg-red-700', 'focus:ring-red-300');
        confirmActionButton.style.display = 'inline-flex';
        confirmActionButton.textContent = 'Confirmar';
        
        // Limpiar clases del icono
        confirmationModalIcon.classList.remove('bg-red-100', 'bg-yellow-100');
        confirmationModalIcon.innerHTML = '';
        
        // Resetear botón cancelar
        cancelConfirmationButton.textContent = 'Cancelar';
        
        // Limpiar variables
        currentArtworkId = null;
        currentAction = null;
    }

    // Event listener para cerrar el modal
    document.getElementById('closeConfirmationModal').addEventListener('click', function() {
        confirmationModal.classList.add('hidden');
        resetConfirmationModal();
    });

    // Event listener para el botón cancelar
    cancelConfirmationButton.addEventListener('click', function() {
        confirmationModal.classList.add('hidden');
        resetConfirmationModal();
    });

    // Event listener para cerrar con Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !confirmationModal.classList.contains('hidden')) {
            confirmationModal.classList.add('hidden');
            resetConfirmationModal();
        }
    });

    // Event listener para cerrar haciendo clic fuera del modal
    confirmationModal.addEventListener('click', function(e) {
        if (e.target === confirmationModal) {
            confirmationModal.classList.add('hidden');
            resetConfirmationModal();
        }
    });
</script>
@endpush 