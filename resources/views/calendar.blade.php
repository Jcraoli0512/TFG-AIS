@extends('layouts.app')

@section('title', 'Calendario - Art Indie Space')

@section('styles')
    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
@endsection

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Calendario -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-6">
                    <div id="calendar"></div>
                </div>
            </div>

            <!-- Panel lateral -->
            <div class="lg:col-span-1">
                <!-- Galería de imágenes -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">Galería del día</h2>
                    <div id="gallery" class="grid grid-cols-1 gap-4">
                        <!-- Las imágenes se cargarán aquí dinámicamente -->
                    </div>
                </div>

                <!-- Formulario de selección de fecha -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Seleccionar fecha para tu obra</h2>
                    <form id="artworkDateForm" class="space-y-4">
                        @csrf
                        {{-- Campo oculto para almacenar IDs de obras seleccionadas --}}
                        <input type="hidden" name="artwork_ids" id="selectedArtworkIdsInput" value="">

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Obras seleccionadas</label>
                            <div id="selectedArtworksDisplay" class="mt-1 text-sm text-gray-900">Ninguna obra seleccionada</div>
                            <button type="button" id="openArtworkSelectionModal" class="mt-2 inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Seleccionar Obras
                            </button>
                        </div>

                        <div>
                            <label for="display_date" class="block text-sm font-medium text-gray-700">Fecha de exhibición</label>
                            <input type="date" id="display_date" name="display_date" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   min="{{ date('Y-m-d') }}">
                        </div>

                        <div>
                            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Solicitar Exhibición
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para seleccionar obras --}}
    <div id="artworkSelectionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border max-w-2xl w-full shadow-lg rounded-md bg-white">
            <!-- Modal Header -->
            <div class="pb-3 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-bold">Seleccionar Obras</h3>
                <button type="button" class="close-artwork-selection-modal text-gray-400 hover:text-gray-600 text-2xl font-semibold leading-none">&times;</button>
            </div>

            <!-- Modal Body - Lista de obras se cargará aquí -->
            <div id="artworkSelectionModalBody" class="py-4 max-h-96 overflow-y-auto">
                <p>Cargando obras...</p>
            </div>

            <!-- Modal Footer -->
            <div class="pt-3 border-t border-gray-200 flex justify-end">
                 <button type="button" class="close-artwork-selection-modal inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mr-3">
                    Cancelar
                </button>
                <button type="button" id="saveArtworkSelection" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Confirmar Selección
                </button>
            </div>
        </div>
    </div>

    {{-- Modal para mostrar detalles del evento --}}
    <div id="eventModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4">
            <div class="mb-4">
                <h3 id="eventTitle" class="text-xl font-bold"></h3>
            </div>
            <div id="eventDescription" class="text-gray-600 mb-4"></div>
            <div id="eventDates" class="text-sm text-gray-500"></div>
            <div class="mt-6 flex justify-end">
                <button onclick="closeEventModal()" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    {{-- Nuevo Modal para Ampliar Imagen de Obra --}}
    <div id="enlargeImageModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full hidden z-[100]"> {{-- Use a higher z-index --}}
        <div class="relative top-10 mx-auto p-4 max-w-4xl w-full">
            <div class="bg-white rounded-lg shadow-xl overflow-hidden">
                <div class="relative">
                    <img id="enlargedArtworkImage" src="" alt="Imagen Ampliada" class="w-full h-auto max-h-[80vh] object-contain mx-auto">
                    {{-- Close button position over the image/container --}}
                    <button type="button" class="absolute top-2 right-2 text-white bg-black bg-opacity-50 rounded-full p-1 hover:bg-opacity-75 focus:outline-none close-enlarge-image-modal">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para mostrar obras exhibidas --}}
    <div id="exhibitedArtworksModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden z-[9999]">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4 sticky top-0 bg-white z-10 pb-2">
                        <h3 class="text-lg font-medium text-gray-900" id="exhibitedArtworksTitle">Obras Exhibidas</h3>
                        <button type="button" onclick="closeExhibitedArtworksModal()" class="text-gray-400 hover:text-gray-500">
                            <span class="sr-only">Cerrar</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div id="exhibitedArtworksContent" class="space-y-4">
                        {{-- El contenido se llenará dinámicamente --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'>
        console.log('FullCalendar JS loaded via CDN'); // Debug: Check if CDN script loads
    </script>
    <script>
        console.log('Calendar script loaded'); // Debug: Check if calendar script block loads

        let calendar; // Declare calendar variable in a broader scope

        document.addEventListener('DOMContentLoaded', function() {
            console.log('Calendar DOM fully loaded'); // Debug: Check if DOMContentLoaded fires

            const calendarEl = document.getElementById('calendar');
            console.log('Calendar element:', calendarEl); // Debug: Check if calendar element is found

            if (!calendarEl) {
                console.error('Error: Calendar element not found!');
                return; // Stop execution if element is not found
            }

            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: {
                    today: 'Hoy',
                    month: 'Mes',
                    week: 'Semana',
                    day: 'Día'
                },
                events: '/api/calendar-events',
                eventClick: function(info) {
                    showExhibitedArtworks(info.event.start);
                },
                dateClick: function(info) {
                    // Actualizar el campo de fecha en el formulario
                    document.getElementById('display_date').value = info.dateStr;
                    // Cargar las imágenes de la galería para esta fecha
                    loadGalleryImages(info.date);
                },
                eventDidMount: function(info) {
                    // Añadir tooltip con la descripción
                    info.el.title = info.event.extendedProps.description;
                },
                // Traducciones adicionales
                dayHeaderFormat: { weekday: 'long' },
                titleFormat: { year: 'numeric', month: 'long' },
                allDayText: 'Todo el día',
                noEventsText: 'No hay eventos',
                moreLinkText: 'más',
                weekText: 'Sem.',
                weekNumbersTitle: 'S',
                firstDay: 1, // Comienza la semana en lunes
                // Traducciones de los meses
                monthNames: [
                    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                ],
                // Traducciones de los meses cortos
                monthNamesShort: [
                    'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
                    'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
                ],
                // Traducciones de los días de la semana
                dayNames: [
                    'Domingo', 'Lunes', 'Martes', 'Miércoles',
                    'Jueves', 'Viernes', 'Sábado'
                ],
                // Traducciones de los días de la semana cortos
                dayNamesShort: [
                    'Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'
                ]
            });
            calendar.render();
            console.log('FullCalendar rendered'); // Debug: Check if render is called
        });

        function showEventModal(event) {
            const modal = document.getElementById('eventModal');
            const title = document.getElementById('eventTitle');
            const description = document.getElementById('eventDescription');
            const dates = document.getElementById('eventDates');

            title.textContent = event.title;
            description.textContent = event.extendedProps.description || 'No hay descripción disponible.';
            
            const startDate = new Date(event.start).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            const endDate = new Date(event.end).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            dates.textContent = `Del ${startDate} al ${endDate}`;
            
            modal.classList.remove('hidden');
        }

        function closeEventModal() {
            document.getElementById('eventModal').classList.add('hidden');
        }

        // Cerrar el modal al hacer clic fuera de él
        document.getElementById('eventModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeEventModal();
            }
        });

        // Cargar imágenes de la galería
        function loadGalleryImages(date) {
            const formattedDate = date.toISOString().split('T')[0];
            fetch(`/api/gallery-images/${formattedDate}`)
                .then(response => response.json())
                .then(images => {
                    const gallery = document.getElementById('gallery');
                    gallery.innerHTML = images.map(image => `
                        <div class="relative group">
                            <img src="${image.url}" alt="${image.title}" class="w-full h-48 object-cover rounded-lg">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-300 rounded-lg flex items-center justify-center">
                                <div class="text-white opacity-0 group-hover:opacity-100 text-center p-4">
                                    <h3 class="font-semibold">${image.title}</h3>
                                    <p class="text-sm">por ${image.artist}</p>
                                </div>
                            </div>
                        </div>
                    `).join('');
                });
        }

        // --- Lógica para el modal de selección de obras ---
        const artworkSelectionModal = document.getElementById('artworkSelectionModal');
        const artworkSelectionModalBody = document.getElementById('artworkSelectionModalBody');
        const openArtworkSelectionModalButton = document.getElementById('openArtworkSelectionModal');
        const saveArtworkSelectionButton = document.getElementById('saveArtworkSelection');
        const selectedArtworkIdsInput = document.getElementById('selectedArtworkIdsInput');
        const selectedArtworksDisplay = document.getElementById('selectedArtworksDisplay');

        // Function to handle click on artwork image in the selection modal to enlarge
        function handleArtworkImageClick(event) {
            const clickedImage = event.target;
            if (clickedImage.tagName === 'IMG') {
                const imageUrl = clickedImage.src;
                openEnlargeImageModal(imageUrl);
            }
        }

        // Attach event listeners to artwork images in the selection modal body
        // This function will be called after the artwork selection partial is loaded
        function attachImageEnlargeListeners() {
            const artworkImages = artworkSelectionModalBody.querySelectorAll('#artworkSelectionModalBody img');
            artworkImages.forEach(img => {
                img.style.cursor = 'pointer'; // Add a pointer cursor to indicate clickability
                img.addEventListener('click', handleArtworkImageClick);
            });
             console.log('Image enlarge listeners attached', artworkImages.length);
        }

        // Function to open the enlarge image modal
        const enlargeImageModal = document.getElementById('enlargeImageModal');
        const enlargedArtworkImage = document.getElementById('enlargedArtworkImage');

        function openEnlargeImageModal(imageUrl) {
            enlargedArtworkImage.src = imageUrl;
            enlargeImageModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        // Function to close the enlarge image modal
        function closeEnlargeImageModal() {
            enlargeImageModal.classList.add('hidden');
            enlargedArtworkImage.src = ''; // Clear the image source
            document.body.style.overflow = ''; // Restore background scrolling
        }

        // Handle close button click for enlarge image modal
        const closeEnlargeImageButtons = enlargeImageModal.querySelectorAll('.close-enlarge-image-modal');
        closeEnlargeImageButtons.forEach(button => {
            button.addEventListener('click', closeEnlargeImageModal);
        });

        // Handle click outside the enlarge image modal content to close it
        if (enlargeImageModal) {
             enlargeImageModal.addEventListener('click', function(event) {
                const isClickInsideModalContent = event.target.closest('.relative.top-10.mx-auto'); // Selects the modal content div
                if (event.target === this && !isClickInsideModalContent) {
                    closeEnlargeImageModal();
                }
            });
        }

        // Función para abrir el modal de selección de obras
        if (openArtworkSelectionModalButton) {
            openArtworkSelectionModalButton.addEventListener('click', function() {
                console.log('Opening artwork selection modal'); // Debug log
                artworkSelectionModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden'; // Prevent scrolling on body

                // Cargar la lista de obras en el cuerpo del modal
                artworkSelectionModalBody.innerHTML = '<p>Cargando obras...</p>'; // Show loading

                fetch('{{ route('artworks.selection-partial') }}')
                    .then(response => {
                        if (!response.ok) {
                            // Log the error response status
                            console.error('Error response from artwork selection partial:', response.status, response.statusText);
                             throw new Error('Network response was not ok ' + response.statusText);
                        }
                        return response.text();
                    })
                    .then(html => {
                        artworkSelectionModalBody.innerHTML = html;
                        console.log('Artwork selection partial loaded successfully'); // Debug log
                        attachImageEnlargeListeners(); // Attach listeners after content is loaded
                    })
                    .catch(error => {
                        console.error('Error al cargar la lista de obras:', error);
                        artworkSelectionModalBody.innerHTML = '<p class="text-red-600">Error al cargar las obras.</p>';
                    });
            });
        }

        // Función para cerrar el modal de selección de obras
        function closeArtworkSelectionModal() {
            console.log('Closing artwork selection modal'); // Debug log
            artworkSelectionModal.classList.add('hidden');
            document.body.style.overflow = ''; // Restore scrolling on body
        }

        // Manejar clics en el botón de cerrar del modal
        const closeArtworkModalButtons = artworkSelectionModal.querySelectorAll('.close-artwork-selection-modal');
        closeArtworkModalButtons.forEach(button => {
            button.addEventListener('click', closeArtworkSelectionModal);
        });

        // Manejar clic fuera del modal para cerrarlo
        // Using event delegation on the modal itself for clicks outside the content
        if (artworkSelectionModal) {
            artworkSelectionModal.addEventListener('click', function(event) {
                // Check if the click target is the modal backdrop itself, not the modal content
                const isClickInsideModalContent = event.target.closest('.relative.top-20.mx-auto'); // Selects the modal content div
                if (event.target === this && !isClickInsideModalContent) {
                    closeArtworkSelectionModal();
                }
            });
        }

        // Manejar clic en el botón de confirmar selección
        if (saveArtworkSelectionButton) {
            saveArtworkSelectionButton.addEventListener('click', function() {
                const selectedCheckboxes = artworkSelectionModalBody.querySelectorAll('input[name="selected_artworks[]"]:checked');
                const selectedIds = Array.from(selectedCheckboxes).map(checkbox => checkbox.value);
                const selectedTitles = Array.from(selectedCheckboxes).map(checkbox => checkbox.closest('label').querySelector('span').textContent);

                // Actualizar el campo oculto y el display de obras seleccionadas en el formulario principal
                selectedArtworkIdsInput.value = JSON.stringify(selectedIds);
                selectedArtworksDisplay.textContent = selectedTitles.length > 0 ? selectedTitles.join(', ') : 'Ninguna obra seleccionada';

                closeArtworkSelectionModal();
            });
        }

        // --- Manejar el envío del formulario principal (actualizado para usar el campo oculto) ---
        const artworkDateForm = document.getElementById('artworkDateForm');
        artworkDateForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            // Obtener los IDs seleccionados del campo oculto
            const selectedArtworkIdsString = selectedArtworkIdsInput.value;
            const selectedArtworkIds = selectedArtworkIdsString ? JSON.parse(selectedArtworkIdsString) : [];

            // Verificar si se ha seleccionado al menos una obra (esta validación ahora es redundante si se valida en el modal, pero la mantenemos por seguridad)
            if (selectedArtworkIds.length === 0) {
                alert('Por favor, selecciona al menos una obra para la exhibición.');
                return; // Detener el envío del formulario
            }

            fetch('/api/artwork-display-dates', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    artwork_ids: selectedArtworkIds,
                    display_date: formData.get('display_date')
                })
            })
            .then(response => {
                if (!response.ok) {
                    // Si la respuesta no es OK, intentar leer los errores de validación
                    return response.json().then(data => {
                        if (response.status === 422 && data.errors) {
                            // Mostrar errores de validación específicos
                            let errorMessages = 'Error de validación:\n';
                            for (const field in data.errors) {
                                errorMessages += `- ${data.errors[field].join(', ')}\n`;
                            }
                            alert(errorMessages);
                        } else if (data.error) {
                            // Mostrar otros errores del servidor
                            alert(data.error);
                        } else {
                            // Mostrar un error genérico si no hay mensajes específicos
                            alert('Ha ocurrido un error al procesar tu solicitud');
                        }
                        // Lanzar un error para que el catch lo maneje si es necesario
                        throw new Error(data.message || 'Error en la respuesta del servidor');
                    });
                }
                // Si la respuesta es OK, parsear el JSON normalmente
                return response.json();
            })
            .then(data => {
                if (data.message) {
                    alert(data.message);
                    if (calendar) { // Check if calendar is defined before calling refetchEvents
                       calendar.refetchEvents();
                    }
                    this.reset();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Este catch ahora es más para errores de red o errores no manejados por el then
                // Ya no debería mostrar la alerta genérica si la validación falló (código 422)
            });
        });

        // Cargar imágenes del día actual al inicio
        loadGalleryImages(new Date());

        function showExhibitedArtworks(date) {
            const modal = document.getElementById('exhibitedArtworksModal');
            const content = document.getElementById('exhibitedArtworksContent');
            const title = document.getElementById('exhibitedArtworksTitle');
            
            // Formatear la fecha para el título
            const formattedDate = new Date(date).toLocaleDateString('es-ES', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            title.textContent = `Obras en el Espacio 3D - ${formattedDate}`;

            // Obtener las obras exhibidas para esta fecha
            fetch(`/api/gallery-images/${date.toISOString().split('T')[0]}`)
                .then(response => response.json())
                .then(artworks => {
                    content.innerHTML = artworks.length > 0 
                        ? artworks.map(artwork => `
                            <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                                <img src="${artwork.url}" alt="${artwork.title}" class="w-24 h-24 object-cover rounded">
                                <div>
                                    <h4 class="font-medium text-gray-900">${artwork.title}</h4>
                                    <p class="text-sm text-gray-500">Artista: ${artwork.artist}</p>
                                    <p class="text-sm text-gray-600">Técnica: ${artwork.technique}</p>
                                    <p class="text-sm text-gray-600 mt-1">${artwork.description}</p>
                                    ${artwork.is_owner ? `
                                        <button type="button" 
                                                onclick="cancelExhibitionDate(${artwork.display_date_id})" 
                                                class="mt-2 inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            Cancelar Exhibición
                                        </button>
                                    ` : ''}
                                </div>
                            </div>
                        `).join('')
                        : '<p class="text-gray-500">No hay obras programadas para esta fecha en el espacio 3D.</p>';
                    
                    modal.classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                    content.innerHTML = '<p class="text-red-500">Error al cargar las obras exhibidas.</p>';
                    modal.classList.remove('hidden');
                });
        }

        function closeExhibitedArtworksModal() {
            document.getElementById('exhibitedArtworksModal').classList.add('hidden');
        }

        // Función para cancelar una fecha de exhibición
        function cancelExhibitionDate(displayDateId) {
            if (confirm('¿Estás seguro de que deseas cancelar esta exhibición?')) {
                fetch(`/api/artwork-display-dates/${displayDateId}/cancel`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.error || 'Error al cancelar la exhibición');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    alert(data.message);
                    // Cerrar el modal y refrescar el calendario
                    closeExhibitedArtworksModal();
                    if (calendar) {
                        calendar.refetchEvents();
                    }
                })
                .catch(error => {
                    console.error('Error cancelling exhibition date:', error);
                    alert(error.message);
                });
            }
        }
    </script>
@endpush 