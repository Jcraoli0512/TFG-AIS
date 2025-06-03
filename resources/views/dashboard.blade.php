<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Sección de Usuarios -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold mb-4">Usuarios</h3>
                            <div class="space-y-2">
                                <a href="{{ route('admin.users') }}" class="block text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    Gestionar Usuarios
                                </a>
                                <a href="#" class="block text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    Estadísticas de Usuarios
                                </a>
                            </div>
                        </div>

                        <!-- Sección de Contenido -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold mb-4">Contenido</h3>
                            <div class="space-y-2">
                                <a href="{{ route('gallery') }}" class="block text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    Galería
                                </a>
                                <a href="#" class="block text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    Gestión de Contenido
                                </a>
                            </div>
                        </div>

                        <!-- Sección de Configuración -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold mb-4">Configuración</h3>
                            <div class="space-y-2">
                                <a href="{{ route('profile.edit') }}" class="block text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    Perfil
                                </a>
                                <a href="#" class="block text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    Configuración del Sistema
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
