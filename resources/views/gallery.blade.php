@extends('layouts.app')

@section('title', 'Galería - Art Indie Space')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <h1 class="text-4xl font-bold text-center mb-8">Galería de Arte 3D</h1>
        
        <div id="gallery-container" class="w-full h-[600px] relative bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- El canvas de Three.js se insertará aquí -->
        </div>
        
        <div class="mt-4 text-center text-gray-600">
            <p>Controles: W (adelante), S (atrás), A (izquierda), D (derecha). Haz clic para mirar alrededor.</p>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Three.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
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
            const moveSpeed = 0.15;
            const keys = {
                w: false,
                a: false,
                s: false,
                d: false
            };

            // Materiales
            const wallMaterial = new THREE.MeshPhongMaterial({ 
                color: 0xF5F5DC,  // Beige
                shininess: 0
            });
            const floorMaterial = new THREE.MeshPhongMaterial({ 
                color: 0x8B4513,  // Marrón
                shininess: 0
            });
            const ceilingMaterial = new THREE.MeshPhongMaterial({ 
                color: 0x000000,  // Negro
                shininess: 0
            });

            // Crear geometrías
            const wallGeometry = new THREE.BoxGeometry(1, 5, 1);
            const floorGeometry = new THREE.PlaneGeometry(1, 1);
            const ceilingGeometry = new THREE.PlaneGeometry(1, 1);

            // Función para crear pared
            function createWall(x, z, width, depth) {
                const wall = new THREE.Mesh(wallGeometry, wallMaterial);
                wall.position.set(x, 2.5, z);
                wall.scale.set(width, 1, depth);
                scene.add(wall);
            }

            // Función para crear suelo
            function createFloor(x, z, width, depth) {
                const floor = new THREE.Mesh(floorGeometry, floorMaterial);
                floor.rotation.x = -Math.PI / 2;
                floor.position.set(x, 0, z);
                floor.scale.set(width, 1, depth);
                scene.add(floor);
            }

            // Función para crear techo
            function createCeiling(x, z, width, depth) {
                const ceiling = new THREE.Mesh(ceilingGeometry, ceilingMaterial);
                ceiling.rotation.x = Math.PI / 2;
                ceiling.position.set(x, 5, z);
                ceiling.scale.set(width, 1, depth);
                scene.add(ceiling);
            }

            // Sala principal
            const mainRoomWidth = 20;
            const mainRoomHeight = 8;
            const mainRoomDepth = 20;
            const wallThickness = 0.5;

            // Paredes
            createWall(0, -mainRoomDepth/2, mainRoomWidth, 1); // Pared norte
            createWall(0, mainRoomDepth/2, mainRoomWidth, 1);  // Pared sur
            createWall(-mainRoomWidth/2, 0, 1, mainRoomDepth); // Pared oeste
            createWall(mainRoomWidth/2, 0, 1, mainRoomDepth);  // Pared este

            // Suelo y techo
            createFloor(0, 0, mainRoomWidth, mainRoomDepth);
            createCeiling(0, 0, mainRoomWidth, mainRoomDepth);

            // Pasillo izquierdo
            const hallwayWidth = 4;
            const hallwayHeight = mainRoomHeight;
            const hallwayDepth = 10;

            createWall(-mainRoomWidth/2 - hallwayWidth/2, 0, 1, hallwayDepth);
            createWall(-mainRoomWidth/2 - hallwayWidth, 0, 1, hallwayDepth);
            createFloor(-mainRoomWidth/2 - hallwayWidth/2, 0, hallwayWidth, hallwayDepth);
            createCeiling(-mainRoomWidth/2 - hallwayWidth/2, 0, hallwayWidth, hallwayDepth);

            // Pasillo derecho
            createWall(mainRoomWidth/2 + hallwayWidth/2, 0, 1, hallwayDepth);
            createWall(mainRoomWidth/2 + hallwayWidth, 0, 1, hallwayDepth);
            createFloor(mainRoomWidth/2 + hallwayWidth/2, 0, hallwayWidth, hallwayDepth);
            createCeiling(mainRoomWidth/2 + hallwayWidth/2, 0, hallwayWidth, hallwayDepth);

            // Añadir cuadros en las paredes
            const paintings = [
                // Sala principal
                { position: [0, mainRoomHeight/2, -mainRoomDepth/2 + 0.5], rotation: [0, 0, 0], size: [3, 4], image: 'https://picsum.photos/400/500' },
                { position: [0, mainRoomHeight/2, mainRoomDepth/2 - 0.5], rotation: [0, Math.PI, 0], size: [3, 4], image: 'https://picsum.photos/401/500' },
                { position: [-mainRoomWidth/2 + 0.5, mainRoomHeight/2, 0], rotation: [0, Math.PI/2, 0], size: [3, 4], image: 'https://picsum.photos/402/500' },
                { position: [mainRoomWidth/2 - 0.5, mainRoomHeight/2, 0], rotation: [0, -Math.PI/2, 0], size: [3, 4], image: 'https://picsum.photos/403/500' },
                
                // Pasillo izquierdo
                { position: [-mainRoomWidth/2 - hallwayWidth/2, hallwayHeight/2, -hallwayDepth/2 + 0.5], rotation: [0, 0, 0], size: [2, 3], image: 'https://picsum.photos/404/500' },
                { position: [-mainRoomWidth/2 - hallwayWidth/2, hallwayHeight/2, hallwayDepth/2 - 0.5], rotation: [0, Math.PI, 0], size: [2, 3], image: 'https://picsum.photos/405/500' },
                { position: [-mainRoomWidth/2 - hallwayWidth + 0.5, hallwayHeight/2, 0], rotation: [0, Math.PI/2, 0], size: [2, 3], image: 'https://picsum.photos/406/500' },
                
                // Pasillo derecho
                { position: [mainRoomWidth/2 + hallwayWidth/2, hallwayHeight/2, -hallwayDepth/2 + 0.5], rotation: [0, 0, 0], size: [2, 3], image: 'https://picsum.photos/407/500' },
                { position: [mainRoomWidth/2 + hallwayWidth/2, hallwayHeight/2, hallwayDepth/2 - 0.5], rotation: [0, Math.PI, 0], size: [2, 3], image: 'https://picsum.photos/408/500' },
                { position: [mainRoomWidth/2 + hallwayWidth - 0.5, hallwayHeight/2, 0], rotation: [0, -Math.PI/2, 0], size: [2, 3], image: 'https://picsum.photos/409/500' }
            ];

            paintings.forEach(painting => {
                const texture = new THREE.TextureLoader().load(painting.image);
                texture.minFilter = THREE.LinearFilter;
                texture.magFilter = THREE.LinearFilter;
                const material = new THREE.MeshPhongMaterial({ 
                    map: texture,
                    side: THREE.DoubleSide,
                    transparent: true
                });
                const geometry = new THREE.PlaneGeometry(...painting.size);
                const mesh = new THREE.Mesh(geometry, material);
                mesh.position.set(...painting.position);
                mesh.rotation.set(...painting.rotation);
                scene.add(mesh);
            });

            // Posicionar cámara
            camera.position.set(0, 1.7, 0);
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

            // Controles de ratón para mirar alrededor
            let isPointerLocked = false;
            const sensitivity = 0.002;
            let pitchObject = new THREE.Object3D();
            let yawObject = new THREE.Object3D();
            yawObject.position.y = 1.7;
            yawObject.add(pitchObject);
            scene.add(yawObject);

            camera.position.set(0, 0, 0);
            pitchObject.add(camera);

            container.addEventListener('click', () => {
                container.requestPointerLock();
            });

            document.addEventListener('pointerlockchange', () => {
                isPointerLocked = document.pointerLockElement === container;
            });

            document.addEventListener('mousemove', (e) => {
                if (isPointerLocked) {
                    yawObject.rotation.y -= e.movementX * sensitivity;
                    pitchObject.rotation.x -= e.movementY * sensitivity;
                    pitchObject.rotation.x = Math.max(-Math.PI/2, Math.min(Math.PI/2, pitchObject.rotation.x));
                }
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
                    const nextPosition = yawObject.position.clone().add(movement);
                    const roomBounds = {
                        x: mainRoomWidth/2 - 1,
                        z: mainRoomDepth/2 - 1,
                        hallwayX: mainRoomWidth/2 + hallwayWidth - 1,
                        hallwayZ: hallwayDepth/2 - 1
                    };

                    if (Math.abs(nextPosition.x) < roomBounds.x || 
                        (nextPosition.x < -roomBounds.hallwayX && Math.abs(nextPosition.z) < roomBounds.hallwayZ) ||
                        (nextPosition.x > roomBounds.hallwayX && Math.abs(nextPosition.z) < roomBounds.hallwayZ)) {
                        yawObject.position.x = nextPosition.x;
                    }

                    if (Math.abs(nextPosition.z) < roomBounds.z || 
                        (Math.abs(nextPosition.x) > roomBounds.x && Math.abs(nextPosition.z) < roomBounds.hallwayZ)) {
                        yawObject.position.z = nextPosition.z;
                    }
                }

                renderer.render(scene, camera);
            }

            animate();
        });
    </script>
@endsection 