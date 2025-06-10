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

            /* Estilos para el menú móvil */
            #mobileMenu {
                transition: all 0.3s ease-in-out;
                transform-origin: top;
            }

            #mobileMenu.hidden {
                transform: scaleY(0);
                opacity: 0;
            }

            #mobileMenu:not(.hidden) {
                transform: scaleY(1);
                opacity: 1;
            }

            /* Animación para el botón hamburguesa */
            #mobileMenuButton svg {
                transition: transform 0.2s ease-in-out;
            }

            #mobileMenuButton:hover svg {
                transform: scale(1.1);
            }

            /* Mejorar la experiencia táctil en móviles */
            @media (max-width: 767px) {
                #mobileMenu a {
                    min-height: 44px;
                    display: flex;
                    align-items: center;
                }
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
                            <img src="{{ asset('img_web/logo.png') }}" alt="Logo" class="w-14 h-14">
                        </a>

                        <!-- Menú de navegación - Desktop -->
                        <div class="hidden md:flex items-center space-x-6">
                            <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-gray-900 {{ request()->routeIs('dashboard') ? 'font-semibold' : '' }}">Inicio</a>
                            <a href="{{ auth()->check() ? route('exhibicion') : route('login') }}" class="text-gray-700 hover:text-gray-900 {{ request()->routeIs('exhibicion') ? 'font-semibold' : '' }}">Exhibición</a>
                            <a href="/gallery" class="text-gray-700 hover:text-gray-900">Galería</a>
                            <a href="{{ auth()->check() ? route('calendar') : route('login') }}" class="text-gray-700 hover:text-gray-900 {{ request()->routeIs('calendar') ? 'font-semibold' : '' }}">Calendario</a>
                            <a href="{{ route('artists.index') }}" class="text-gray-700 hover:text-gray-900 {{ request()->routeIs('artists.index') ? 'font-semibold' : '' }}">Artistas</a>
                            <a href="{{ route('about') }}" class="text-gray-700 hover:text-gray-900 {{ request()->routeIs('about') ? 'font-semibold' : '' }}">Nosotros</a>
                        </div>

                        <!-- Menú de usuario y botón hamburguesa -->
                        <div class="flex items-center space-x-4">
                            <!-- Botón hamburguesa - Mobile -->
                            <button id="mobileMenuButton" class="md:hidden p-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>

                            @auth
                                <div class="relative">
                                    <button id="userMenuButton" type="button" class="flex items-center space-x-2 focus:outline-none">
                                        <img src="{{ Auth::user()->profile_photo_url }}" alt="Foto de perfil" class="w-12 h-12 rounded-full object-cover">
                                        <span class="text-gray-700 hidden sm:inline">{{ Auth::user()->name }}</span>
                                    </button>
                                    <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                        <a href="{{ route('profile.show', ['user' => Auth::user()->id]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Ir a perfil</a>
                                        @if(Auth::user()->isAdmin())
                                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Panel de Administración</a>
                                            <a href="{{ route('admin.exhibition-requests.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Solicitudes de Exhibición</a>
                                        @endif
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Cerrar sesión</button>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <div class="hidden sm:flex space-x-2">
                                    <a href="{{ route('register') }}" class="bg-gray-100 px-4 py-2 rounded-lg hover:bg-gray-200 transition">Registrarte</a>
                                    <a href="{{ route('login') }}" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">Iniciar Sesión</a>
                                </div>
                            @endauth
                        </div>
                    </div>

                    <!-- Menú móvil -->
                    <div id="mobileMenu" class="hidden md:hidden">
                        <div class="px-2 pt-2 pb-3 space-y-1 bg-white border-t border-gray-200">
                            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 {{ request()->routeIs('dashboard') ? 'bg-gray-100 text-gray-900' : '' }}">Inicio</a>
                            <a href="{{ auth()->check() ? route('exhibicion') : route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 {{ request()->routeIs('exhibicion') ? 'bg-gray-100 text-gray-900' : '' }}">Exhibición</a>
                            <a href="/gallery" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100">Galería</a>
                            <a href="{{ auth()->check() ? route('calendar') : route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 {{ request()->routeIs('calendar') ? 'bg-gray-100 text-gray-900' : '' }}">Calendario</a>
                            <a href="{{ route('artists.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 {{ request()->routeIs('artists.index') ? 'bg-gray-100 text-gray-900' : '' }}">Artistas</a>
                            <a href="{{ route('about') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 {{ request()->routeIs('about') ? 'bg-gray-100 text-gray-900' : '' }}">Nosotros</a>
                            
                            @guest
                                <div class="pt-4 pb-3 border-t border-gray-200">
                                    <div class="flex flex-col space-y-2">
                                        <a href="{{ route('register') }}" class="block px-3 py-2 rounded-md text-base font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 transition">Registrarte</a>
                                        <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium bg-gray-800 text-white hover:bg-gray-700 transition">Iniciar Sesión</a>
                                    </div>
                                </div>
                            @endguest
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
                            <li><a href="/gallery" class="text-gray-600 hover:text-gray-900">Galería</a></li>
                            <li><a href="{{ auth()->check() ? route('calendar') : route('login') }}" class="text-gray-600 hover:text-gray-900">Calendario</a></li>
                            <li><a href="{{ route('artists.index') }}" class="text-gray-600 hover:text-gray-900">Artistas</a></li>
                            <li><a href="{{ route('about') }}" class="text-gray-600 hover:text-gray-900">Nosotros</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg mb-4">Legal</h3>
                        <ul class="space-y-2">
                            <li><a href="#" onclick="event.preventDefault(); openLegalModal('terms')" class="text-gray-600 hover:text-gray-900">Términos y condiciones</a></li>
                            <li><a href="#" onclick="event.preventDefault(); openLegalModal('privacy')" class="text-gray-600 hover:text-gray-900">Política de privacidad</a></li>
                            <li><a href="#" onclick="event.preventDefault(); openLegalModal('cookies')" class="text-gray-600 hover:text-gray-900">Cookies</a></li>
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

        <!-- Modal Legal -->
        <div id="legalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 1000;">
            <div class="relative top-20 mx-auto p-5 border max-w-2xl w-full shadow-lg rounded-md bg-white">
                <!-- Modal Header -->
                <div class="flex justify-between items-center pb-3">
                    <h3 class="text-2xl font-bold" id="legalModalTitle"></h3>
                    <button onclick="closeLegalModal()" class="text-gray-500 hover:text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="mt-4">
                    <div id="legalModalContent" class="text-gray-600 leading-relaxed">
                    </div>
                </div>
            </div>
        </div>

        <!-- Scripts -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Manejar el menú móvil
                const mobileMenuButton = document.getElementById('mobileMenuButton');
                const mobileMenu = document.getElementById('mobileMenu');
                
                if (mobileMenuButton && mobileMenu) {
                    mobileMenuButton.onclick = function(event) {
                        event.stopPropagation();
                        
                        if (mobileMenu.classList.contains('hidden')) {
                            // Mostrar menú
                            mobileMenu.classList.remove('hidden');
                            mobileMenu.style.display = 'block';
                            setTimeout(() => {
                                mobileMenu.style.transform = 'scaleY(1)';
                                mobileMenu.style.opacity = '1';
                            }, 10);
                            
                            // Cambiar a icono X
                            const svg = mobileMenuButton.querySelector('svg');
                            svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />';
                        } else {
                            // Ocultar menú
                            mobileMenu.style.transform = 'scaleY(0)';
                            mobileMenu.style.opacity = '0';
                            setTimeout(() => {
                                mobileMenu.classList.add('hidden');
                                mobileMenu.style.display = 'none';
                            }, 300);
                            
                            // Cambiar a icono hamburguesa
                            const svg = mobileMenuButton.querySelector('svg');
                            svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />';
                        }
                    };
                    
                    // Cerrar menú móvil al hacer clic en un enlace
                    const mobileMenuLinks = mobileMenu.querySelectorAll('a');
                    mobileMenuLinks.forEach(link => {
                        link.addEventListener('click', function() {
                            mobileMenu.style.transform = 'scaleY(0)';
                            mobileMenu.style.opacity = '0';
                            setTimeout(() => {
                                mobileMenu.classList.add('hidden');
                                mobileMenu.style.display = 'none';
                            }, 300);
                            
                            const svg = mobileMenuButton.querySelector('svg');
                            svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />';
                        });
                    });
                    
                    // Cerrar menú móvil al hacer clic fuera
                    document.addEventListener('click', function(event) {
                        if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                            if (!mobileMenu.classList.contains('hidden')) {
                                mobileMenu.style.transform = 'scaleY(0)';
                                mobileMenu.style.opacity = '0';
                                setTimeout(() => {
                                    mobileMenu.classList.add('hidden');
                                    mobileMenu.style.display = 'none';
                                }, 300);
                                
                                const svg = mobileMenuButton.querySelector('svg');
                                svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />';
                            }
                        }
                    });
                }

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

                // Cerrar menús al cambiar el tamaño de la ventana
                window.addEventListener('resize', function() {
                    if (window.innerWidth >= 768) {
                        // En pantallas grandes, ocultar menú móvil
                        if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                            mobileMenu.style.transform = 'scaleY(0)';
                            mobileMenu.style.opacity = '0';
                            setTimeout(() => {
                                mobileMenu.classList.add('hidden');
                                mobileMenu.style.display = 'none';
                            }, 300);
                            
                            const svg = mobileMenuButton.querySelector('svg');
                            if (svg) {
                                svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />';
                            }
                        }
                    }
                });
            });

            // Funciones para las modales legales
            function openLegalModal(type) {
                const modal = document.getElementById('legalModal');
                const title = document.getElementById('legalModalTitle');
                const content = document.getElementById('legalModalContent');
                
                // Definir el contenido según el tipo
                const legalContent = {
                    'terms': {
                        title: 'Términos y Condiciones',
                        content: `
                            <h4 class="font-semibold text-lg mb-4">Términos y Condiciones de Uso</h4>
                            <p class="mb-4">Bienvenido a Art Indie Space. Al utilizar nuestra plataforma, aceptas los siguientes términos:</p>
                            
                            <h5 class="font-semibold mb-2">1. Uso de la Plataforma</h5>
                            <p class="mb-4">Art Indie Space es una plataforma para artistas independientes. Te comprometes a usar el servicio de manera responsable y respetuosa.</p>
                            
                            <h5 class="font-semibold mb-2">2. Contenido del Usuario</h5>
                            <p class="mb-4">Eres responsable del contenido que subas a la plataforma. Debes tener los derechos sobre las obras que compartas.</p>
                            
                            <h5 class="font-semibold mb-2">3. Propiedad Intelectual</h5>
                            <p class="mb-4">Los artistas mantienen todos los derechos sobre sus obras. Art Indie Space solo proporciona la plataforma de exhibición.</p>
                            
                            <h5 class="font-semibold mb-2">4. Modificaciones</h5>
                            <p class="mb-4">Nos reservamos el derecho de modificar estos términos en cualquier momento. Los cambios serán notificados a los usuarios.</p>
                        `
                    },
                    'privacy': {
                        title: 'Política de Privacidad',
                        content: `
                            <h4 class="font-semibold text-lg mb-4">Política de Privacidad</h4>
                            <p class="mb-4">Tu privacidad es importante para nosotros. Esta política describe cómo recopilamos y utilizamos tu información:</p>
                            
                            <h5 class="font-semibold mb-2">1. Información que Recopilamos</h5>
                            <p class="mb-4">Recopilamos información que nos proporcionas directamente, como tu nombre, email y obras de arte que subes.</p>
                            
                            <h5 class="font-semibold mb-2">2. Uso de la Información</h5>
                            <p class="mb-4">Utilizamos tu información para proporcionar y mejorar nuestros servicios, comunicarnos contigo y personalizar tu experiencia.</p>
                            
                            <h5 class="font-semibold mb-2">3. Compartir Información</h5>
                            <p class="mb-4">No vendemos, alquilamos ni compartimos tu información personal con terceros sin tu consentimiento explícito.</p>
                            
                            <h5 class="font-semibold mb-2">4. Seguridad</h5>
                            <p class="mb-4">Implementamos medidas de seguridad para proteger tu información personal contra acceso no autorizado.</p>
                        `
                    },
                    'cookies': {
                        title: 'Política de Cookies',
                        content: `
                            <h4 class="font-semibold text-lg mb-4">Política de Cookies</h4>
                            <p class="mb-4">Utilizamos cookies para mejorar tu experiencia en Art Indie Space:</p>
                            
                            <h5 class="font-semibold mb-2">¿Qué son las cookies?</h5>
                            <p class="mb-4">Las cookies son pequeños archivos de texto que se almacenan en tu dispositivo cuando visitas nuestro sitio web.</p>
                            
                            <h5 class="font-semibold mb-2">Tipos de cookies que utilizamos:</h5>
                            <ul class="list-disc list-inside mb-4 space-y-1">
                                <li><strong>Cookies esenciales:</strong> Necesarias para el funcionamiento básico del sitio</li>
                                <li><strong>Cookies de funcionalidad:</strong> Para recordar tus preferencias</li>
                                <li><strong>Cookies de rendimiento:</strong> Para analizar cómo se usa el sitio</li>
                            </ul>
                            
                            <h5 class="font-semibold mb-2">Control de cookies</h5>
                            <p class="mb-4">Puedes controlar y eliminar las cookies a través de la configuración de tu navegador. Sin embargo, esto puede afectar la funcionalidad del sitio.</p>
                        `
                    }
                };
                
                // Establecer el contenido
                title.textContent = legalContent[type].title;
                content.innerHTML = legalContent[type].content;
                
                // Mostrar la modal
                modal.classList.remove('hidden');
            }

            function closeLegalModal() {
                const modal = document.getElementById('legalModal');
                modal.classList.add('hidden');
            }

            // Cerrar modal al hacer clic fuera de ella
            document.addEventListener('click', function(event) {
                const modal = document.getElementById('legalModal');
                if (modal && event.target === modal) {
                    closeLegalModal();
                }
            });
        </script>
        @stack('scripts')
         <!-- Swiper JS -->
         <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
    </body>
</html>
