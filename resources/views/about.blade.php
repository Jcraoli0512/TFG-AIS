@extends('layouts.app')

@section('title', 'Nosotros - Art Indie Space')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-4xl font-bold text-center mb-8">Sobre Nosotros</h1>
            
            <div class="text-center mb-12">
                <p class="text-lg text-gray-600 leading-relaxed">
                    En Art Indie Space, creemos en el poder del arte independiente y en la importancia de dar voz a artistas emergentes. 
                    Nuestra plataforma nace con la misión de crear un espacio inclusivo donde el arte pueda florecer sin barreras.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-xl font-semibold mb-3">Diversidad</h3>
                    <p class="text-gray-600">Celebramos y promovemos la diversidad en todas sus formas, tanto en los estilos artísticos como en los artistas que representamos.</p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-xl font-semibold mb-3">Innovación</h3>
                    <p class="text-gray-600">Fomentamos la experimentación y la innovación en el arte, apoyando a aquellos que buscan nuevas formas de expresión.</p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-xl font-semibold mb-3">Comunidad</h3>
                    <p class="text-gray-600">Construimos una comunidad inclusiva donde los artistas pueden crecer y desarrollarse en un ambiente de apoyo mutuo.</p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-xl font-semibold mb-3">Accesibilidad</h3>
                    <p class="text-gray-600">Trabajamos para hacer el arte accesible para todos, eliminando barreras y creando oportunidades de conexión.</p>
                </div>
            </div>
            
            <div class="text-center mt-12">
                @guest
                    <a href="{{ route('register') }}" class="inline-block bg-gray-800 text-white px-8 py-3 rounded-lg hover:bg-gray-700 transition">
                        Únete a nuestra comunidad
                    </a>
                @endguest
            </div>
        </div>
    </div>
@endsection 