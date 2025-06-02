<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Art Indie Space')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <!-- Swiper CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css" />
        <link rel="icon" type="image/png" href="{{ asset('img_web/logo.png') }}">
        <style>
            .swiper-button-next,
            .swiper-button-prev {
                color: black !important;
            }
        </style>
        @yield('styles')
    </head>
    <body class="bg-gray-50 min-h-screen flex flex-col">
        <!-- Barra de navegación -->
        <header class="bg-white shadow-sm">
            <nav class="bg-white">
                <div class="container mx-auto px-4">
                    <div class="flex justify-between items-center h-16">
                        <!-- Logo -->
                        <a href="{{ route('dashboard') }}" class="flex items-center">
                            <img src="{{ asset('img_web/logo.png') }}" alt="Logo" class="w-10 h-10">
                        </a>

                        <!-- Menú de navegación -->
                        <div class="hidden md:flex items-center space-x-6">
                            <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-gray-900 {{ request()->routeIs('dashboard') ? 'font-semibold' : '' }}">Inicio</a>
                            <a href="{{ auth()->check() ? route('exhibicion') : route('login') }}" class="text-gray-700 hover:text-gray-900 {{ request()->routeIs('exhibicion') ? 'font-semibold' : '' }}">Exhibición</a>
                            <a href="#" class="text-gray-700 hover:text-gray-900">Galería</a>
                            <a href="{{ auth()->check() ? route('calendar') : route('login') }}" class="text-gray-700 hover:text-gray-900 {{ request()->routeIs('calendar') ? 'font-semibold' : '' }}">Calendario</a>
                            <a href="{{ auth()->check() ? '#' : route('login') }}" class="text-gray-700 hover:text-gray-900">Artistas</a>
                            <a href="{{ auth()->check() ? '#' : route('login') }}" class="text-gray-700 hover:text-gray-900">Nosotros</a>
                        </div>

                        <!-- Menú de usuario -->
                        <div class="flex items-center space-x-4">
                            @auth
                                <div class="relative">
                                    <button id="userMenuButton" type="button" class="flex items-center space-x-2 focus:outline-none">
                                        <img src="{{ Auth::user()->profile_photo_url }}" alt="Foto de perfil" class="w-8 h-8 rounded-full object-cover">
                                        <span class="text-gray-700">{{ Auth::user()->name }}</span>
                                    </button>
                                    <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                        <a href="{{ route('profile.show', ['user' => Auth::user()->id]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Ir a perfil</a>
                                        @if(Auth::user()->isAdmin())
                                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Panel de Administración</a>
                                        @endif
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Cerrar sesión</button>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900">Iniciar sesión</a>
                                <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Registrarse</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </nav>
        </header>

        <!-- Contenido principal -->
        <main class="flex-grow pt-4">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t mt-12 py-6">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <h3 class="font-bold text-lg mb-4">Art Indie Space</h3>
                        <p class="text-gray-600">Tu espacio para descubrir y compartir arte independiente.</p>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg mb-4">Enlaces rápidos</h3>
                        <ul class="space-y-2">
                            <li><a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900">Inicio</a></li>
                            <li><a href="{{ auth()->check() ? route('exhibicion') : route('login') }}" class="text-gray-600 hover:text-gray-900">Exhibición</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-gray-900">Galería</a></li>
                            <li><a href="{{ auth()->check() ? route('calendar') : route('login') }}" class="text-gray-600 hover:text-gray-900">Calendario</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-gray-900">Artistas</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-gray-900">Nosotros</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg mb-4">Legal</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-600 hover:text-gray-900">Términos y condiciones</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-gray-900">Política de privacidad</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-gray-900">Cookies</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg mb-4">Contacto</h3>
                        <ul class="space-y-2">
                            <li class="text-gray-600">Email: info@artindiespace.com</li>
                            <li class="text-gray-600">Teléfono: +34 123 456 789</li>
                            <li class="text-gray-600">Dirección: Calle del Arte, 123</li>
                        </ul>
                    </div>
                </div>
                <div class="mt-8 pt-8 border-t text-center text-gray-600">
                    © {{ date('Y') }} Art Indie Space. Todos los derechos reservados.
                </div>
            </div>
        </footer>

        <!-- Scripts -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Manejar el menú de usuario
                const userMenuButton = document.getElementById('userMenuButton');
                const userDropdown = document.getElementById('userDropdown');
                
                if (userMenuButton && userDropdown) {
                    userMenuButton.onclick = function(event) {
                        event.stopPropagation();
                        userDropdown.classList.toggle('hidden');
                    };
                    
                    document.addEventListener('click', function(event) {
                        if (!userDropdown.classList.contains('hidden')) {
                            userDropdown.classList.add('hidden');
                        }
                    });
                }
            });
        </script>
        @stack('scripts')
         <!-- Swiper JS -->
         <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
    </body>
</html>
