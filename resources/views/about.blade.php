@extends('layouts.app')

@section('title', 'Nosotros - Art Indie Space')

@section('content')
    <div class="min-h-screen bg-white">
        <div class="container mx-auto px-4 py-16">
            <div class="max-w-4xl mx-auto">
                <!-- Header minimalista -->
                <div class="text-center mb-16">
                    <h1 class="text-5xl font-bold text-gray-900 mb-6">
                        Sobre Nosotros
                    </h1>
                    <div class="w-16 h-px bg-gray-300 mx-auto mb-8"></div>
                    <p class="text-xl text-gray-600 leading-relaxed max-w-3xl mx-auto">
                        En Art Indie Space, creemos en el poder del arte independiente y en la importancia de dar voz a artistas emergentes. 
                        Nuestra plataforma nace con la misión de crear un espacio inclusivo donde el arte pueda florecer sin barreras.
                    </p>
                </div>

                <!-- Valores minimalistas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
                    <div class="bg-gray-50 p-8 rounded-lg hover:bg-gray-100 transition-colors duration-300 border border-gray-200">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900">Diversidad</h3>
                        </div>
                        <p class="text-gray-600 leading-relaxed">
                            Celebramos y promovemos la diversidad en todas sus formas, tanto en los estilos artísticos como en los artistas que representamos.
                        </p>
                    </div>

                    <div class="bg-gray-50 p-8 rounded-lg hover:bg-gray-100 transition-colors duration-300 border border-gray-200">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900">Innovación</h3>
                        </div>
                        <p class="text-gray-600 leading-relaxed">
                            Fomentamos la experimentación y la innovación en el arte, apoyando a aquellos que buscan nuevas formas de expresión.
                        </p>
                    </div>

                    <div class="bg-gray-50 p-8 rounded-lg hover:bg-gray-100 transition-colors duration-300 border border-gray-200">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900">Comunidad</h3>
                        </div>
                        <p class="text-gray-600 leading-relaxed">
                            Construimos una comunidad inclusiva donde los artistas pueden crecer y desarrollarse en un ambiente de apoyo mutuo.
                        </p>
                    </div>

                    <div class="bg-gray-50 p-8 rounded-lg hover:bg-gray-100 transition-colors duration-300 border border-gray-200">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900">Accesibilidad</h3>
                        </div>
                        <p class="text-gray-600 leading-relaxed">
                            Trabajamos para hacer el arte accesible para todos, eliminando barreras y creando oportunidades de conexión.
                        </p>
                    </div>
                </div>
                
                <!-- CTA minimalista -->
                <div class="text-center">
                    @guest
                        <div class="bg-gray-50 p-8 rounded-lg border border-gray-200">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">¿Listo para unirte?</h3>
                            <p class="text-gray-600 mb-6">
                                Forma parte de nuestra comunidad de artistas independientes.
                            </p>
                            <a href="{{ route('register') }}" class="inline-block bg-gray-900 text-white px-8 py-3 rounded-lg hover:bg-gray-800 transition-colors duration-300 font-medium">
                                Únete a nuestra comunidad
                            </a>
                        </div>
                    @else
                        <div class="bg-gray-50 p-8 rounded-lg border border-gray-200">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">¡Bienvenido a la comunidad!</h3>
                            <p class="text-gray-600 mb-6">
                                Gracias por ser parte de Art Indie Space.
                            </p>
                            <a href="{{ route('gallery') }}" class="inline-block bg-gray-900 text-white px-8 py-3 rounded-lg hover:bg-gray-800 transition-colors duration-300 font-medium">
                                Explorar Galería
                            </a>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </div>
@endsection 