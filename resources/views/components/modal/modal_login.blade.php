<div class="modal {{ $errors->has('login_email') || $errors->has('login_password') ? 'flex' : 'hidden' }}" id="modalLogin">
    <div class="form-container">
        <button class="close-button" id="cerrarLogin">&times;</button>
        <div class="logo-container">
            <img src="{{ asset('images/logoCosmosCinema.png') }}" alt="Cosmos Cinema Logo" class="logo">
        </div>
        <!-- Action será la URL activa -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-row">
                <input class="input" type="email" name="login_email" id='login_email' 
                    placeholder="Email" value="{{ old('login_email')  }}" required>
            </div>

            <div class="form-row">
                <input class="input" type="password" name="login_password" id='login_password' 
                    placeholder="**********" required>
            </div>

            @error('login_email')  
                <div class="form-row">
                    <p>{{ $message }}</p>
                </div>
            @enderror
            @error('login_password')
                <div class="form-row">
                    <p>{{ $message }}</p>
                </div>
            @enderror

            <div class="button-group">
                <a href="{{ route('principal') }}" class="btn back-button">Volver</a>
                <button type="submit" class="btn">Login</button>
            </div>

            
        </form>
    </div>
</div>