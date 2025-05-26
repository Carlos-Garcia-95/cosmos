@props(['peliculas'])

@if($peliculas && $peliculas->count() > 0)
<div class="poster-carousel-container-component"> {{-- Clase específica --}}
    <h2 class="poster-carousel-title">Películas en Cartelera</h2>
    <div class="swiper poster-movie-carousel"> {{-- Clase diferente para este Swiper --}}
        <div class="swiper-wrapper">
            @foreach($peliculas as $index => $pelicula)
                @php
                    // Obtener datos para este slide del array JS preparado, usando el índice
                    // Esto es para el renderizado inicial. El JS también puede actualizarlo.
                    $peliculaJsData = $GLOBALS['peliculasParaJsGlobalParaEsteSlider'][$index] ?? null; // Necesitaremos una forma de pasar esto o referenciarlo

                    $titulo = $peliculaJsData['titulo'] ?? (is_object($pelicula) ? $pelicula->titulo : ($pelicula['titulo'] ?? 'Sin Título'));
                    $posterUrl = $peliculaJsData['poster_url'] ?? asset('images/placeholder-poster.jpg');
                @endphp

                <div class="swiper-slide poster-slide" data-slide-index="{{ $index }}">
                        <img src="{{ $posterUrl }}" alt="Póster de {{ $titulo }}" class="poster-slide-image">
                        <div class="poster-slide-title-overlay">
                            <h3 class="poster-slide-title">{{ $titulo }}</h3>
                        </div>
                </div>
            @endforeach
        </div>
        <div class="swiper-pagination poster-carousel-pagination"></div>
        <div class="swiper-button-prev poster-carousel-button-prev"></div>
        <div class="swiper-button-next poster-carousel-button-next"></div>
    </div>
</div>
@endif

{{-- Necesitamos pasar $peliculasParaJs a una variable global diferente o usar la ya existente con cuidado --}}
{{-- Lo ideal sería que el JS que inicializa este carrusel también use window.sliderPeliculasDisponibles y filtre/mapee para su propio uso --}}