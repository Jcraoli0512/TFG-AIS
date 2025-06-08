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
    <!-- Modal de Detalles de Obra (2D) -->
    <div id="artworkDetailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[10000] flex items-center justify-center">
        <div class="relative mx-auto p-6 border w-11/12 md:w-1/2 lg:w-1/3 shadow-xl rounded-lg bg-white transform transition-all max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 id="artworkTitle" class="text-2xl font-bold text-gray-900"></h3>
                <button type="button" id="closeArtworkDetailModal" class="text-gray-400 hover:text-gray-600 text-3xl leading-none font-semibold">&times;</button>
            </div>
            <div class="mb-4">
                <p class="text-gray-700">Artista: <a id="artistNameLink" href="#" class="text-blue-600 hover:underline font-semibold"></a></p>
                <p id="artworkTechnique" class="text-gray-500 text-sm"></p>
                <p id="artworkYear" class="text-gray-500 text-sm"></p>
                <p id="artworkDescription" class="text-gray-600 mt-4 text-sm italic"></p>
            </div>
            <div class="flex justify-center mt-6">
                <img id="artworkImage" src="" alt="Imagen de Obra" class="max-w-full h-auto rounded-lg shadow-md max-h-96 object-contain">
            </div>
        </div>
    </div>

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
            
            console.log('Container dimensions - Width:', container.clientWidth, 'Height:', container.clientHeight); // Debug: Check container dimensions

            const scene = new THREE.Scene();
            const camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1, 1000);
            const renderer = new THREE.WebGLRenderer({ antialias: true });
            
            renderer.setSize(container.clientWidth, container.clientHeight);
            container.appendChild(renderer.domElement);

            // Objetos para control de la cámara
            let pitchObject = new THREE.Object3D();
            let yawObject = new THREE.Object3D();
            yawObject.position.y = 1.7; // Altura a nivel de los ojos
            yawObject.position.z = 0; // Iniciar en el centro del pasillo para probar
            yawObject.rotation.y = Math.PI; // Mirar hacia el interior del pasillo (eje Z negativo)
            yawObject.add(pitchObject);
            scene.add(yawObject);
            camera.position.set(0, 0, 0); // La cámara ya está en el pitchObject, su posición relativa es 0,0,0
            pitchObject.add(camera); // Añadir la cámara al pitchObject para que se mueva con él

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
            const spacing = corridorLength / 6; // Reducir de 11 a 6 para dar más espacio entre obras
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

            // Añadir luces ambientales para iluminar toda la sala
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.2);
            scene.add(ambientLight);

            // Añadir luces direccionales desde diferentes ángulos
            const directionalLight1 = new THREE.DirectionalLight(0xffffff, 0.3);
            directionalLight1.position.set(0, 10, 0);
            scene.add(directionalLight1);

            const directionalLight2 = new THREE.DirectionalLight(0xffffff, 0.2);
            directionalLight2.position.set(10, 5, 0);
            scene.add(directionalLight2);

            const directionalLight3 = new THREE.DirectionalLight(0xffffff, 0.2);
            directionalLight3.position.set(-10, 5, 0);
            scene.add(directionalLight3);

            // Añadir luces puntuales en el techo
            const ceilingLights = [];
            const lightSpacing = corridorLength / 6;
            for (let i = 0; i < 5; i++) {
                const light = new THREE.PointLight(0xffffff, 0.25, 15);
                light.position.set(0, wallHeight - 0.5, -corridorLength/2 + lightSpacing * (i+1));
                scene.add(light);
                ceilingLights.push(light);
            }

            // Array global para almacenar los grupos de obras
            const artworkGroups = [];

            // Función para añadir una obra a la escena 3D
            function addArtworkToScene(artwork, positionData) {
                const textureLoader = new THREE.TextureLoader();
                console.log('Loading artwork:', artwork.url); // Debug: Confirm artwork URL is being processed
                
                textureLoader.load(
                    artwork.url,
                    function(texture) {
                        console.log('Texture loaded successfully for:', artwork.title); // Debug: Confirm texture loaded
                        
                        // Configuración básica de la textura
                        texture.minFilter = THREE.LinearFilter;
                        texture.magFilter = THREE.LinearFilter;
                        texture.anisotropy = 4;
                        texture.generateMipmaps = true;
                        
                        // Obtener dimensiones originales de la imagen
                        const imageWidth = texture.image.width;
                        const imageHeight = texture.image.height;
                        const imageAspectRatio = imageWidth / imageHeight;

                        // Definir dimensiones máximas para la obra en la escena 3D
                        const maxArtworkHeight = wallHeight * 0.7; // Por ejemplo, el 70% de la altura de la pared
                        const maxArtworkWidth = corridorWidth * 0.7; // Por ejemplo, el 70% del ancho del pasillo

                        let artworkPlaneWidth;
                        let artworkPlaneHeight;

                        // Ajustar el tamaño de la obra manteniendo la proporción
                        if (imageAspectRatio > (maxArtworkWidth / maxArtworkHeight)) {
                            // La imagen es más ancha que alta en proporción, ajustar por ancho
                            artworkPlaneWidth = maxArtworkWidth;
                            artworkPlaneHeight = artworkPlaneWidth / imageAspectRatio;
                        } else {
                            // La imagen es más alta que ancha en proporción, ajustar por alto
                            artworkPlaneHeight = maxArtworkHeight;
                            artworkPlaneWidth = artworkPlaneHeight * imageAspectRatio;
                        }

                        // Asegurar que la obra no sea demasiado pequeña (opcional)
                        const minSize = 1.0; // Tamaño mínimo para evitar obras diminutas
                        if (artworkPlaneWidth < minSize) {
                            artworkPlaneWidth = minSize;
                            artworkPlaneHeight = artworkPlaneWidth / imageAspectRatio;
                        }
                        if (artworkPlaneHeight < minSize) {
                            artworkPlaneHeight = minSize;
                            artworkPlaneWidth = artworkPlaneHeight * imageAspectRatio;
                        }
                        
                        // Material para la obra
                        const artworkMaterial = new THREE.MeshBasicMaterial({
                            map: texture,
                            side: THREE.DoubleSide
                        });
                        
                        // Crear la geometría y el mesh de la obra con el tamaño calculado
                        const artworkGeometry = new THREE.PlaneGeometry(artworkPlaneWidth, artworkPlaneHeight);
                        const artworkMesh = new THREE.Mesh(artworkGeometry, artworkMaterial);
                        
                        // Dimensiones del marco (obra + un pequeño padding)
                        const framePadding = 0.2; // Relleno alrededor de la obra
                        const frameWidth = artworkPlaneWidth + framePadding;
                        const frameHeight = artworkPlaneHeight + framePadding;
                        const frameDepth = 0.1; // Grosor del marco

                        const frameGeometry = new THREE.BoxGeometry(frameWidth, frameHeight, frameDepth);
                        const frameMaterial = new THREE.MeshBasicMaterial({
                            color: 0x8B4513 // Color café para el marco
                        });
                        const frameMesh = new THREE.Mesh(frameGeometry, frameMaterial);

                        // Crear un grupo para contener el marco y la obra
                        const artworkGroup = new THREE.Group();
                        // Asignar la información de la obra al grupo
                        artworkGroup.userData.artwork = artwork;
                        // Añadir el marco y la obra al grupo
                        frameMesh.position.z = -frameDepth/2 - 0.01; // El marco detrás de la obra
                        artworkMesh.position.z = 0;
                        artworkGroup.add(frameMesh);
                        artworkGroup.add(artworkMesh);
                        // Posicionar y rotar el grupo
                        artworkGroup.position.set(...positionData.position);
                        artworkGroup.rotation.set(...positionData.rotation);
                        // Añadir el grupo a la escena
                        scene.add(artworkGroup);
                        // Guardar el grupo en el array global para el raycaster
                        artworkGroups.push(artworkGroup);
                    },
                    undefined,
                    function(err) {
                        console.error('Error loading texture:', err);
                    }
                );
            }

            // Obtener y mostrar las obras del día actual
            const today = new Date().toISOString().split('T')[0];
            console.log('Fetching artworks for date:', today); // Debug log
            
            fetch(`/api/gallery-images/${today}`)
                .then(response => response.json())
                .then(artworks => {
                    console.log('Received artworks:', artworks); // Debug: Log fetched artworks
                    if (artworks.length === 0) {
                        console.warn('No artworks found for today.'); // Warn if no artworks
                    }
                    artworks.slice(0, 10).forEach((artwork, index) => {
                        if (index < artworkPositions.length) {
                            // Ya no necesitamos pasar el 'size' fijo
                            addArtworkToScene(artwork, { 
                                position: artworkPositions[index].position, 
                                rotation: artworkPositions[index].rotation 
                            });
                        }
                    });
                })
                .catch(error => {
                    console.error('Error fetching artworks:', error);
                });

            // Mantener controles y animación existentes...
            // (Pega aquí el resto del código de controles y animación WASD, pointer lock, etc.)

            // --- CONTROLES Y ANIMACIÓN (igual que antes) ---
            const moveSpeed = 0.15;
            const keys = { w: false, a: false, s: false, d: false };
            let isPointerLocked = false;
            const sensitivity = 0.002;

            // Bloqueo del puntero
            container.addEventListener('click', function() {
                container.requestPointerLock();
            });

            // Interacción con las obras (clic)
            const artworkDetailModal = document.getElementById('artworkDetailModal');
            const artworkTitleElement = document.getElementById('artworkTitle');
            const artistNameLinkElement = document.getElementById('artistNameLink');
            const artworkDescriptionElement = document.getElementById('artworkDescription');
            const artworkImageElement = document.getElementById('artworkImage');
            const closeArtworkDetailModalButton = document.getElementById('closeArtworkDetailModal');

            function showArtworkDetailModal(artworkData) {
                document.getElementById('artworkTitle').textContent = artworkData.title;
                const artistNameLinkElement = document.getElementById('artistNameLink');
                artistNameLinkElement.textContent = artworkData.artist;
                artistNameLinkElement.href = `/profile/${artworkData.artist_id}`;
                document.getElementById('artworkTechnique').textContent = `Técnica: ${artworkData.technique}`;
                document.getElementById('artworkYear').textContent = `Año: ${artworkData.year || 'N/A'}`;
                document.getElementById('artworkDescription').textContent = `Descripción de la obra: ${artworkData.description}`;
                document.getElementById('artworkImage').src = artworkData.url;
                document.getElementById('artworkDetailModal').classList.remove('hidden');
                document.getElementById('artworkDetailModal').classList.add('flex');
                document.body.style.overflow = 'hidden'; // Evitar scroll del body
            }

            function closeArtworkDetailModal() {
                document.getElementById('artworkDetailModal').classList.add('hidden');
                document.getElementById('artworkDetailModal').classList.remove('flex');
                document.body.style.overflow = 'auto'; // Restaurar scroll del body
            }

            closeArtworkDetailModalButton.addEventListener('click', closeArtworkDetailModal);

            artworkDetailModal.addEventListener('click', function(event) {
                if (event.target === artworkDetailModal) {
                    closeArtworkDetailModal();
                }
            });

            const raycaster = new THREE.Raycaster();
            const mouse = new THREE.Vector2();

            function onDocumentMouseDown(event) {
                console.log('Click event detected on renderer.domElement!'); // DEBUG: Confirm event fired
                event.preventDefault();
                mouse.x = (event.clientX / renderer.domElement.clientWidth) * 2 - 1;
                mouse.y = -(event.clientY / renderer.domElement.clientHeight) * 2 + 1;
                raycaster.setFromCamera(mouse, camera);
                // Solo intersectar contra los grupos de obras
                const intersects = raycaster.intersectObjects(artworkGroups, true);
                if (intersects.length > 0) {
                    console.log('Intersection detected!', intersects[0].object); // DEBUG: Confirm intersection
                    let clickedObject = intersects[0].object;
                    // Subir por la jerarquía hasta encontrar el grupo con userData.artwork
                    while (clickedObject && !clickedObject.userData.artwork) {
                        clickedObject = clickedObject.parent;
                    }
                    if (clickedObject && clickedObject.userData.artwork) {
                        const artwork = clickedObject.userData.artwork;
                        console.log('Artwork clicked:', artwork.title); // DEBUG: Confirm artwork identified
                        showArtworkDetailModal(artwork);
                    } else {
                        console.log('Clicked object is not an artwork or part of an artwork group.'); // DEBUG: No artwork found
                    }
                } else {
                    console.log('No intersection detected.'); // DEBUG: No intersection
                }
            }
            console.log('Adding event listeners to renderer.domElement...'); // DEBUG: Confirm listener attachment attempt
            renderer.domElement.addEventListener('mousedown', onDocumentMouseDown, false);
            renderer.domElement.addEventListener('click', onDocumentMouseDown, false); // Adding click listener as well
            console.log('Event listeners added.'); // DEBUG: Confirm listeners added

            document.addEventListener('pointerlockchange', function() {
                isPointerLocked = document.pointerLockElement === container;
            });

            document.addEventListener('mousemove', function(event) {
                if (isPointerLocked) {
                    yawObject.rotation.y -= event.movementX * sensitivity;
                    pitchObject.rotation.x -= event.movementY * sensitivity;
                    pitchObject.rotation.x = Math.max(-Math.PI / 2, Math.min(Math.PI / 2, pitchObject.rotation.x));
                }
            });

            document.addEventListener('keydown', function(event) {
                if (isPointerLocked) {
                    keys[event.key.toLowerCase()] = true;
                }
            });

            document.addEventListener('keyup', function(event) {
                if (isPointerLocked) {
                    keys[event.key.toLowerCase()] = false;
                }
            });

            // Bucle de animación
            function animate() {
                requestAnimationFrame(animate);

                if (isPointerLocked) {
                    const direction = new THREE.Vector3();
                    if (keys.w) direction.z -= 1;
                    if (keys.s) direction.z += 1;
                    if (keys.a) direction.x -= 1;
                    if (keys.d) direction.x += 1;

                    if (direction.lengthSq() > 0) {
                        direction.normalize();
                        // Aplicar la rotación del yawObject para que el movimiento sea relativo a la dirección de la mirada
                        direction.applyQuaternion(yawObject.quaternion);
                        yawObject.position.addScaledVector(direction, moveSpeed);

                        // Opcional: Limitar el movimiento para que no se salga del pasillo
                        const halfCorridorWidth = corridorWidth / 2 - 0.7; // Margen para evitar salir
                        const halfCorridorLength = corridorLength / 2 - 1; // Margen para evitar salir

                        if (Math.abs(yawObject.position.x) > halfCorridorWidth) {
                            yawObject.position.x = Math.sign(yawObject.position.x) * halfCorridorWidth;
                        }
                        if (Math.abs(yawObject.position.z) > halfCorridorLength) {
                            yawObject.position.z = Math.sign(yawObject.position.z) * halfCorridorLength;
                        }
                    }
                }

                renderer.render(scene, camera);
            }
            animate();

            // Manejar redimensionamiento de la ventana
            window.addEventListener('resize', function() {
                camera.aspect = container.clientWidth / container.clientHeight;
                camera.updateProjectionMatrix();
                renderer.setSize(container.clientWidth, container.clientHeight);
            });
        });
    </script>
@endpush 