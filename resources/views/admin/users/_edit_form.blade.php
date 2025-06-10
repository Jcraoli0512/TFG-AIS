<form id="editUserForm" action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
    @csrf
    @method('PUT')

    {{-- Información del usuario --}}
    <div class="bg-gray-50 p-4 rounded-lg">
        <h4 class="text-md font-semibold text-gray-900 mb-3">Información del Usuario</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
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

    {{-- Información Básica --}}
    <div class="border-b border-gray-200 pb-6">
        <h4 class="text-md font-semibold text-gray-900 mb-4">Información Básica</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
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

            <div>
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

        <div class="mt-4">
            <label for="biography" class="block text-sm font-medium text-gray-700">Descripción / Biografía</label>
            <textarea name="biography" id="biography" rows="4" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('biography', $user->biography) }}</textarea>
            @error('biography')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Redes Sociales --}}
    <div class="border-b border-gray-200 pb-6">
        <h4 class="text-md font-semibold text-gray-900 mb-3">Redes Sociales</h4>
        <p class="text-sm text-gray-600 mb-4">Enlaces donde el usuario muestra su arte</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="instagram" class="block text-sm font-medium text-gray-700">Instagram</label>
                <input type="url" name="instagram" id="instagram" value="{{ old('instagram', $user->instagram) }}" placeholder="https://instagram.com/usuario" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('instagram')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="twitter" class="block text-sm font-medium text-gray-700">Twitter/X</label>
                <input type="url" name="twitter" id="twitter" value="{{ old('twitter', $user->twitter) }}" placeholder="https://twitter.com/usuario" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('twitter')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="tiktok" class="block text-sm font-medium text-gray-700">TikTok</label>
                <input type="url" name="tiktok" id="tiktok" value="{{ old('tiktok', $user->tiktok) }}" placeholder="https://tiktok.com/@usuario" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('tiktok')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="youtube" class="block text-sm font-medium text-gray-700">YouTube</label>
                <input type="url" name="youtube" id="youtube" value="{{ old('youtube', $user->youtube) }}" placeholder="https://youtube.com/@usuario" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('youtube')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="pinterest" class="block text-sm font-medium text-gray-700">Pinterest</label>
                <input type="url" name="pinterest" id="pinterest" value="{{ old('pinterest', $user->pinterest) }}" placeholder="https://pinterest.com/usuario" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('pinterest')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="linkedin" class="block text-sm font-medium text-gray-700">LinkedIn</label>
                <input type="url" name="linkedin" id="linkedin" value="{{ old('linkedin', $user->linkedin) }}" placeholder="https://linkedin.com/in/usuario" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('linkedin')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Sección de Imagen Panorámica --}}
    <div class="border-b border-gray-200 pb-6">
        <h4 class="text-md font-semibold text-gray-900 mb-4">Imagen Panorámica</h4>
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
    <div class="pb-6">
        <h4 class="text-md font-semibold text-gray-900 mb-4">Obras del Usuario ({{ $user->artworks->count() }})</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($user->artworks as $artwork)
            <div class="relative group">
                <img src="{{ $artwork->image_path ? asset('storage/' . $artwork->image_path) : asset('img/placeholder.jpg') }}" 
                     alt="{{ $artwork->title }}" 
                     class="w-full h-48 object-cover rounded-lg">
                <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded-lg flex flex-col items-center justify-center p-4">
                    <h5 class="text-white font-medium mb-2 text-center">{{ $artwork->title }}</h5>
                    <p class="text-white text-sm mb-2 text-center">{{ $artwork->technique }} - {{ $artwork->year }}</p>
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

    {{-- Botones de acción --}}
    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
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