<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nosotros - Art Indie Space</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/png" href="{{ asset('img_web/logo.png') }}">
</head>

<body class="bg-gray-50">
    <!-- Barra de navegación -->
    <nav class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div>
                <a href="{{ route('welcome') }}">
                    <img src="{{ asset('img_web/logo.png') }}" alt="Logo" class="w-16 h-16">
                </a>
            </div>
            <div class="hidden md:flex space-x-6">
                <a href="{{ route('welcome') }}" class="text-gray-700 hover:text-gray-900">Inicio</a>
                <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900">Galería</a>
                <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900">Calendario</a>
                <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900">Artistas</a>
                <a href="{{ route('about') }}" class="text-gray-700 hover:text-gray-900 font-bold">Nosotros</a>
            </div>
            <div class="space-x-4">
                <a href="{{ route('register') }}"
                    class="bg-gray-100 px-4 py-2 rounded-lg hover:bg-gray-200 transition">Registrarte</a>
                <a href="{{ route('login') }}"
                    class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">Iniciar Sesión</a>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-4xl font-bold text-center mb-8">Sobre Nosotros</h1>
            
            <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
                <h2 class="text-2xl font-semibold mb-4">Nuestra Misión</h2>
                <p class="text-gray-600 leading-relaxed mb-6">
                    En Art Indie Space, nos dedicamos a crear un espacio donde los artistas independientes puedan mostrar su trabajo y conectar con una audiencia que aprecia el arte en todas sus formas. Creemos en la diversidad artística y en dar voz a aquellos que tienen algo único que compartir.
                </p>
                
                <h2 class="text-2xl font-semibold mb-4">Nuestra Visión</h2>
                <p class="text-gray-600 leading-relaxed mb-6">
                    Aspiramos a ser el punto de encuentro entre artistas emergentes y amantes del arte, creando una comunidad vibrante donde la creatividad florece y las conexiones se fortalecen. Queremos democratizar el acceso al arte y hacer que cada artista tenga la oportunidad de ser descubierto.
                </p>
                
                <h2 class="text-2xl font-semibold mb-4">Nuestros Valores</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-xl font-semibold mb-3">Diversidad</h3>
                        <p class="text-gray-600">Celebramos y promovemos la diversidad en todas sus formas, tanto en los estilos artísticos como en los artistas que representamos.</p>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-xl font-semibold mb-3">Innovación</h3>
                        <p class="text-gray-600">Fomentamos la experimentación y la innovación en el arte, apoyando a aquellos que buscan nuevas formas de expresión.</p>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-xl font-semibold mb-3">Comunidad</h3>
                        <p class="text-gray-600">Construimos una comunidad inclusiva donde los artistas pueden crecer y desarrollarse en un ambiente de apoyo mutuo.</p>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-xl font-semibold mb-3">Accesibilidad</h3>
                        <p class="text-gray-600">Trabajamos para hacer el arte accesible para todos, eliminando barreras y creando oportunidades de conexión.</p>
                    </div>
                </div>
            </div>
            
            <div class="text-center">
                <a href="{{ route('register') }}" class="inline-block bg-gray-800 text-white px-8 py-3 rounded-lg hover:bg-gray-700 transition">
                    Únete a nuestra comunidad
                </a>
            </div>
        </div>
    </div>

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
                        <li><a href="{{ route('welcome') }}" class="text-gray-600 hover:text-gray-900">Inicio</a></li>
                        <li><a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Galería</a></li>
                        <li><a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Calendario</a></li>
                        <li><a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Artistas</a></li>
                        <li><a href="{{ route('about') }}" class="text-gray-600 hover:text-gray-900">Nosotros</a></li>
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
</body>

</html> 