@extends('layouts.app')

@section('title', 'Galería de Obras - Art Indie Space')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-center text-gray-800 mb-10">Explora todas las obras de arte</h1>

        <div id="artworks-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($artworks as $artwork)
                <div class="group cursor-pointer rounded-lg overflow-hidden shadow-md bg-white hover:shadow-lg transition-shadow duration-300" onclick="openArtworkModal({{ $artwork->id }})">
                    <img src="{{ $artwork->image_path ? asset('storage/' . $artwork->image_path) : '#' }}" alt="{{ $artwork->title }}" class="w-full h-64 object-cover transition-transform duration-300 group-hover:scale-105">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg text-gray-900 mb-2">{{ $artwork->title }}</h3>
                        <p class="text-sm text-gray-600">Por: 
                            @if(Auth::check())
                                <a href="{{ route('profile.show', ['user' => $artwork->user->id]) }}" 
                                   onclick="event.stopPropagation()" 
                                   class="text-gray-700 hover:text-gray-900 transition-colors duration-300 font-medium">{{ $artwork->user->name }}</a>
                            @else
                                <a href="{{ route('login', ['redirect' => route('profile.show', ['user' => $artwork->user->id])]) }}" 
                                   onclick="event.stopPropagation()" 
                                   class="text-gray-700 hover:text-gray-900 transition-colors duration-300 font-medium">{{ $artwork->user->name }}</a>
                            @endif
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Modal de Detalles de Obra (reutilizado del perfil) --}}
        <div id="artworkViewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 100;">
            <div class="relative top-20 mx-auto p-5 border max-w-4xl w-full shadow-lg rounded-md bg-white">
                <!-- Modal Header -->
                <div class="flex justify-between items-center pb-3">
                    <h3 class="text-2xl font-bold text-gray-900" id="artworkTitle"></h3>
                    <button onclick="closeArtworkModal()" class="text-gray-500 hover:text-gray-700 transition-colors duration-300">
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
                                <h4 class="font-semibold text-gray-900">Técnica</h4>
                                <p id="artworkTechnique" class="text-gray-600"></p>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Año</h4>
                                <p id="artworkYear" class="text-gray-600"></p>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Descripción</h4>
                                <p id="artworkDescription" class="text-gray-600 whitespace-pre-line"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
<script>
    const isAuthenticated = {{ Auth::check() ? 'true' : 'false' }};

    function openArtworkModal(artworkId) {
        const modal = document.getElementById('artworkViewModal');
        modal.classList.remove('hidden');
        
        fetch(`/artworks/${artworkId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('artworkTitle').textContent = data.title;
                document.getElementById('artworkImage').src = data.image_url || '#';
                document.getElementById('artworkImage').alt = data.title;
                document.getElementById('artworkTechnique').textContent = data.technique || 'No especificada';
                document.getElementById('artworkYear').textContent = data.year || 'No especificado';
                document.getElementById('artworkDescription').textContent = data.description || 'Sin descripción disponible';
            })
            .catch(error => {
                console.error('Error al cargar los detalles de la obra:', error);
            });
    }

    function closeArtworkModal() {
        const modal = document.getElementById('artworkViewModal');
        modal.classList.add('hidden');
    }
</script>
@endpush 