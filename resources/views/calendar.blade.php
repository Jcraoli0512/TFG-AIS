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

    {{-- Modal de Confirmación de Cancelación --}}
    <div id="cancelConfirmationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[10000]">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Confirmar Cancelación</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">¿Estás seguro de que deseas cancelar esta exhibición? Esta acción no se puede deshacer.</p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="cancelCancelButton" class="px-4 py-2 mr-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md w-1/2 shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200">
                        No, Mantener
                    </button>
                    <button id="confirmCancelButton" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-1/2 shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                        Sí, Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de confirmación de solicitud (usado también para mensajes generales) --}}
    <div id="confirmationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[10000]">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">¡Solicitud Enviada!</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Tu solicitud de exhibición ha sido enviada correctamente. El administrador la revisará y te notificará cuando sea aprobada.
                    </p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="closeConfirmationModal" class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                        Aceptar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Confirmación de Cancelación Masiva --}}
    <div id="cancelAllConfirmationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[10000]">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Confirmar Cancelación Masiva</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">¿Estás seguro de que deseas cancelar todas las exhibiciones para esta fecha? Esta acción no se puede deshacer.</p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="cancelAllCancelButton" class="px-4 py-2 mr-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md w-1/2 shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-200">
                        No, Mantener
                    </button>
                    <button id="confirmCancelAllButton" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-1/2 shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                        Sí, Cancelar Todo
                    </button>
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

        // --- Manejar el envío del formulario principal ---
        const artworkDateForm = document.getElementById('artworkDateForm');
        const confirmationModal = document.getElementById('confirmationModal');
        const closeConfirmationModal = document.getElementById('closeConfirmationModal');

        artworkDateForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const selectedArtworkIdsString = selectedArtworkIdsInput.value;
            const selectedArtworkIds = selectedArtworkIdsString ? JSON.parse(selectedArtworkIdsString) : [];

            if (selectedArtworkIds.length === 0) {
                alert('Por favor, selecciona al menos una obra para la exhibición.');
                return;
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
                    return response.json().then(data => {
                        throw new Error(data.error || 'Error al procesar la solicitud');
                    });
                }
                return response.json();
            })
            .then(data => {
                // Mostrar modal de éxito
                showGeneralMessageModal('Solicitud Enviada', data.message || 'Tu solicitud de exhibición ha sido enviada correctamente. El administrador la revisará y te notificará cuando sea aprobada.', 'success');
                
                // Limpiar el formulario
                document.getElementById('artworkDateForm').reset();
                selectedArtworkIdsInput.value = '';
                selectedArtworksDisplay.textContent = 'Ninguna obra seleccionada';

                // Refrescar el calendario
                if (calendar) {
                    calendar.refetchEvents();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Mostrar modal de error
                showGeneralMessageModal('Error', error.message || 'Ha ocurrido un error al procesar tu solicitud.', 'error');
            });
        });

        // Cerrar el modal de confirmación
        closeConfirmationModal.addEventListener('click', function() {
            confirmationModal.classList.add('hidden');
            document.body.style.overflow = '';
        });

        // Cerrar el modal de confirmación al hacer clic fuera
        confirmationModal.addEventListener('click', function(event) {
            if (event.target === this) {
                confirmationModal.classList.add('hidden');
                document.body.style.overflow = '';
            }
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
                    console.log('Artworks received:', artworks); // Debug log
                    
                    // Verificar si el usuario es admin o propietario de alguna obra
                    const isAdmin = artworks.some(artwork => artwork.is_admin);
                    const isOwner = artworks.some(artwork => artwork.is_owner);
                    
                    // Crear el contenido del modal
                    let modalContent = '';
                    
                    // Añadir botón de cancelación masiva si es admin o propietario
                    if (isAdmin || isOwner) {
                        modalContent += `
                            <div class="mb-4 flex justify-end">
                                <button type="button" 
                                        onclick="cancelAllExhibitions('${date.toISOString().split('T')[0]}', ${isAdmin})" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    ${isAdmin ? 'Cancelar Todas las Exhibiciones (Admin)' : 'Cancelar Todas mis Exhibiciones'}
                                </button>
                            </div>
                        `;
                    }
                    
                    // Añadir la lista de obras
                    modalContent += artworks.length > 0 
                        ? artworks.map(artwork => {
                            console.log('Processing artwork:', artwork); // Debug log for each artwork
                            return `
                            <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                                <img src="${artwork.url}" alt="${artwork.title}" class="w-24 h-24 object-cover rounded">
                                <div>
                                    <h4 class="font-medium text-gray-900">${artwork.title}</h4>
                                    <p class="text-sm text-gray-500">Artista: ${artwork.artist}</p>
                                    <p class="text-sm text-gray-600">Técnica: ${artwork.technique}</p>
                                    <p class="text-sm text-gray-600 mt-1">${artwork.description}</p>
                                    ${(artwork.is_owner || artwork.is_admin) ? `
                                        <button type="button" 
                                                onclick="cancelExhibitionDate(${artwork.display_date_id}, ${artwork.is_admin})" 
                                                class="mt-2 inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            ${artwork.is_admin ? 'Cancelar Exhibición (Admin)' : 'Cancelar Exhibición'}
                                        </button>
                                    ` : ''}
                                </div>
                            </div>
                        `}).join('')
                        : '<p class="text-gray-500">No hay obras programadas para esta fecha en el espacio 3D.</p>';
                    
                    content.innerHTML = modalContent;
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

        // --- Lógica para el modal de confirmación de cancelación ---
        const cancelConfirmationModal = document.getElementById('cancelConfirmationModal');
        const cancelCancelButton = document.getElementById('cancelCancelButton');
        const confirmCancelButton = document.getElementById('confirmCancelButton');
        
        let currentDisplayDateIdToCancel = null; // Para almacenar el ID a cancelar

        // Función para mostrar el modal de confirmación de cancelación
        function showCancelConfirmationModal(displayDateId) {
            currentDisplayDateIdToCancel = displayDateId;
            cancelConfirmationModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        // Función para cerrar el modal de confirmación de cancelación
        function closeCancelConfirmationModal() {
            cancelConfirmationModal.classList.add('hidden');
            document.body.style.overflow = '';
            currentDisplayDateIdToCancel = null;
        }

        // Listener para el botón 'No, Mantener'
        cancelCancelButton.addEventListener('click', function() {
            closeCancelConfirmationModal();
        });

        // Listener para el botón 'Sí, Cancelar'
        confirmCancelButton.addEventListener('click', function() {
            if (currentDisplayDateIdToCancel !== null) {
                executeCancellation(currentDisplayDateIdToCancel);
            }
            closeCancelConfirmationModal();
        });

        // Cerrar modal de cancelación haciendo clic fuera
        cancelConfirmationModal.addEventListener('click', function(event) {
            if (event.target === cancelConfirmationModal) {
                closeCancelConfirmationModal();
            }
        });

        // --- Lógica para mostrar mensajes en el modal general de confirmación (#confirmationModal) ---
        // Reutilizamos este modal para mostrar mensajes de éxito o error de varias acciones
        const generalMessageModal = document.getElementById('confirmationModal'); // El mismo modal usado para confirmación de envío
        const generalMessageTitle = generalMessageModal.querySelector('h3');
        const generalMessageText = generalMessageModal.querySelector('p');
        const generalMessageIconDiv = generalMessageModal.querySelector('.mx-auto.flex');
        const generalMessageCloseButton = generalMessageModal.querySelector('button'); // Assuming there's only one button with the close behavior

        function showGeneralMessageModal(title, message, type = 'success') {
            const modal = document.getElementById('confirmationModal');
            const modalTitle = modal.querySelector('h3');
            const modalMessage = modal.querySelector('p');
            const modalIcon = modal.querySelector('.mx-auto.flex');
            const modalButton = modal.querySelector('button');

            // Configurar el ícono según el tipo
            if (type === 'success') {
                modalIcon.innerHTML = `
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                `;
                modalIcon.classList.remove('bg-yellow-100', 'bg-red-100');
                modalIcon.classList.add('bg-green-100');
                modalTitle.classList.remove('text-yellow-600', 'text-red-600');
                modalTitle.classList.add('text-green-600');
                modalButton.classList.remove('bg-yellow-500', 'hover:bg-yellow-600', 'focus:ring-yellow-300', 'bg-red-500', 'hover:bg-red-600', 'focus:ring-red-300');
                modalButton.classList.add('bg-green-500', 'hover:bg-green-600', 'focus:ring-green-300');
            } else if (type === 'error') {
                modalIcon.innerHTML = `
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                `;
                modalIcon.classList.remove('bg-green-100', 'bg-yellow-100');
                modalIcon.classList.add('bg-red-100');
                modalTitle.classList.remove('text-green-600', 'text-yellow-600');
                modalTitle.classList.add('text-red-600');
                modalButton.classList.remove('bg-green-500', 'hover:bg-green-600', 'focus:ring-green-300', 'bg-yellow-500', 'hover:bg-yellow-600', 'focus:ring-yellow-300');
                modalButton.classList.add('bg-red-500', 'hover:bg-red-600', 'focus:ring-red-300');
            } else if (type === 'warning') {
                modalIcon.innerHTML = `
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                `;
                modalIcon.classList.remove('bg-green-100', 'bg-red-100');
                modalIcon.classList.add('bg-yellow-100');
                modalTitle.classList.remove('text-green-600', 'text-red-600');
                modalTitle.classList.add('text-yellow-600');
                modalButton.classList.remove('bg-green-500', 'hover:bg-green-600', 'focus:ring-green-300', 'bg-red-500', 'hover:bg-red-600', 'focus:ring-red-300');
                modalButton.classList.add('bg-yellow-500', 'hover:bg-yellow-600', 'focus:ring-yellow-300');
            }

            // Configurar título y mensaje
            modalTitle.textContent = title;
            modalMessage.textContent = message;

            // Mostrar el modal
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        // Listener para cerrar el modal de mensaje general
        generalMessageCloseButton.addEventListener('click', function() {
            generalMessageModal.classList.add('hidden');
            document.body.style.overflow = '';
        });

         // Cerrar modal de mensaje general haciendo clic fuera
        generalMessageModal.addEventListener('click', function(event) {
            if (event.target === generalMessageModal) {
                generalMessageModal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });

        // --- Funciones de ejecución de acciones --- (Anteriormente dentro de confirm/alert)
        
        // Función para ejecutar la cancelación
        function executeCancellation(displayDateId) {
            fetch(`/api/artwork-display-dates/${displayDateId}/cancel`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
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
                // Mostrar modal de éxito
                showGeneralMessageModal('Cancelación Exitosa', data.message || 'La exhibición ha sido cancelada correctamente.', 'success');
                
                // Cerrar el modal de obras exhibidas y refrescar el calendario
                closeExhibitedArtworksModal();
                if (calendar) {
                    calendar.refetchEvents();
                }
            })
            .catch(error => {
                console.error('Error cancelling exhibition date:', error);
                // Mostrar modal de error
                showGeneralMessageModal('Error de Cancelación', error.message || 'Ha ocurrido un error al cancelar la exhibición.', 'error');
            });
        }

        // --- Modificar la función original cancelExhibitionDate --- 
        // Ahora solo mostrará el modal de confirmación
        function cancelExhibitionDate(displayDateId) {
             if (!displayDateId) {
                console.error('No display date ID provided');
                 showGeneralMessageModal('Error', 'No se pudo identificar la fecha de exhibición.', 'error');
                return;
            }
            showCancelConfirmationModal(displayDateId);
        }

        // Variables para la cancelación masiva
        const cancelAllConfirmationModal = document.getElementById('cancelAllConfirmationModal');
        const cancelAllCancelButton = document.getElementById('cancelAllCancelButton');
        const confirmCancelAllButton = document.getElementById('confirmCancelAllButton');
        let currentCancelAllDate = null;
        let isAdminCancelAll = false;

        // Función para mostrar el modal de confirmación de cancelación masiva
        function cancelAllExhibitions(date, isAdmin) {
            currentCancelAllDate = date;
            isAdminCancelAll = isAdmin;
            cancelAllConfirmationModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        // Función para cerrar el modal de confirmación de cancelación masiva
        function closeCancelAllConfirmationModal() {
            cancelAllConfirmationModal.classList.add('hidden');
            document.body.style.overflow = '';
            currentCancelAllDate = null;
            isAdminCancelAll = false;
        }

        // Listener para el botón 'No, Mantener'
        cancelAllCancelButton.addEventListener('click', closeCancelAllConfirmationModal);

        // Listener para el botón 'Sí, Cancelar Todo'
        confirmCancelAllButton.addEventListener('click', function() {
            if (currentCancelAllDate) {
                executeCancelAll(currentCancelAllDate, isAdminCancelAll);
            }
            closeCancelAllConfirmationModal();
        });

        // Cerrar modal de cancelación masiva haciendo clic fuera
        cancelAllConfirmationModal.addEventListener('click', function(event) {
            if (event.target === cancelAllConfirmationModal) {
                closeCancelAllConfirmationModal();
            }
        });

        // Función para ejecutar la cancelación masiva
        function executeCancelAll(date, isAdmin) {
            console.log('Executing cancel all for date:', date); // Debug log
            fetch(`/api/artwork-display-dates/cancel-all/${date}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                console.log('Response status:', response.status); // Debug log
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.error || 'Error al cancelar las exhibiciones');
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Success response:', data); // Debug log
                // Mostrar modal de éxito
                showGeneralMessageModal('Cancelación Exitosa', data.message || 'Las exhibiciones han sido canceladas correctamente.', 'success');
                
                // Cerrar el modal de obras exhibidas y refrescar el calendario
                closeExhibitedArtworksModal();
                if (calendar) {
                    calendar.refetchEvents();
                }
            })
            .catch(error => {
                console.error('Error cancelling all exhibitions:', error);
                // Mostrar modal de error
                showGeneralMessageModal('Error de Cancelación', error.message || 'Ha ocurrido un error al cancelar las exhibiciones.', 'error');
            });
        }
    </script>
@endpush 