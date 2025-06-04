@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('header')
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Editar Usuario') }}
            </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-2xl font-semibold mb-6">Editar Usuario: {{ $user->name }}</h2>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

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

                        <div class="mt-6">
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                Guardar Cambios
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="ml-4 text-gray-600 hover:text-gray-900">
                                Volver a la lista
                            </a>
                        </div>
                    </form>

                    <!-- Sección de Obras del Usuario -->
                    <div class="mt-12">
                        <h3 class="text-xl font-semibold mb-4">Obras del Usuario</h3>
                        
                        @if($user->artworks->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($user->artworks as $artwork)
                                    <div class="bg-white border rounded-lg overflow-hidden shadow-sm">
                                        @if($artwork->image_path)
                                            <img src="{{ asset('storage/' . $artwork->image_path) }}" 
                                                 alt="{{ $artwork->title }}" 
                                                 class="w-full h-48 object-cover">
                                        @endif
                                        <div class="p-4">
                                            <h4 class="font-semibold text-lg mb-2">{{ $artwork->title }}</h4>
                                            <p class="text-gray-600 mb-2">Técnica: {{ $artwork->technique }}</p>
                                            <p class="text-gray-600 mb-4">Año: {{ $artwork->year }}</p>
                                            
                                            <form action="{{ route('admin.users.artworks.delete', ['user' => $user, 'artwork' => $artwork]) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta obra?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                                                    Eliminar Obra
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-600">Este usuario no tiene obras subidas.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 