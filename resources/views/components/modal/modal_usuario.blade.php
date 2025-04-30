
<div class="modal hidden" id="modalCuenta">
    
    <div class="modal-content">
        <button class="close-button" id="cerrarCuentaModal">&times;</button>

        <div class="modal-header">
            <h2 class="modal-title">Mi Cuenta</h2>
        </div>

        <div class="modal-body">

            <div class="user-info-item">
                <strong>Nombre:</strong> <span id="infoNombre"></span> {{-- Nuevo span para Nombre --}}
            </div>
            <div class="user-info-item">
                <strong>Apellidos:</strong> <span id="infoApellidos"></span> {{-- Nuevo span para Apellidos --}}
            </div>
            <div class="user-info-item">
                <strong>Email:</strong> <span id="infoEmail"></span>
            </div>
            <div class="user-info-item">
                <strong>Fecha de Nacimiento:</strong> <span id="infoFechaNacimiento"></span>
            </div>
            <div class="user-info-item">
                <strong>Teléfono:</strong> <span id="infoTelefono"></span>
            </div>
            <div class="user-info-item">
                <strong>DNI:</strong> <span id="infoDni"></span>
            </div>
            <div class="user-info-item">
                <strong>Dirección:</strong> <span id="infoDireccion"></span>
            </div>
            <div class="user-info-item">
                <strong>Ciudad:</strong> <span id="infoCiudad"></span>
            </div>
            <div class="user-info-item">
                <strong>Código Postal:</strong> <span id="infoCodigoPostal"></span>
            </div>

            <div class="user-info-item">
                <strong>ID Descuento:</strong> <span id="infoIdDescuento"></span>
            </div>

            {{-- Opcional: Área para mostrar mensajes de error si la carga de datos falla --}}
            {{-- <div class="client-side-errors error-messages"></div> --}}

            {{-- Opcional: Botón para editar perfil si lo implementas --}}
            <div class="modal-actions">
                <button class="btn btn-primary" id="editarPerfilBtn">Editar Perfil</button>
            </div>
        </div>
    </div>
</div>