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