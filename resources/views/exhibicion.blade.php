@extends('layouts.app')

@section('title', 'Galería - Art Indie Space')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <h1 class="text-4xl font-bold text-center mb-8">Galería de Arte 3D</h1>
        
        <script>console.log('Content section loaded');</script>

        <div id="gallery-container" class="w-full h-[600px] relative bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- El canvas de Three.js se insertará aquí -->
        </div>
        
        <div class="mt-4 text-center text-gray-600">
            <p>Controles: W (adelante), S (atrás), A (izquierda), D (derecha). Haz clic para mirar alrededor.</p>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Three.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script>
        console.log('Exhibicion script loaded'); // Debug: Check if script loads

        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM fully loaded'); // Debug: Check if DOMContentLoaded fires

            // Configuración básica
            const container = document.getElementById('gallery-container');
            console.log('Gallery container element:', container); // Debug: Check if container is found

            if (!container) {
                console.error('Error: Gallery container element not found!');
                return; // Stop execution if container is not found
            }
            
            const scene = new THREE.Scene();
            const camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1, 1000);
            const renderer = new THREE.WebGLRenderer({ antialias: true });
            
            renderer.setSize(container.clientWidth, container.clientHeight);
            container.appendChild(renderer.domElement);

            // Parámetros del pasillo
            const corridorLength = 30; // Largo del pasillo
            const corridorWidth = 6;   // Ancho del pasillo
            const wallHeight = 5;
            const wallThickness = 0.5;

            // Materiales
            const wallMaterial = new THREE.MeshPhongMaterial({ 
                color: 0xF5F5DC, 
                shininess: 0,
                side: THREE.DoubleSide 
            });
            const floorMaterial = new THREE.MeshPhongMaterial({ 
                color: 0x8B4513, 
                shininess: 0,
                side: THREE.DoubleSide 
            });
            const ceilingMaterial = new THREE.MeshPhongMaterial({ 
                color: 0x222222, 
                shininess: 0,
                side: THREE.DoubleSide 
            });

            // Crear paredes laterales
            function createWall(x, z, width, height, depth) {
                const geometry = new THREE.BoxGeometry(width, height, depth);
                const wall = new THREE.Mesh(geometry, wallMaterial);
                wall.position.set(x, height/2, z);
                scene.add(wall);
            }

            // Pared izquierda
            createWall(-corridorWidth/2, 0, wallThickness, wallHeight, corridorLength);
            // Pared derecha
            createWall(corridorWidth/2, 0, wallThickness, wallHeight, corridorLength);
            // Pared fondo (opcional)
            createWall(0, -corridorLength/2, corridorWidth, wallHeight, wallThickness);
            // Pared entrada (opcional)
            createWall(0, corridorLength/2, corridorWidth, wallHeight, wallThickness);

            // Suelo
            const floorGeometry = new THREE.PlaneGeometry(corridorWidth, corridorLength);
            const floor = new THREE.Mesh(floorGeometry, floorMaterial);
            floor.rotation.x = -Math.PI / 2;
            floor.position.set(0, 0, 0);
            scene.add(floor);

            // Techo
            const ceilingGeometry = new THREE.PlaneGeometry(corridorWidth, corridorLength);
            const ceiling = new THREE.Mesh(ceilingGeometry, ceilingMaterial);
            ceiling.rotation.x = Math.PI / 2;
            ceiling.position.set(0, wallHeight, 0);
            scene.add(ceiling);

            // Posiciones para las obras (5 a la izquierda, 5 a la derecha)
            const artworkPositions = [];
            const spacing = corridorLength / 11; // 10 espacios, 5 obras por lado
            for (let i = 0; i < 5; i++) {
                // Izquierda
                artworkPositions.push({
                    position: [-(corridorWidth/2) + wallThickness/2 + 0.1, wallHeight/2, -corridorLength/2 + spacing * (i+1)],
                    rotation: [0, Math.PI/2, 0],
                    size: [3, 4]
                });
                // Derecha
                artworkPositions.push({
                    position: [(corridorWidth/2) - wallThickness/2 - 0.1, wallHeight/2, -corridorLength/2 + spacing * (i+1)],
                    rotation: [0, -Math.PI/2, 0],
                    size: [3, 4]
                });
            }

            // Función para añadir una obra a la escena 3D
            function addArtworkToScene(artwork, positionData) {
                const textureLoader = new THREE.TextureLoader();
                console.log('Loading artwork:', artwork.url);
                
                textureLoader.load(
                    artwork.url,
                    function(texture) {
                        console.log('Texture loaded successfully');
                        
                        // Configuración básica de la textura
                        texture.minFilter = THREE.LinearFilter;
                        texture.magFilter = THREE.LinearFilter;
                        texture.anisotropy = 4; // Añadir anisotropía moderada
                        texture.generateMipmaps = true;
                        
                        // Material para la obra
                        const material = new THREE.MeshBasicMaterial({ 
                            map: texture,
                            side: THREE.DoubleSide
                        });
                        
                        // Crear la geometría y el mesh de la obra
                        const geometry = new THREE.PlaneGeometry(...positionData.size);
                        const mesh = new THREE.Mesh(geometry, material);
                        
                        // Posicionar la obra ligeramente por delante del marco
                        const artworkPosition = [...positionData.position];
                        // Mover la obra hacia adelante y hacia el centro del pasillo
                        if (positionData.position[0] < 0) { // Si está en la pared izquierda
                            artworkPosition[0] += 0.2; // Mover hacia la derecha
                        } else { // Si está en la pared derecha
                            artworkPosition[0] -= 0.2; // Mover hacia la izquierda
                        }
                        artworkPosition[2] += 0.2; // Mover hacia adelante
                        mesh.position.set(...artworkPosition);
                        mesh.rotation.set(...positionData.rotation);
                        
                        // Añadir un marco simple
                        const frameGeometry = new THREE.BoxGeometry(
                            positionData.size[0] + 0.2, 
                            positionData.size[1] + 0.2, 
                            0.1
                        );
                        const frameMaterial = new THREE.MeshBasicMaterial({ 
                            color: 0x8B4513
                        });
                        const frame = new THREE.Mesh(frameGeometry, frameMaterial);
                        frame.position.set(...positionData.position);
                        frame.rotation.set(...positionData.rotation);
                        
                        // Añadir primero el marco y luego la obra
                        scene.add(frame);
                        scene.add(mesh);
                        
                        // Añadir un punto de luz suave para cada obra
                        const spotLight = new THREE.PointLight(0xffffff, 0.3, 10);
                        spotLight.position.set(
                            positionData.position[0],
                            positionData.position[1] + 2,
                            positionData.position[2]
                        );
                        scene.add(spotLight);
                    },
                    function(xhr) {
                        console.log((xhr.loaded / xhr.total * 100) + '% loaded');
                    },
                    function(error) {
                        console.error('Error loading texture:', error);
                    }
                );
            }

            // Obtener y mostrar las obras del día actual
            const today = new Date().toISOString().split('T')[0];
            console.log('Fetching artworks for date:', today); // Debug log
            
            fetch(`/api/gallery-images/${today}`)
                .then(response => response.json())
                .then(artworks => {
                    console.log('Received artworks:', artworks); // Debug log
                    artworks.slice(0, 10).forEach((artwork, index) => {
                        if (index < artworkPositions.length) {
                            addArtworkToScene(artwork, artworkPositions[index]);
                        }
                    });
                })
                .catch(error => {
                    console.error('Error fetching artworks:', error);
                });

            // Posicionar cámara al inicio del pasillo
            camera.position.set(0, 1.7, corridorLength/2 - 2);
            camera.lookAt(0, 1.7, 0);

            // Iluminación base
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.4);
            scene.add(ambientLight);
            
            const directionalLight = new THREE.DirectionalLight(0xffffff, 0.3);
            directionalLight.position.set(5, 5, 5);
            scene.add(directionalLight);
            
            // Mantener controles y animación existentes...
            // (Pega aquí el resto del código de controles y animación WASD, pointer lock, etc.)

            // --- CONTROLES Y ANIMACIÓN (igual que antes) ---
            const moveSpeed = 0.15;
            const keys = { w: false, a: false, s: false, d: false };
            let isPointerLocked = false;
            const sensitivity = 0.002;
            let pitchObject = new THREE.Object3D();
            let yawObject = new THREE.Object3D();
            yawObject.position.y = 1.7;
            yawObject.add(pitchObject);
            scene.add(yawObject);
            camera.position.set(0, 0, 0);
            pitchObject.add(camera);
            container.addEventListener('click', () => { container.requestPointerLock(); });
            document.addEventListener('pointerlockchange', () => { isPointerLocked = document.pointerLockElement === container; });
            document.addEventListener('mousemove', (e) => {
                if (isPointerLocked) {
                    yawObject.rotation.y -= e.movementX * sensitivity;
                    pitchObject.rotation.x -= e.movementY * sensitivity;
                    pitchObject.rotation.x = Math.max(-Math.PI/2, Math.min(Math.PI/2, pitchObject.rotation.x));
                }
            });
            document.addEventListener('keydown', (e) => { if (keys.hasOwnProperty(e.key.toLowerCase())) keys[e.key.toLowerCase()] = true; });
            document.addEventListener('keyup', (e) => { if (keys.hasOwnProperty(e.key.toLowerCase())) keys[e.key.toLowerCase()] = false; });
            window.addEventListener('resize', () => {
                camera.aspect = container.clientWidth / container.clientHeight;
                camera.updateProjectionMatrix();
                renderer.setSize(container.clientWidth, container.clientHeight);
            });
            function animate() {
                requestAnimationFrame(animate);
                const moveDirection = new THREE.Vector3();
                if (keys.w) moveDirection.z -= 1;
                if (keys.s) moveDirection.z += 1;
                if (keys.a) moveDirection.x -= 1;
                if (keys.d) moveDirection.x += 1;
                if (moveDirection.length() > 0) {
                    moveDirection.normalize();
                    moveDirection.multiplyScalar(moveSpeed);
                    
                    // Ajustar la dirección del movimiento según la rotación de la cámara
                    const angle = -yawObject.rotation.y; // Invertimos el ángulo para mantener la consistencia
                    const rotatedX = moveDirection.x * Math.cos(angle) - moveDirection.z * Math.sin(angle);
                    const rotatedZ = moveDirection.x * Math.sin(angle) + moveDirection.z * Math.cos(angle);
                    
                    const nextPosition = yawObject.position.clone().add(new THREE.Vector3(rotatedX, 0, rotatedZ));
                    
                    // Limitar movimiento a los límites del pasillo
                    if (Math.abs(nextPosition.x) < (corridorWidth/2 - 0.7)) {
                        yawObject.position.x = nextPosition.x;
                    }
                    if (nextPosition.z > -corridorLength/2 + 1 && nextPosition.z < corridorLength/2 - 1) {
                        yawObject.position.z = nextPosition.z;
                    }
                }
                renderer.render(scene, camera);
            }
            animate();
        });
    </script>
@endpush 