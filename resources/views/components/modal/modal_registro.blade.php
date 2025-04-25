
<div class="modal hidden" id="modalRegistro">
    <div class="form-container">
        <button class="close-button" id="cerrarRegistro">&times;</button>
        <div class="logo-container">
            <img src="{{ asset('images/logoCosmosCinema.png') }}" alt="Cosmos Cinema Logo" class="logo">
        </div>

        <form method="POST" action="{{ route('registro')}}">
            @csrf

            <div id="step1" class="form-step active">
    <div class="form-row">
        @if ($errors->has('email'))
        <div class="error-message">{{ $errors->first('email') }}</div>
        @endif
        <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required class="input">
        <span class="client-side-field-error" style="color: red; display: none; margin-bottom: 2%; "></span>
    </div>

    <div class="form-row">
        @if ($errors->has('email_confirmation'))
        <div class="error-message">{{ $errors->first('email_confirmation') }}</div>
        @endif
        <input type="email" name="email_confirmation" placeholder="Confirmar tu email" value="{{ old('email_confirmation') }}" required class="input">
        <span class="client-side-field-error" style="color: red; display: none; margin-bottom: 2%; "></span>
    </div>


    <div class="form-row">
        @if ($errors->has('password'))
        <div class="error-message">{{ $errors->first('password') }}</div>
        @endif
        <div class="password-input-container">
            <input type="password" name="password" placeholder="Contraseña" required class="input">
            <span class="toggle-password">👁️</span>
        </div>
        <span class="client-side-field-error" style="color: red; display: none; margin-bottom: 2%; "></span>
    </div>

    <div class="form-row">
        @if ($errors->has('password_confirmation'))
        <div class="error-message">{{ $errors->first('password_confirmation') }}</div>
        @endif
        <div class="password-input-container">
            <input type="password" name="password_confirmation" placeholder="Confirmar contraseña" required class="input">
            <span class="toggle-password">👁️</span>
        </div>
        <span class="client-side-field-error" style="color: red; display: none;"></span>
    </div>

    <div class="button-group">
        <a href="{{ route('principal') }}" class="btn back-button">Volver</a>
        <button type="button" class="btn next-step">Siguiente</button>
    </div>
</div>

            <div id="step2" class="form-step">
                <div class="form-row" style="display: flex; justify-content: space-between;">
                    <div class="half-width">
                        @error('email', 'registro')
                        <div class="error-message">{{ $message }}</div>
                        @enderror

                        <input type="text" name="nombre" placeholder="Nombre" value="{{ old('nombre') }}" required class="input">
                    </div>
                    <div class="half-width">
                        @error('apellidos', 'registro')
                        <div class="error-message">{{ $message }}</div>
                        @enderror

                        <input type="text" name="apellidos" placeholder="Apellidos" value="{{ old('apellidos') }}" required class="input">
                    </div>
                </div>
                <div class="form-row">
                    @error('direccion', 'registro')
                    <div class="error-message">{{ $message }}</div>
                    @enderror

                    <input type="text" name="direccion" placeholder="Dirección" value="{{ old('direccion') }}" required class="input">
                </div>
                <div class="form-row" style="display: flex; justify-content: space-between;">
                    <div class="half-width">
                        <select name="ciudad" id="ciudad" class="input ciudad-scroll" required>
                            <option value="" disabled selected>Selecciona tu ciudad</option>
                            @foreach($ciudades as $ciudad)
                            <option value="{{ $ciudad->nombre }}">
                                {{ $ciudad->nombre }}
                            </option>
                            @endforeach
                        </select>
                        @error('ciudad', 'registro')
                        <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="half-width">
                        @error('codigo_postal', 'registro')
                        <div class="error-message">{{ $message }}</div>
                        @enderror

                        <input type="text" name="codigo_postal" placeholder="Código Postal" value="{{ old('codigo_postal') }}" required class="input">
                    </div>
                </div>
                <div class="form-row">
                    @error('telefono', 'registro')
                    <div class="error-message">{{ $message }}</div>
                    @enderror

                    <input type="text" name="telefono" placeholder="Teléfono" value="{{ old('telefono') }}" required class="input" pattern="^\d{9}$" title="El teléfono debe tener 9 dígitos." maxlength="9" minlength="9">
                    <span class="client-side-field-error" style="color: red; display: none; margin-bottom: 2%;"></span>
                </div>

                <div class="form-row">
                    @error('dni', 'registro')
                    <div class="error-message">{{ $message }}</div>
                    @enderror

                    <input type="text" name="dni" placeholder="DNI" value="{{ old('dni') }}" required class="input" pattern="^\d{8}[A-Za-z]$" title="El DNI debe tener 8 dígitos seguidos de una letra." maxlength="9" minlength="9">
                    <span class="client-side-field-error" style="color: red; display: none;"></span>
                </div>

                <div class="button-group">
                    <button type="button" class="btn prev-step">Anterior</button>
                    <button type="button" class="btn next-step">Siguiente</button>
                </div>
            </div>

            <div id="step3" class="form-step">
                <!-- Falta el captcha-->
                <div class="form-row">
                    @error('fecha_nacimiento', 'registro')
                    <div class="error-message">{{ $message }}</div>
                    @enderror

                    <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" placeholder="Fecha de Nacimiento" required class="input">
                    <span class="client-side-field-error" style="color: red; display: none; margin-bottom: 2%;"></span>
                </div>

                <div class="form-row">
                    <label><input type="checkbox"> Acepto recibir publicidad y comunicaciones sobre promociones y novedades relacionadas con mis preferencias y gustos cinematográficos.</label>
                </div>
                <div class="form-row">
                    @error('mayor_edad', 'registro')
                    <div class="error-message">{{ $message }}</div>
                    @enderror

                    <label><input type="checkbox" name="mayor_edad" value="on" required> Soy mayor de 14 años</label>
                    <span class="client-side-field-error" style="color: red; display: none;"></span>
                </div>
                <div class="button-group">
                    <button type="button" class="btn prev-step">Anterior</button>
                    <button type="submit" class="btn">Registrarse</button>
                </div>

                <!-- Mostrar errores generales si existen -->
                @if ($errors->registro->any())
                <div class="error-messages">
                    <ul>
                        @foreach ($errors->registro->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif



            </div>
        </form>
    </div>
</div>