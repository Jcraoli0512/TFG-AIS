<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galería - Art Indie Space</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Three.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
</head>

<body class="bg-gray-50">
    <!-- Barra de navegación -->
    <nav class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div>
                <img src="{{ asset('img_web/logo.png') }}" alt="Logo" class="w-16 h-16">
            </div>
            <div class="hidden md:flex space-x-6">
                <a href="{{ route('gallery') }}" class="text-gray-700 hover:text-gray-900">Galerías</a>
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
        <h1 class="text-4xl font-bold text-center mb-8">Galería de Arte 3D</h1>
        
        <div id="gallery-container" class="w-full h-[600px] relative bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- El canvas de Three.js se insertará aquí -->
        </div>
        
        <div class="mt-4 text-center text-gray-600">
            <p>Usa el ratón para mover la cámara. Haz clic y arrastra para rotar, rueda para hacer zoom.</p>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t mt-12 py-6">
        <div class="container mx-auto px-4 text-center text-gray-600">
            © AIS Art Indie Space
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configuración básica
            const container = document.getElementById('gallery-container');
            const scene = new THREE.Scene();
            const camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1, 1000);
            const renderer = new THREE.WebGLRenderer({ antialias: true });
            renderer.setSize(container.clientWidth, container.clientHeight);
            container.appendChild(renderer.domElement);

            // Controles de órbita
            const controls = new THREE.OrbitControls(camera, renderer.domElement);
            controls.enableDamping = true;
            controls.dampingFactor = 0.05;

            // Crear sala 3D
            const roomSize = 10;
            const wallGeometry = new THREE.BoxGeometry(roomSize, roomSize, roomSize);
            const wallMaterial = new THREE.MeshBasicMaterial({
                color: 0xf3f4f6, // Color gris claro que coincide con el tema
                side: THREE.BackSide,
                wireframe: false
            });
            const room = new THREE.Mesh(wallGeometry, wallMaterial);
            scene.add(room);

            // Crear cuadros en las paredes
            const paintings = [
                { position: [0, 0, 4.9], rotation: [0, 0, 0], image: 'https://picsum.photos/400/500' },
                { position: [0, 0, -4.9], rotation: [0, Math.PI, 0], image: 'https://picsum.photos/401/500' },
                { position: [4.9, 0, 0], rotation: [0, Math.PI/2, 0], image: 'https://picsum.photos/402/500' },
                { position: [-4.9, 0, 0], rotation: [0, -Math.PI/2, 0], image: 'https://picsum.photos/403/500' }
            ];

            paintings.forEach(painting => {
                const texture = new THREE.TextureLoader().load(painting.image);
                const material = new THREE.MeshBasicMaterial({ map: texture });
                const geometry = new THREE.PlaneGeometry(2, 3);
                const mesh = new THREE.Mesh(geometry, material);
                mesh.position.set(...painting.position);
                mesh.rotation.set(...painting.rotation);
                scene.add(mesh);
            });

            // Posicionar cámara
            camera.position.z = 7;

            // Iluminación
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.7);
            scene.add(ambientLight);

            const directionalLight = new THREE.DirectionalLight(0xffffff, 0.5);
            directionalLight.position.set(5, 5, 5);
            scene.add(directionalLight);

            // Manejar redimensionamiento
            window.addEventListener('resize', () => {
                camera.aspect = container.clientWidth / container.clientHeight;
                camera.updateProjectionMatrix();
                renderer.setSize(container.clientWidth, container.clientHeight);
            });

            // Animación
            function animate() {
                requestAnimationFrame(animate);
                controls.update();
                renderer.render(scene, camera);
            }
            animate();
        });
    </script>

    <style>
        #gallery-container {
            background-color: #ffffff;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        #gallery-container canvas {
            width: 100%;
            height: 100%;
        }
    </style>
</body>

</html> 