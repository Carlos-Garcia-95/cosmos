<script>
    var peliculas = @JSON($peliculas)
</script>

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
                    <img class='movie_poster' src="{{ $pelicula['backdrop_url'] }}" loading="lazy" alt="{{ $pelicula['title'] }}">
                @else
                    <p>Póster no disponible</p>
                @endif
                    <div class="slide-caption">
                        <div class="caption-dot"></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- POR SI ACASO, ESTO ES LO QUE HABIA ANTES POR SI SE ROMPE --}}

{{-- @foreach ($peliculas as $pelicula)
    <div class="col-md-4 mb-4">
        <div class="card">
            @if (isset($pelicula['poster_url']))
                <img src="{{ $pelicula['poster_url'] }}" class="card-img-top" alt="{{ $pelicula['title'] }}" width='500'>
            @else
                <p>No poster available</p>
            @endif
            <div class="card-body">
                <h5 class="card-title">{{ $pelicula['title'] }}</h5>
                <p class="card-text">Fecha de lanzamiento: {{ $pelicula['release_date'] }}</p>
                <p class="card-text">{{ Str::limit($pelicula['overview'], 100) }}</p>
            </div>
        </div>
    </div>
@endforeach --}}

{{-- <div class="overlay">
    <div class="overlay-inner">
        <div class="overlay-count-row">
            <div class="count-column">
                <h2 data-slide-count="step" class="count-heading">01</h2>
            </div>
            <div class="count-row-divider"></div>
            <div class="count-column">
                <h2 data-slide-count="total" class="count-heading">04</h2>
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
</div> --}}


{{-- <div class="slider-wrap">
    <div data-slider="list" class="slider-list">
        <div data-slider="slide" class="slider-slide">
            <div class="slide-inner"><img src="https://cdn.prod.website-files.com/674d847bf8e817966d307714/674d90f74ff2fe8b0b912b97_slide-1.avif" loading="lazy" sizes="(max-width: 479px) 100vw, 560px" alt="Abstract layout By FAKURIANDESIGN through Unsplash">
                <div class="slide-caption">
                    <div class="caption-dot"></div>
                    <p class="caption">Layout nº001</p>
                </div>
            </div>
        </div>
        <div data-slider="slide" class="slider-slide active">
            <div class="slide-inner"><img src="https://cdn.prod.website-files.com/674d847bf8e817966d307714/674d90f7cf52dd961b48a1e2_slide-2.avif" loading="lazy" alt="Abstract layout By FAKURIANDESIGN through Unsplash">
                <div class="slide-caption">
                    <div class="caption-dot"></div>
                    <p class="caption">Layout nº002</p>
                </div>
            </div>
        </div>
        <div data-slider="slide" class="slider-slide">
            <div class="slide-inner"><img src="https://cdn.prod.website-files.com/674d847bf8e817966d307714/674d90f7f7cce73267703347_slide-3.avif" loading="lazy" sizes="(max-width: 479px) 100vw, 560px" alt="Abstract layout By FAKURIANDESIGN through Unsplash">
                <div class="slide-caption">
                    <div class="caption-dot"></div>
                    <p class="caption">Layout nº003</p>
                </div>
            </div>
        </div>
        <div data-slider="slide" class="slider-slide">
            <div class="slide-inner"><img src="https://cdn.prod.website-files.com/674d847bf8e817966d307714/674d90f7ccfd203c82a46798_slide-4.avif" loading="lazy" sizes="(max-width: 479px) 100vw, 560px" alt="Abstract layout By FAKURIANDESIGN through Unsplash">
                <div class="slide-caption">
                    <div class="caption-dot"></div>
                    <p class="caption">Layout nº004</p>
                </div>
            </div>
        </div>
    </div>
</div> --}}