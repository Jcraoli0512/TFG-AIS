<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenidos a Art Indie Space</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css" />
    <style>
        .swiper-button-next,
        .swiper-button-prev {
            color: black !important;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Barra de navegación -->
    <nav class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div>
                <img src="{{ asset('img_web/logo.png') }}" alt="Logo" class="w-16 h-16">
            </div>
            <div class="hidden md:flex space-x-6">
                <a href="#" class="text-gray-700 hover:text-gray-900">Galerías</a>
                <a href="#" class="text-gray-700 hover:text-gray-900">Calendario</a>
                <a href="#" class="text-gray-700 hover:text-gray-900">Artistas</a>
                <a href="#" class="text-gray-700 hover:text-gray-900">Géneros</a>
                <a href="#" class="text-gray-700 hover:text-gray-900">Nosotros</a>
            </div>
            <div class="space-x-4">
                @auth
                    <div class="relative inline-block text-left">
                        <button id="userMenuButton" type="button" class="flex items-center space-x-2 focus:outline-none">
                            <img src="{{ Auth::user()->profile_photo_url }}" alt="Foto de perfil" class="w-10 h-10 rounded-full object-cover">
                            <span class="text-gray-700">{{ Auth::user()->name }}</span>
                        </button>
                        <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white border rounded shadow-lg z-50">
                            <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Ir a perfil</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">Cerrar sesión</button>
                            </form>
                        </div>
                    </div>
                    <script>
                        // Mostrar/ocultar menú desplegable
                        document.addEventListener('DOMContentLoaded', function () {
                            const userMenuButton = document.getElementById('userMenuButton');
                            const userDropdown = document.getElementById('userDropdown');
                            userMenuButton.onclick = function (event) {
                                event.stopPropagation();
                                userDropdown.classList.toggle('hidden');
                            };
                            document.addEventListener('click', function (event) {
                                if (!userDropdown.classList.contains('hidden')) {
                                    userDropdown.classList.add('hidden');
                                }
                            });
                        });
                    </script>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container mx-auto px-4 py-12">
        <h1 class="text-4xl font-bold text-center mb-8">Bienvenidos a Art Indie Space</h1>

        <!-- Swiper -->
        <div class="max-w-2xl mx-auto mb-12">
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <img src="https://images.unsplash.com/photo-1464983953574-0892a716854b" alt="Arte 1"
                            class="w-full h-64 object-cover rounded-lg">
                    </div>
                    <div class="swiper-slide">
                        <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb" alt="Arte 2"
                            class="w-full h-64 object-cover rounded-lg">
                    </div>
                    <div class="swiper-slide">
                        <img src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97" alt="Arte 3"
                            class="w-full h-64 object-cover rounded-lg">
                    </div>
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>

        <!-- Texto descriptivo -->
        <div class="max-w-3xl mx-auto text-center">
            <p class="text-lg text-gray-600 leading-relaxed">
                Un espacio dedicado a aquellos artistas que quieren compartir, exponer, mostrar, etc... sus obras,
                tanto si es realismo como cubismo, aceptamos y mostramos todos, así que coged vuestras obras y
                mostradla al mundo.
            </p>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t mt-12 py-6">
        <div class="container mx-auto px-4 text-center text-gray-600">
            © AIS Art Indie Space
        </div>
    </footer>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
    <script>
        var swiper = new Swiper(".mySwiper", {
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            loop: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
        });
    </script>
</body>

</html> 