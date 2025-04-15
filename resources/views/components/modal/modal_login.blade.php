<div class="modal" id="modalLogin">
    <div class="form-container">
        <button class="close-button" id="cerrarLogin">&times;</button>
        <div class="logo-container">
            <img src="{{ asset('images/logoCosmosCinema.png') }}" alt="Cosmos Cinema Logo" class="logo">
        </div>
        <!-- Action será la URL activa -->
        <form method="POST" action='{{ route('login') }}'>
            @csrf

            <div class="form-row">
                <input class="input" type="email" name="login_email" id='login_email' 
                    placeholder="Email" value="{{ old('email') }}" required>
                @error('email')
                    <span>{{ $message }}</span>
                @enderror
            </div>

            <div class="form-row">
                <input class="input" type="password" name="login_password" id='login_password' 
                    placeholder="**********" required>
                @error('login_password')
                    <span>{{ $message }}</span>
                @enderror
            </div>

            <div class="button-group">
                <a href="{{ route('principal') }}" class="btn back-button">Volver</a>
                <button type="submit" class="btn">Login</button>
            </div>
        </form>
    </div>
</div>