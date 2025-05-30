@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <h1 class="text-4xl font-bold text-center mb-8">Bienvenidos a Art Indie Space</h1>

        <!-- Swiper Carrusel de presentación -->
        <div class="max-w-2xl mx-auto mb-12">
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <img src="https://images.unsplash.com/photo-1464983953574-0892a716854b" alt="Arte 1"
                            class="w-full h-64 object-cover rounded-lg">
                    </div>
                    <div class="swiper-slide">
                        <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb" alt="Arte 2"
                            class="w-full h-64 object-cover rounded-lg">
                    </div>
                    <div class="swiper-slide">
                        <img src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97" alt="Arte 3"
                            class="w-full h-64 object-cover rounded-lg">
                    </div>
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>

        <!-- Texto descriptivo -->
        <div class="max-w-3xl mx-auto text-center">
            <p class="text-lg text-gray-600 leading-relaxed">
                Un espacio dedicado a aquellos artistas que quieren compartir, exponer, mostrar, etc... sus obras,
                tanto si es realismo como cubismo, aceptamos y mostramos todos, así que coged vuestras obras y
                mostradla al mundo.
            </p>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var swiper = new Swiper(".mySwiper", {
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            loop: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
        });
    });
</script>
@endpush 