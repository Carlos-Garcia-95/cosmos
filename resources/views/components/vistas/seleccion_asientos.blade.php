<div>
            <div id='cerrar-button' class='header-buttons cerrar-button'>
                <button id="cerrarCompra">VOLVER</button>
            </div>

            <div class="movie-container">
                <label>Pick a Movie</label>
                <!-- Ya haremos el select con las querys necesarias para sacar info de base de datos -->
                <select id="movie">
                    <option value='8'>True Romance - $8</option>
                    <option value='8'>American History X - $8</option>
                    <option value='8'>A Beautiful Mind - $8</option>
                    <option value='10'>Joker - $10</option>
                </select>
            </div>

            <!-- Podemos meter los tres dentro de una tabla y juntarla a asiento-->
            <ul class="showcase">
                <li>
                    <div class="seat"></div>
                    <small>Available</small>
                </li>
                <li>
                    <div class="seat selected"></div>
                    <small>Selected</small>
                </li>
                <li>
                    <div class="seat occupied"></div>
                    <small>Occupied</small>
                </li>
            </ul>

            <!-- Contenedor para los asientos -->
            <div class="container">
                <div class="screen"></div>
                @for ($i = 0; $i < 8; $i++) <div class="row">
                    @for ($j = 0; $j < 8; $j++) <div class="seat">
            </div>
            @endfor
        </div>
        @endfor
        </div>

        <!-- Información de los asientos seleccionados y precio -->
        <div class="text-wrapper">
            <p class="text">
                You have selected <span id="count">0</span> seats for a price of $<span id="total">0</span>
            </p>
            <button id="confirmarCompra">Comprar Entradas</button>
        </div>
        </div>