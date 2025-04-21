<!--Cambiar más adelante-->
@php
    $ciudades = [
        'Madrid', 'Barcelona', 'Valencia', 'Sevilla', 'Zaragoza',
        'Málaga', 'Murcia', 'Palma de Mallorca', 'Las Palmas de Gran Canaria', 'Lleida',
        'Bilbao', 'Alicante', 'Córdoba', 'Valladolid', 'Cáceres',
        'Salamanca', 'Girona', 'Toledo', 'Badajoz', 'Oviedo'
    ];
@endphp

<div class="modal hidden" id="modalRegistro">
    <div class="form-container">
        <button class="close-button" id="cerrarRegistro">&times;</button>
        <div class="logo-container">
            <img src="{{ asset('images/logoCosmosCinema.png') }}" alt="Cosmos Cinema Logo" class="logo">
        </div>
        <form method="POST" action="{{ route('registro')}}">
            @csrf

            <div id="step1" class="form-step active">
                <!-- Fila para el email -->
                <div class="form-row">
                    @if ($errors->has('email'))
                    <div class="error-message">{{ $errors->first('email') }}</div>
                    @endif
                    <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required class="input">
                </div>

                <!-- Fila para confirmar el email -->
                <div class="form-row">
                    @if ($errors->has('email_confirmation'))
                    <div class="error-message">{{ $errors->first('email_confirmation') }}</div>
                    @endif
                    <input type="email" name="email_confirmation" placeholder="Confirmar tu email" value="{{ old('email_confirmation') }}" required class="input">
                </div>

                <!-- Fila para contraseña y confirmación -->
                <div class="form-row">
                    @if ($errors->has('password'))
                    <div class="error-message">{{ $errors->first('password') }}</div>
                    @endif
                    <input type="password" name="password" placeholder="Contraseña" required class="input">
                    @if ($errors->has('password_confirmation'))
                    <div class="error-message">{{ $errors->first('password_confirmation') }}</div>
                    @endif
                    <input type="password" name="password_confirmation" placeholder="Confirmar contraseña" required class="input">
                </div>

                <!-- Botones -->
                <div class="button-group">
                    <a href="{{ route('principal') }}" class="btn back-button">Volver</a>
                    <button type="button" class="btn next-step">Siguiente</button>
                </div>
            </div>

            <div id="step2" class="form-step">
                <div class="form-row" style="display: flex; justify-content: space-between;">
                    <div class="half-width">
                        @if ($errors->has('nombre'))
                        <div class="error-message">{{ $errors->first('nombre') }}</div>
                        @endif
                        <input type="text" name="nombre" placeholder="Nombre" value="{{ old('nombre') }}" required class="input">
                    </div>
                    <div class="half-width">
                        @if ($errors->has('apellidos'))
                        <div class="error-message">{{ $errors->first('apellidos') }}</div>
                        @endif
                        <input type="text" name="apellidos" placeholder="Apellidos" value="{{ old('apellidos') }}" required class="input">
                    </div>
                </div>
                <div class="form-row">
                    @if ($errors->has('direccion'))
                    <div class="error-message">{{ $errors->first('direccion') }}</div>
                    @endif
                    <input type="text" name="direccion" placeholder="Dirección" value="{{ old('direccion') }}" required class="input">
                </div>
                <div class="form-row" style="display: flex; justify-content: space-between;">
                    <div class="half-width">
                        <select name="ciudad" id="ciudad" class="input" required>
                            <option value="" disabled selected>Selecciona tu ciudad</option>
                            @foreach($ciudades as $ciudad)
                            <option value="{{ $ciudad }}">{{ $ciudad}}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('ciudad'))
                        <div class="error-message">{{ $errors->first('ciudad') }}</div>
                        @endif
                    </div>
                    <div class="half-width">
                        @if ($errors->has('codigo_postal'))
                        <div class="error-message">{{ $errors->first('codigo_postal') }}</div>
                        @endif
                        <input type="text" name="codigo_postal" placeholder="Código Postal" value="{{ old('codigo_postal') }}" required class="input">
                    </div>
                </div>
                <div class="form-row">
                    @if ($errors->has('telefono'))
                    <div class="error-message">{{ $errors->first('telefono') }}</div>
                    @endif
                    <input type="text" name="telefono" placeholder="Teléfono" value="{{ old('telefono') }}" required class="input" pattern="^\d{9}$" title="El teléfono debe tener 9 dígitos." maxlength="9" minlength="9">
                </div>

                <div class="form-row">
                    @if ($errors->has('dni'))
                    <div class="error-message">{{ $errors->first('dni') }}</div>
                    @endif
                    <input type="text" name="dni" placeholder="DNI" value="{{ old('dni') }}" required class="input" pattern="^\d{8}[A-Za-z]$" title="El DNI debe tener 8 dígitos seguidos de una letra." maxlength="9" minlength="9">
                </div>

                <div class="button-group">
                    <button type="button" class="btn prev-step">Anterior</button>
                    <button type="button" class="btn next-step">Siguiente</button>
                </div>
            </div>

            <div id="step3" class="form-step">
                <!-- Falta el captcha-->
                <div class="form-row">
                    @if ($errors->has('fecha_nacimiento'))
                    <div class="error-message">{{ $errors->first('fecha_nacimiento') }}</div>
                    @endif
                    <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" placeholder="Fecha de Nacimiento" required class="input">
                </div>

                <div class="form-row">
                    <label><input type="checkbox"> Acepto recibir publicidad y comunicaciones sobre promociones y novedades relacionadas con mis preferencias y gustos cinematográficos.</label>
                </div>
                <div class="form-row">
                    @if ($errors->has('mayor_edad'))
                    <div class="error-message">{{ $errors->first('mayor_edad') }}</div>
                    @endif
                    <label><input type="checkbox" name="mayor_edad" value="on" required> Soy mayor de 14 años</label>
                </div>
                <div class="button-group">
                    <button type="button" class="btn prev-step">Anterior</button>
                    <button type="submit" class="btn">Registrarse</button>
                </div>


            </div>
        </form>
    </div>
</div>