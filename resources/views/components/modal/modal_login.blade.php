<div class="modal" id="modalLogin">
    <div class="form-container">
        <button class="close-button" id="cerrarLogin">&times;</button>
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

                <!-- Botones -->
                <div class="button-group">
                    <a href="{{ route('principal') }}" class="btn back-button">Volver</a>
                    <button type="submit" class="btn">Login</button>
                </div>
            </div>
        </form>
    </div>
</div>