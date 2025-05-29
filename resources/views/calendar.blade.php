@extends('layouts.app')

@section('title', 'Calendario - Art Indie Space')

@section('styles')
    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
@endsection

@section('content')
    <div class="container mx-auto px-4 py-12">
        <h1 class="text-4xl font-bold text-center mb-8">Calendario de Exposiciones</h1>
        
        <!-- Calendario -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <div id="calendar"></div>
        </div>

        <!-- Galería de imágenes del día seleccionado -->
        <div id="selected-date-gallery" class="hidden">
            <h2 class="text-2xl font-bold mb-4">Imágenes del día seleccionado</h2>
            <div id="gallery-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 p-4">
                <!-- Las imágenes se cargarán dinámicamente aquí -->
            </div>
        </div>

        <!-- Modal para mostrar detalles del evento -->
        <div id="eventModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4">
                <div class="flex justify-between items-start mb-4">
                    <h3 id="eventTitle" class="text-xl font-bold"></h3>
                    <button onclick="closeEventModal()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="eventDescription" class="text-gray-600 mb-4"></div>
                <div id="eventDates" class="text-sm text-gray-500"></div>
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

        document.addEventListener('DOMContentLoaded', function() {
            console.log('Calendar DOM fully loaded'); // Debug: Check if DOMContentLoaded fires

            const calendarEl = document.getElementById('calendar');
            console.log('Calendar element:', calendarEl); // Debug: Check if calendar element is found

            if (!calendarEl) {
                console.error('Error: Calendar element not found!');
                return; // Stop execution if element is not found
            }

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: '/api/calendar-events',
                eventClick: function(info) {
                    showEventModal(info.event);
                },
                dateClick: function(info) {
                    // Mostrar la galería del día seleccionado
                    document.getElementById('selected-date-gallery').classList.remove('hidden');
                    
                    // Cargar las imágenes del día seleccionado
                    fetch(`/api/gallery-images/${info.dateStr}`)
                        .then(response => response.json())
                        .then(data => {
                            const galleryGrid = document.getElementById('gallery-grid');
                            galleryGrid.innerHTML = '';
                            
                            if (data.length === 0) {
                                galleryGrid.innerHTML = '<p class="col-span-full text-center text-gray-500">No hay imágenes disponibles para este día.</p>';
                                return;
                            }
                            
                            data.forEach(image => {
                                const galleryItem = document.createElement('div');
                                galleryItem.className = 'relative overflow-hidden rounded-lg shadow-md group';
                                galleryItem.innerHTML = `
                                    <img src="${image.url}" alt="${image.title}" class="w-full h-64 object-cover transition-transform duration-300 group-hover:scale-105">
                                    <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-70 text-white p-4 transform translate-y-full transition-transform duration-300 group-hover:translate-y-0">
                                        <h3 class="font-bold">${image.title}</h3>
                                        <p class="text-sm">${image.artist}</p>
                                        <p class="text-sm mt-2">${image.description || ''}</p>
                                    </div>
                                `;
                                galleryGrid.appendChild(galleryItem);
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            const galleryGrid = document.getElementById('gallery-grid');
                            galleryGrid.innerHTML = '<p class="col-span-full text-center text-red-500">Error al cargar las imágenes.</p>';
                        });
                }
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
    </script>
@endpush 