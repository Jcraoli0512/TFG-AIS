<form id="editUserForm" action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-4">
    @csrf
    @method('PUT')

    {{-- Información del usuario --}}
    <div class="bg-gray-50 p-4 rounded-lg mb-6">
        <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
            <div>
                <p class="font-medium">Fecha de registro:</p>
                <p>{{ $user->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <p class="font-medium">Última actividad:</p>
                <p>{{ $user->last_active_at ? $user->last_active_at->format('d/m/Y H:i') : 'Nunca' }}</p>
            </div>
        </div>
    </div>

    {{-- Campos del formulario --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="role" class="block text-sm font-medium text-gray-700">Rol</label>
            <select name="role" id="role" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="artist" {{ old('role', $user->role) === 'artist' ? 'selected' : '' }}>Artista</option>
                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrador</option>
            </select>
            @error('role')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="is_active" class="block text-sm font-medium text-gray-700">Estado</label>
            <select name="is_active" id="is_active" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="1" {{ old('is_active', $user->is_active) ? 'selected' : '' }}>Activo</option>
                <option value="0" {{ old('is_active', $user->is_active) ? '' : 'selected' }}>Inactivo</option>
            </select>
            @error('is_active')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Sección de Imagen Panorámica --}}
    <div class="mt-8">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Imagen Panorámica</h3>
        <div class="relative">
            @if($user->panoramic_image)
                <div class="relative group">
                    <img src="{{ asset('storage/' . $user->panoramic_image) }}" 
                         alt="Imagen panorámica" 
                         class="w-full h-48 object-cover rounded-lg">
                    <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded-lg flex items-center justify-center">
                        <button type="button" 
                                onclick="handleDeletePanoramic({{ $user->id }})"
                                class="text-white bg-red-600 hover:bg-red-700 px-4 py-2 rounded-md text-sm">
                            Eliminar Imagen
                        </button>
                    </div>
                </div>
            @else
                <p class="text-gray-500">No hay imagen panorámica</p>
            @endif
        </div>
    </div>

    {{-- Sección de Obras del Usuario --}}
    @if($user->artworks->count() > 0)
    <div class="mt-8">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Obras del Usuario</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($user->artworks as $artwork)
            <div class="relative group">
                <img src="{{ $artwork->image_path ? asset('storage/' . $artwork->image_path) : asset('img/placeholder.jpg') }}" 
                     alt="{{ $artwork->title }}" 
                     class="w-full h-48 object-cover rounded-lg">
                <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded-lg flex flex-col items-center justify-center p-4">
                    <h4 class="text-white font-medium mb-2">{{ $artwork->title }}</h4>
                    <p class="text-white text-sm mb-2">{{ $artwork->technique }} - {{ $artwork->year }}</p>
                    <button type="button" 
                            onclick="handleDeleteArtwork({{ $user->id }}, {{ $artwork->id }})"
                            class="text-white bg-red-600 hover:bg-red-700 px-4 py-2 rounded-md text-sm">
                        Eliminar Obra
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="flex justify-end space-x-3 pt-4">
        <button type="button" onclick="closeEditModal()" 
                class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
            Cancelar
        </button>
        <button type="submit" 
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Guardar Cambios
        </button>
    </div>
</form> 