@extends('layouts.app')

@section('title', 'Artistas - Art Indie Space')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-center text-gray-800 mb-10">Descubre a nuestros artistas</h1>

        <div class="max-w-xl mx-auto mb-10">
            <form action="{{ route('artists.index') }}" method="GET" class="flex">
                <input type="text" name="search" placeholder="Buscar artistas por nombre..." 
                       value="{{ request('search') }}"
                       class="flex-grow px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-gray-700">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-r-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Buscar
                </button>
            </form>
        </div>

        <div id="artists-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($artists as $artist)
                <div class="bg-white rounded-lg shadow-md overflow-hidden text-center p-4 cursor-pointer" onclick="openArtistModal({{ $artist->id }})">
                    <img src="{{ $artist->profile_photo_url }}" alt="Foto de perfil de {{ $artist->name }}" 
                         class="w-32 h-32 object-cover rounded-full mx-auto mb-4 border-2 border-indigo-500 p-1">
                    <h3 class="text-xl font-semibold text-gray-800 hover:text-indigo-600 transition-colors duration-200">{{ $artist->name }}</h3>
                    <p class="text-gray-600 text-sm mb-2">{{ $artist->artworks_count }} obras</p>
                    <p class="text-gray-700 text-sm line-clamp-3">{{ $artist->biography ?: 'Biografía no disponible.' }}</p>
                </div>
            @empty
                <p class="col-span-full text-center text-gray-600">No se encontraron artistas.</p>
            @endforelse
        </div>

        {{-- Puedes añadir aquí enlaces de paginación si usas paginate() en el controlador --}}
        <div class="mt-10">
            {{ $artists->links() }}
        </div>
    </div>

    {{-- Artist Details Modal --}}
    <div id="artistModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 100;">
        <div class="relative top-20 mx-auto p-5 border max-w-4xl w-full shadow-lg rounded-md bg-white">
            <!-- Modal Header -->
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-2xl font-bold" id="modalArtistName"></h3>
                <button onclick="closeArtistModal()" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="flex flex-col md:flex-row gap-6 mb-6">
                <div class="md:w-1/3 flex-shrink-0">
                    <img id="modalArtistPhoto" src="" alt="Foto de perfil del artista" class="w-full h-auto object-cover rounded-lg shadow-md mb-4">
                    <p class="text-center">
                        @auth
                            <a id="modalArtistProfileLink" href="#" class="text-indigo-600 hover:text-indigo-800 font-semibold transition-colors duration-200">Ver perfil completo</a>
                        @else
                            <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold transition-colors duration-200">Iniciar sesión para ver perfil</a>
                        @endauth
                    </p>
                </div>
                <div class="md:w-2/3">
                    <h4 class="font-semibold text-gray-700 mb-2">Biografía</h4>
                    <p id="modalArtistBiography" class="text-gray-600 whitespace-pre-line"></p>
                </div>
            </div>

            <h4 class="text-xl font-semibold text-gray-800 mb-4">Obras del artista</h4>
            <div class="swiper myArtistArtworksSwiper rounded-lg p-4">
                <div class="swiper-wrapper" id="modalArtistArtworksCarousel">
                    {{-- Artworks will be loaded here by JavaScript --}}
                </div>
                {{-- Add Pagination --}}
                {{-- <div class="swiper-pagination"></div> --}}
                {{-- Add Navigation --}}
                {{-- <div class="swiper-button-next"></div> --}}
                {{-- <div class="swiper-button-prev"></div> --}}
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
@endpush

