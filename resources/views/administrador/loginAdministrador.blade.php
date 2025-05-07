<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Cosmos Cinema</title>
    @vite(['resources/css/adminLogin.css',
            "resources/js/loginAdmin.js"])
</head>
<body>
<div class="login-container">
        <h2>Admin Cosmos</h2>
        <form action="{{ route('admin.login.submit') }}" method="POST" id="adminLoginForm" class="login-form">
            @csrf

            @if (session('error'))
                <div class="error-message general-error">{{ session('error') }}</div>
            @endif

            <input type="email" name="email" placeholder="Email" required autofocus value="{{ old('email') }}">
            <div class="input-password-container">
                <input type="password" name="password" placeholder="Contraseña" required>
                <span class="toggle-password">
                    👁️
                </span>
            </div>
            <input type="text" name="codigo_administrador" placeholder="Código de Administrador" required value="{{ old('codigo_administrador') }}">

            @if ($errors->any())
                <div class="validation-errors">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <button type="submit">Login</button>
        </form>
    </div>

</body>
</html>