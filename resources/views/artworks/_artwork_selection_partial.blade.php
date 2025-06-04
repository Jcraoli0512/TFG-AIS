<div class="space-y-4">
    @forelse(auth()->user()->artworks as $artwork)
        <label class="flex items-center space-x-3 p-2 border rounded-md hover:bg-gray-100 cursor-pointer">
            <input type="checkbox" name="selected_artworks[]" value="{{ $artwork->id }}" class="form-checkbox h-5 w-5 text-indigo-600">
            <img src="{{ $artwork->image_path ? asset('storage/' . $artwork->image_path) : asset('img/placeholder.jpg') }}" 
                 alt="{{ $artwork->title }}" 
                 class="w-16 h-16 object-cover rounded-md">
            <span class="text-gray-900 flex-1">{{ $artwork->title }}</span>
        </label>
    @empty
        <p class="text-gray-700">No tienes obras disponibles para seleccionar.</p>
    @endforelse
</div> 