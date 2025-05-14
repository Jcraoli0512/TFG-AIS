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
            <p>Controles: W (adelante), S (atrás), A (izquierda), D (derecha). Haz clic para mirar alrededor.</p>
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

            // Variables para el movimiento
            const moveSpeed = 0.15; // Aumentado para un movimiento más fluido
            const keys = {
                w: false,
                a: false,
                s: false,
                d: false
            };

            // Crear sala principal y pasillos
            const wallMaterial = new THREE.MeshPhongMaterial({
                color: 0xf3f4f6,
                side: THREE.DoubleSide
            });

            // Sala principal
            const mainRoomWidth = 20;
            const mainRoomHeight = 8;
            const mainRoomDepth = 20;
            const wallThickness = 0.5;

            // Paredes de la sala principal
            const walls = [
                // Pared frontal
                { size: [mainRoomWidth, mainRoomHeight, wallThickness], position: [0, mainRoomHeight/2, -mainRoomDepth/2] },
                // Pared trasera
                { size: [mainRoomWidth, mainRoomHeight, wallThickness], position: [0, mainRoomHeight/2, mainRoomDepth/2] },
                // Pared izquierda
                { size: [wallThickness, mainRoomHeight, mainRoomDepth], position: [-mainRoomWidth/2, mainRoomHeight/2, 0] },
                // Pared derecha
                { size: [wallThickness, mainRoomHeight, mainRoomDepth], position: [mainRoomWidth/2, mainRoomHeight/2, 0] },
                // Techo
                { size: [mainRoomWidth, wallThickness, mainRoomDepth], position: [0, mainRoomHeight, 0] },
                // Suelo
                { size: [mainRoomWidth, wallThickness, mainRoomDepth], position: [0, 0, 0] }
            ];

            walls.forEach(wall => {
                const geometry = new THREE.BoxGeometry(...wall.size);
                const mesh = new THREE.Mesh(geometry, wallMaterial);
                mesh.position.set(...wall.position);
                scene.add(mesh);
            });

            // Pasillos
            const hallwayWidth = 4;
            const hallwayHeight = mainRoomHeight;
            const hallwayDepth = 10;

            // Pasillo izquierdo
            const leftHallway = [
                { size: [hallwayWidth, hallwayHeight, wallThickness], position: [-mainRoomWidth/2 - hallwayWidth/2, hallwayHeight/2, -hallwayDepth/2] },
                { size: [hallwayWidth, hallwayHeight, wallThickness], position: [-mainRoomWidth/2 - hallwayWidth/2, hallwayHeight/2, hallwayDepth/2] },
                { size: [wallThickness, hallwayHeight, hallwayDepth], position: [-mainRoomWidth/2 - hallwayWidth, hallwayHeight/2, 0] }
            ];

            // Pasillo derecho
            const rightHallway = [
                { size: [hallwayWidth, hallwayHeight, wallThickness], position: [mainRoomWidth/2 + hallwayWidth/2, hallwayHeight/2, -hallwayDepth/2] },
                { size: [hallwayWidth, hallwayHeight, wallThickness], position: [mainRoomWidth/2 + hallwayWidth/2, hallwayHeight/2, hallwayDepth/2] },
                { size: [wallThickness, hallwayHeight, hallwayDepth], position: [mainRoomWidth/2 + hallwayWidth, hallwayHeight/2, 0] }
            ];

            [...leftHallway, ...rightHallway].forEach(wall => {
                const geometry = new THREE.BoxGeometry(...wall.size);
                const mesh = new THREE.Mesh(geometry, wallMaterial);
                mesh.position.set(...wall.position);
                scene.add(mesh);
            });

            // Añadir cuadros en las paredes
            const paintings = [
                // Sala principal
                { position: [0, mainRoomHeight/2, -mainRoomDepth/2 + 0.1], rotation: [0, 0, 0], size: [3, 4], image: 'https://picsum.photos/400/500' },
                { position: [0, mainRoomHeight/2, mainRoomDepth/2 - 0.1], rotation: [0, Math.PI, 0], size: [3, 4], image: 'https://picsum.photos/401/500' },
                { position: [-mainRoomWidth/2 + 0.1, mainRoomHeight/2, 0], rotation: [0, Math.PI/2, 0], size: [3, 4], image: 'https://picsum.photos/402/500' },
                { position: [mainRoomWidth/2 - 0.1, mainRoomHeight/2, 0], rotation: [0, -Math.PI/2, 0], size: [3, 4], image: 'https://picsum.photos/403/500' },
                
                // Pasillo izquierdo
                { position: [-mainRoomWidth/2 - hallwayWidth/2, hallwayHeight/2, -hallwayDepth/2 + 0.1], rotation: [0, 0, 0], size: [2, 3], image: 'https://picsum.photos/404/500' },
                { position: [-mainRoomWidth/2 - hallwayWidth/2, hallwayHeight/2, hallwayDepth/2 - 0.1], rotation: [0, Math.PI, 0], size: [2, 3], image: 'https://picsum.photos/405/500' },
                { position: [-mainRoomWidth/2 - hallwayWidth + 0.1, hallwayHeight/2, 0], rotation: [0, Math.PI/2, 0], size: [2, 3], image: 'https://picsum.photos/406/500' },
                
                // Pasillo derecho
                { position: [mainRoomWidth/2 + hallwayWidth/2, hallwayHeight/2, -hallwayDepth/2 + 0.1], rotation: [0, 0, 0], size: [2, 3], image: 'https://picsum.photos/407/500' },
                { position: [mainRoomWidth/2 + hallwayWidth/2, hallwayHeight/2, hallwayDepth/2 - 0.1], rotation: [0, Math.PI, 0], size: [2, 3], image: 'https://picsum.photos/408/500' },
                { position: [mainRoomWidth/2 + hallwayWidth - 0.1, hallwayHeight/2, 0], rotation: [0, -Math.PI/2, 0], size: [2, 3], image: 'https://picsum.photos/409/500' }
            ];

            paintings.forEach(painting => {
                const texture = new THREE.TextureLoader().load(painting.image);
                const material = new THREE.MeshPhongMaterial({ map: texture });
                const geometry = new THREE.PlaneGeometry(...painting.size);
                const mesh = new THREE.Mesh(geometry, material);
                mesh.position.set(...painting.position);
                mesh.rotation.set(...painting.rotation);
                scene.add(mesh);
            });

            // Posicionar cámara
            camera.position.set(0, 1.7, 0); // Altura de los ojos
            camera.lookAt(0, 1.7, -1);

            // Iluminación
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
            scene.add(ambientLight);

            const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
            directionalLight.position.set(5, 5, 5);
            scene.add(directionalLight);

            // Controles de teclado
            document.addEventListener('keydown', (e) => {
                if (keys.hasOwnProperty(e.key.toLowerCase())) {
                    keys[e.key.toLowerCase()] = true;
                }
            });

            document.addEventListener('keyup', (e) => {
                if (keys.hasOwnProperty(e.key.toLowerCase())) {
                    keys[e.key.toLowerCase()] = false;
                }
            });

            // Manejar redimensionamiento
            window.addEventListener('resize', () => {
                camera.aspect = container.clientWidth / container.clientHeight;
                camera.updateProjectionMatrix();
                renderer.setSize(container.clientWidth, container.clientHeight);
            });

            // Animación y movimiento
            function animate() {
                requestAnimationFrame(animate);

                // Movimiento WASD
                const moveDirection = new THREE.Vector3();
                if (keys.w) moveDirection.z += 1;
                if (keys.s) moveDirection.z -= 1;
                if (keys.a) moveDirection.x += 1;
                if (keys.d) moveDirection.x -= 1;

                if (moveDirection.length() > 0) {
                    moveDirection.normalize();
                    moveDirection.multiplyScalar(moveSpeed);
                    
                    // Aplicar movimiento en la dirección de la cámara
                    const cameraDirection = new THREE.Vector3();
                    camera.getWorldDirection(cameraDirection);
                    cameraDirection.y = 0;
                    cameraDirection.normalize();

                    const right = new THREE.Vector3();
                    right.crossVectors(new THREE.Vector3(0, 1, 0), cameraDirection);

                    const movement = new THREE.Vector3();
                    movement.addScaledVector(cameraDirection, moveDirection.z);
                    movement.addScaledVector(right, moveDirection.x);

                    // Verificar colisiones con las paredes
                    const nextPosition = camera.position.clone().add(movement);
                    const roomBounds = {
                        x: mainRoomWidth/2 - 1,
                        z: mainRoomDepth/2 - 1,
                        hallwayX: mainRoomWidth/2 + hallwayWidth - 1,
                        hallwayZ: hallwayDepth/2 - 1
                    };

                    if (Math.abs(nextPosition.x) < roomBounds.x || 
                        (nextPosition.x < -roomBounds.hallwayX && Math.abs(nextPosition.z) < roomBounds.hallwayZ) ||
                        (nextPosition.x > roomBounds.hallwayX && Math.abs(nextPosition.z) < roomBounds.hallwayZ)) {
                        camera.position.x = nextPosition.x;
                    }

                    if (Math.abs(nextPosition.z) < roomBounds.z || 
                        (Math.abs(nextPosition.x) > roomBounds.x && Math.abs(nextPosition.z) < roomBounds.hallwayZ)) {
                        camera.position.z = nextPosition.z;
                    }
                }

                renderer.render(scene, camera);
            }
            animate();

            // Controles de ratón para mirar alrededor
            let isPointerLocked = false;
            const sensitivity = 0.002;

            container.addEventListener('click', () => {
                container.requestPointerLock();
            });

            document.addEventListener('pointerlockchange', () => {
                isPointerLocked = document.pointerLockElement === container;
            });

            document.addEventListener('mousemove', (e) => {
                if (isPointerLocked) {
                    camera.rotation.y -= e.movementX * sensitivity;
                    camera.rotation.x -= e.movementY * sensitivity;
                    camera.rotation.x = Math.max(-Math.PI/2, Math.min(Math.PI/2, camera.rotation.x));
                }
            });
        });
    </script>

    <style>
        #gallery-container {
            background-color: #ffffff;
            border-radius: 0.5rem;
            overflow: hidden;
            cursor: pointer;
        }
        #gallery-container canvas {
            width: 100%;
            height: 100%;
        }
    </style>
</body>

</html> 