@push('scripts')
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
        const artistModal = document.getElementById('artistModal');
        let artistArtworksSwiper = null; // Variable para almacenar la instancia de Swiper

        async function openArtistModal(artistId) {
            console.log('Opening modal for artist:', artistId); // Debug log
            artistModal.classList.remove('hidden');

            try {
                // Usar la URL base correcta
                const baseUrl = window.location.origin;
                const response = await fetch(`${baseUrl}/api/artists/${artistId}`);
                
                console.log('API Response status:', response.status); // Debug log
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('API Error:', errorText); // Debug log
                    throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
                }
                
                const data = await response.json();
                console.log('Artist data received:', data); // Debug log

                // Actualizar la información del artista
                document.getElementById('modalArtistName').textContent = data.name;
                
                // Manejar la URL de la foto de perfil
                const profilePhotoUrl = data.profile_photo_url;
                console.log('Profile photo URL:', profilePhotoUrl); // Debug log
                document.getElementById('modalArtistPhoto').src = profilePhotoUrl || 'https://via.placeholder.com/300x300?text=No+Photo';
                document.getElementById('modalArtistPhoto').onerror = function() {
                    console.error('Error loading profile photo'); // Debug log
                    this.src = 'https://via.placeholder.com/300x300?text=Error+Loading+Photo';
                };
                
                document.getElementById('modalArtistBiography').textContent = data.biography || 'Biografía no disponible.';
                
                const profileLink = document.getElementById('modalArtistProfileLink');
                if (profileLink) {
                    profileLink.href = `${baseUrl}/profile/${data.id}`;
                }

                // Limpiar carrusel anterior
                const artworksContainer = document.getElementById('modalArtistArtworksCarousel');
                artworksContainer.innerHTML = '';

                if (data.artworks && data.artworks.length > 0) {
                    console.log('Loading artworks:', data.artworks.length); // Debug log
                    
                    data.artworks.forEach(artwork => {
                        console.log('Processing artwork:', artwork); // Debug log
                        
                        const artworkSlide = `
                            <div class="swiper-slide">
                                <div class="relative group rounded-lg overflow-hidden h-64">
                                    <img src="${artwork.image_path || 'https://via.placeholder.com/300x200?text=No+Image'}" 
                                         alt="${artwork.title}" 
                                         class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                         onerror="this.src='https://via.placeholder.com/300x200?text=Error+Loading+Image'">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-300 flex items-center justify-center p-4">
                                        <div class="text-white opacity-0 group-hover:opacity-100 text-center">
                                            <h3 class="font-semibold text-lg mb-1">${artwork.title}</h3>
                                            <p class="text-sm">${artwork.technique || ''} ${artwork.year ? `- ${artwork.year}` : ''}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        artworksContainer.insertAdjacentHTML('beforeend', artworkSlide);
                    });

                    // Destruir la instancia existente de Swiper si la hay
                    if (artistArtworksSwiper) {
                        artistArtworksSwiper.destroy(true, true);
                    }

                    // Inicializar Swiper
                    artistArtworksSwiper = new Swiper('.myArtistArtworksSwiper', {
                        slidesPerView: 1,
                        spaceBetween: 20,
                        loop: true,
                        autoplay: {
                            delay: 1000, // Retraso de 1 segundo antes de iniciar la siguiente transición
                            disableOnInteraction: false, // No detener el autoplay al interactuar
                            reverseDirection: false, // Moverse en una dirección constante
                        },
                        speed: 5000, // Velocidad de la transición (5 segundos)
                        breakpoints: {
                            640: {
                                slidesPerView: 2,
                                spaceBetween: 20,
                            },
                            768: {
                                slidesPerView: 3,
                                spaceBetween: 30,
                            },
                            1024: {
                                slidesPerView: 4,
                                spaceBetween: 40,
                            },
                        },
                    });
                } else {
                    console.log('No artworks found for artist'); // Debug log
                    artworksContainer.innerHTML = '<p class="text-center text-gray-600 col-span-full">Este artista aún no ha añadido ninguna obra.</p>';
                }

            } catch (error) {
                console.error('Error fetching artist data:', error);
                // Mostrar mensaje de error en la modal
                document.getElementById('modalArtistName').textContent = 'Error al cargar los datos del artista';
                document.getElementById('modalArtistBiography').textContent = 'Por favor, intenta de nuevo más tarde.';
                document.getElementById('modalArtistPhoto').src = 'https://via.placeholder.com/300x300?text=Error';
                document.getElementById('modalArtistArtworksCarousel').innerHTML = 
                    '<p class="text-center text-red-600 col-span-full">No se pudieron cargar las obras del artista. Por favor, intenta de nuevo más tarde.</p>';
            }
        }

        function closeArtistModal() {
            artistModal.classList.add('hidden');
            if (artistArtworksSwiper) {
                artistArtworksSwiper.destroy(true, true); // Destruir la instancia de Swiper al cerrar la modal
                artistArtworksSwiper = null;
            }
        }

        // Close modal when clicking outside of it
        window.addEventListener('click', function(event) {
            if (event.target === artistModal) {
                closeArtistModal();
            }
        });
    </script>
@endpush 