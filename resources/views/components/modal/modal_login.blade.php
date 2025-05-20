<div class="modal hidden" id="modalLogin">
    <div class="form-container">
        <button class="close-button" id="cerrarLogin">&times;</button>
        <div class="logo-container">
            <img src="{{ asset('images/logoCosmosCinema.png') }}" alt="Cosmos Cinema Logo" class="logo">
        </div>
        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            <div class="form-row">
                <input class="input" type="email" name="login_email" id='login_email'
                    placeholder="Email" value="{{ old('login_email') }}" required>
                <p class="client-side-field-error"></p>
                @error('login_email')
                <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-row">
                <input class="input" type="password" name="login_password" id='login_password'
                    placeholder="**********" required>
                <p class="client-side-field-error"></p>
                @error('login_password')
                <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-row">
                <label>
                    <input type="checkbox" name="remember"> Recordarme
                </label>
            </div>

            <div id="g_id_onload"
                data-client_id="TU_CLIENT_ID.apps.googleusercontent.com"
                data-callback="handleCredentialResponse" 
                data-auto_prompt="false">
            </div>

            <div class="g_id_signin"
                data-type="standar"
                data-size="large"
                data-theme="outline"
                data-text="sign_in_with"
                data-shape="rectangular"
                data-logo_alignment="left">
            </div>

            <div class="form-row">
                <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                @error('recaptcha')
                <p class="error-text">{{ $message }}</p>
                @enderror
            </div>


            <div class="button-group">
                <button type="button" class="btn back-button" id="volverLoginModal">Volver</button>
                <button type="submit" class="btn">Login</button>
            </div>

        </form>
    </div>
</div>