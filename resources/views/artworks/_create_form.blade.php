<form action="{{ route('artworks.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
    @csrf

    <div>
        <label for="title" class="block text-sm font-medium text-gray-700">Título de la Obra</label>
        <input type="text" name="title" id="title" required
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        @error('title')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
        <textarea name="description" id="description" rows="3" required
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
        @error('description')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="technique" class="block text-sm font-medium text-gray-700">Técnica</label>
        <input type="text" name="technique" id="technique" required
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        @error('technique')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="year" class="block text-sm font-medium text-gray-700">Año</label>
        <input type="number" name="year" id="year" required min="1900" max="{{ date('Y') }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        @error('year')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="image" class="block text-sm font-medium text-gray-700">Imagen de la Obra</label>
        <input type="file" name="image" id="image" required
               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
        @error('image')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Optional: Select Collection --}}
    {{-- You would need to pass collections to this view if you want this functionality --}}
    {{-- <div>
        <label for="collection_id" class="block text-sm font-medium text-gray-700">Colección (Opcional)</label>
        <select name="collection_id" id="collection_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Selecciona una colección...</option>
            {{-- @foreach($collections as $collection)
                <option value="{{ $collection->id }}">{{ $collection->name }}</option>
            @endforeach --}}
        </select>
         @error('collection_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div> --}}

    <div class="flex justify-end space-x-3">
        <button type="button" class="close-modal inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
            Cancelar
        </button>
        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
            Guardar Obra
        </button>
    </div>
</form> 