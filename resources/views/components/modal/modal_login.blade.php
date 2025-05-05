<div class="modal {{ $errors->has('login_email') || $errors->has('login_password') ? 'flex' : 'hidden' }}" id="modalLogin">
    <div class="form-container">
        <button class="close-button" id="cerrarLogin">&times;</button>
        <div class="logo-container">
            <img src="{{ asset('images/logoCosmosCinema.png') }}" alt="Cosmos Cinema Logo" class="logo">
        </div>
        <!-- Action será la URL activa -->
        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            <div class="form-row">
                <input class="input" type="email" name="login_email" id='login_email'
                    placeholder="Email" value="{{ old('login_email')  }}" required>
                    <p class="client-side-field-error error-text"></p>
            </div>

            @error('login_email')
                <div class="form-row"> {{-- Nota: Esto crea un nuevo form-row solo para el error --}}
                    <p class="error-text">{{ $message }}</p>
                </div>
            @enderror
            <div class="form-row">
                <input class="input" type="password" name="login_password" id='login_password'
                    placeholder="**********" required>
                    <p class="client-side-field-error error-text"></p>
            </div>

            @error('login_password')
                <div class="form-row"> {{-- Nota: Esto crea un nuevo form-row solo para el error --}}
                    <p class="error-text">{{ $message }}</p>
                </div>
            @enderror

            <div class="form-row">
                <label>
                    <input type="checkbox" name="remember"> Recordarme
                </label>
            </div>

            <div class="button-group">
                <button type="button" class="btn back-button" id="volverLoginModal">Volver</button>
                <button type="submit" class="btn">Login</button>
            </div>


        </form>
    </div>
</div>