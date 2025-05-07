<script>
    var peliculas = @JSON($peliculas)
</script>

@php
    var_dump($peliculas)
@endphp

<div class="overlay">
    <div class="overlay-inner">
        <div class="overlay-count-row">
            <div class="count-column">
                <h2 data-slide-count="step" class="count-heading"></h2>
            </div>
        </div>
        <div class="overlay-nav-row"><button aria-label="previous slide" data-slider="button-prev" class="button"><svg xmlns="http://www.w3.org/2000/svg" width="100%" viewbox="0 0 17 12" fill="none" class="button-arrow">
                    <path d="M6.28871 12L7.53907 10.9111L3.48697 6.77778H16.5V5.22222H3.48697L7.53907 1.08889L6.28871 0L0.5 6L6.28871 12Z" fill="currentColor"></path>
                </svg>
                <div class="button-overlay">
                    <div class="overlay-corner"></div>
                    <div class="overlay-corner top-right"></div>
                    <div class="overlay-corner bottom-left"></div>
                    <div class="overlay-corner bottom-right"></div>
                </div>
            </button><button aria-label="previous slide" data-slider="button-next" class="button"><svg xmlns="http://www.w3.org/2000/svg" width="100%" viewbox="0 0 17 12" fill="none" class="button-arrow next">
                    <path d="M6.28871 12L7.53907 10.9111L3.48697 6.77778H16.5V5.22222H3.48697L7.53907 1.08889L6.28871 0L0.5 6L6.28871 12Z" fill="currentColor"></path>
                </svg>
                <div class="button-overlay">
                    <div class="overlay-corner"></div>
                    <div class="overlay-corner top-right"></div>
                    <div class="overlay-corner bottom-left"></div>
                    <div class="overlay-corner bottom-right"></div>
                </div>
            </button></div>
    </div>
</div>

<div class="slider-wrap">
    <div data-slider="list" class="slider-list" id='list'>
        @foreach ($peliculas as $pelicula)
            <div data-slider="slide" class="slider-slide">
                <div class="slide-inner">
                @if (isset($pelicula['poster_url']))
                    <img class='movie_poster' src="{{ $pelicula['backdrop_url'] }}" loading="lazy" alt="{{ $pelicula['titulo'] }}">
                @else
                    <p>Póster no disponible</p>
                @endif
                </div>
            </div>
        @endforeach
    </div>
</div>