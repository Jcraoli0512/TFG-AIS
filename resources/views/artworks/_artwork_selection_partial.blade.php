<div class="space-y-4">
    @forelse(auth()->user()->artworks as $artwork)
        <label class="flex items-center space-x-3">
            <input type="checkbox" name="selected_artworks[]" value="{{ $artwork->id }}" class="form-checkbox h-5 w-5 text-indigo-600">
            <span class="text-gray-900">{{ $artwork->title }}</span>
        </label>
    @empty
        <p class="text-gray-700">No tienes obras disponibles para seleccionar.</p>
    @endforelse
</div> 