<div class="modal hidden" id="modalRegistro">
    <div class="form-container">
        <button class="close-button" id="cerrarRegistro">&times;</button>
        <div class="logo-container">
            <img src="{{ asset('images/logoCosmosCinema.png') }}" alt="Cosmos Cinema Logo" class="logo">
        </div>
        <form method="POST">
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

                <!-- Fila para nombre y apellidos -->
                <div class="form-row">
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

                <!-- Botones -->
                <div class="button-group">
                    <a href="{{ route('principal') }}" class="btn back-button">Volver</a>
                    <button type="button" class="btn next-step">Siguiente</button>
                </div>
            </div>

            <div id="step2" class="form-step">
                @if ($errors->has('direccion'))
                <div class="error-message">{{ $errors->first('direccion') }}</div>
                @endif
                <input type="text" name="direccion" placeholder="Dirección" value="{{ old('direccion') }}" required class="input">
                @if ($errors->has('ciudad'))
                <div class="error-message">{{ $errors->first('ciudad') }}</div>
                @endif
                <input type="text" name="ciudad" placeholder="Ciudad" value="{{ old('ciudad') }}" required class="input">
                @if ($errors->has('codigo_postal'))
                <div class="error-message">{{ $errors->first('codigo_postal') }}</div>
                @endif
                <input type="text" name="codigo_postal" placeholder="Código Postal" value="{{ old('codigo_postal') }}" required class="input">
                @if ($errors->has('telefono'))
                <div class="error-message">{{ $errors->first('telefono') }}</div>
                @endif
                <input type="tel" name="telefono" placeholder="Teléfono" value="{{ old('telefono') }}" required class="input">
                @if ($errors->has('password'))
                <div class="error-message">{{ $errors->first('password') }}</div>
                @endif
                <input type="password" name="password" placeholder="Contraseña" required class="input">
                <div class="button-group">
                    <button type="button" class="btn prev-step">Anterior</button>
                    <button type="button" class="btn next-step">Siguiente</button>
                </div>
            </div>

            <div id="step3" class="form-step">
                <label><input type="checkbox"> Acepto recibir publicidad y comunicaciones sobre promociones y novedades relacionadas con mis preferencias y gustos cinematográficos.</label>
                @if ($errors->has('mayor_edad'))
                <div class="error-message">{{ $errors->first('mayor_edad') }}</div>
                @endif
                <label><input type="checkbox" name="mayor_edad" {{ old('mayor_edad') ? 'checked' : '' }} required> Soy mayor de 14 años</label>
                <div class="button-group">
                    <button type="button" class="btn prev-step">Anterior</button>
                    <button type="submit" class="btn">Registrarse</button>
                </div>
            </div>
        </form>
    </div>
</div>

