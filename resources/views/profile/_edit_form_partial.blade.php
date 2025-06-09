<form method="POST" action="{{ route('profile.update') }}" class="space-y-4" enctype="multipart/form-data">
    @csrf
    @method('PATCH')

    <!-- Información Básica -->
    <div class="border-b pb-4">
        <h4 class="text-md font-semibold text-gray-900 mb-3">Información Básica</h4>
        
        <div class="space-y-3">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="biography" class="block text-sm font-medium text-gray-700">Biografía</label>
                <textarea name="biography" id="biography" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('biography', $user->biography) }}</textarea>
                @error('biography')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Redes Sociales -->
    <div class="border-b pb-4">
        <h4 class="text-md font-semibold text-gray-900 mb-3">Redes Sociales</h4>
        <p class="text-sm text-gray-600 mb-3">Comparte tus enlaces donde muestras tu arte</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label for="instagram" class="block text-sm font-medium text-gray-700">Instagram</label>
                <input type="url" name="instagram" id="instagram" value="{{ old('instagram', $user->instagram) }}" placeholder="https://instagram.com/tu-usuario" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('instagram')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="twitter" class="block text-sm font-medium text-gray-700">Twitter/X</label>
                <input type="url" name="twitter" id="twitter" value="{{ old('twitter', $user->twitter) }}" placeholder="https://twitter.com/tu-usuario" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('twitter')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="tiktok" class="block text-sm font-medium text-gray-700">TikTok</label>
                <input type="url" name="tiktok" id="tiktok" value="{{ old('tiktok', $user->tiktok) }}" placeholder="https://tiktok.com/@tu-usuario" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('tiktok')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="youtube" class="block text-sm font-medium text-gray-700">YouTube</label>
                <input type="url" name="youtube" id="youtube" value="{{ old('youtube', $user->youtube) }}" placeholder="https://youtube.com/@tu-canal" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('youtube')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="pinterest" class="block text-sm font-medium text-gray-700">Pinterest</label>
                <input type="url" name="pinterest" id="pinterest" value="{{ old('pinterest', $user->pinterest) }}" placeholder="https://pinterest.com/tu-usuario" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('pinterest')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="linkedin" class="block text-sm font-medium text-gray-700">LinkedIn</label>
                <input type="url" name="linkedin" id="linkedin" value="{{ old('linkedin', $user->linkedin) }}" placeholder="https://linkedin.com/in/tu-perfil" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('linkedin')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Foto de Perfil -->
    <div>
        <h4 class="text-md font-semibold text-gray-900 mb-3">Foto de Perfil</h4>
        <div class="flex items-center">
            <img src="{{ $user->profile_photo_url }}" alt="Foto de perfil actual" class="h-16 w-16 rounded-full object-cover">
            <input type="file" name="profile_photo" id="profile_photo" accept="image/*" class="ml-4 block w-full text-sm text-gray-500
                file:mr-4 file:py-2 file:px-4
                file:rounded-md file:border-0
                file:text-sm file:font-semibold
                file:bg-indigo-50 file:text-indigo-700
                hover:file:bg-indigo-100">
        </div>
        @error('profile_photo')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        <p class="mt-1 text-sm text-gray-500">Formatos: JPG, PNG, GIF. Máx. 2MB.</p>
    </div>

    <!-- Botones -->
    <div class="flex justify-end space-x-3 pt-4 border-t">
        <button type="button" class="close-modal inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Cancelar
        </button>
        <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Guardar cambios
        </button>
    </div>
</form> 