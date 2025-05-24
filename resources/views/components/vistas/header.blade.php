<div class="header-buttons" style="display: flex; align-items: center;">
    @if (session('success'))
    <div id="flash-message" class="success-message">
        {{ session('success') }}
    </div>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const flashMessage = document.getElementById('flash-message');
            if (flashMessage && flashMessage.textContent.trim() !== '') {
                flashMessage.classList.add('show');
                setTimeout(function() {
                    flashMessage.classList.remove('show');
                    setTimeout(() => {
                        flashMessage.textContent = '';
                    }, 500);
                }, 3000);
            }
        });

    </script>
    @endpush
    @endif
    <button id="mostrarMenus"> <a class="botones" href="#seccionMenus">CARTA COSMOS</a></button>
    <button id="mostrarCompra"> <a class="botones" href="#seccionCompra">COMPRAR ENTRADAS</a></button>
    @if(Auth::check())
    <button id="miCuenta"><a class="botones" href="#">MI CUENTA</a></button>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: inline;">
        @csrf
        <button type="submit" id="logout">
            <a class="botones">LOGOUT</a>
        </button>
    </form>
    @else
    <button id="mostrarRegistro"><a class="botones">ÚNETE A COSMOS</a></button>
    <button id="mostrarLogin"><a class="botones">INICIAR SESIÓN</a></button>
    @endif

</div>
<div class="logo-container">
    <img src="{{ asset('images/logoCosmosCinema.png') }}" alt="Cosmos Cinema Logo" class="cinema-logo">
</div>
