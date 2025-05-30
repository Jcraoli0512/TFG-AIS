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
                        <div>
                            <label for="artwork" class="block text-sm font-medium text-gray-700">Selecciona tu obra</label>
                            <select id="artwork" name="artwork_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Selecciona una obra...</option>
                                @foreach(auth()->user()->artworks as $artwork)
                                    <option value="{{ $artwork->id }}">{{ $artwork->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="display_date" class="block text-sm font-medium text-gray-700">Fecha de exhibición</label>
                            <input type="date" id="display_date" name="display_date" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                        </div>
                        <button type="submit" 
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Solicitar fecha
                        </button>
                    </form>
                </div>
            </div>
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
                    if (info.event.extendedProps.type === 'artwork') {
                        loadGalleryImages(info.event.start);
                    }
                },
                dateClick: function(info) {
                    loadGalleryImages(info.date);
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

        // Manejar el formulario de selección de fecha
        const artworkDateForm = document.getElementById('artworkDateForm');
        artworkDateForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            fetch('/api/artwork-display-dates', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    artwork_id: formData.get('artwork_id'),
                    display_date: formData.get('display_date')
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else {
                    alert(data.message);
                    calendar.refetchEvents();
                    this.reset();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ha ocurrido un error al procesar tu solicitud');
            });
        });

        // Cargar imágenes del día actual al inicio
        loadGalleryImages(new Date());
    </script>
@endpush 