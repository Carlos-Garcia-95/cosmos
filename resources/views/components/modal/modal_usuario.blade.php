<div class="modal hidden" id="modalCuenta">

    <div class="modal-content">
        <button class="close-button" id="cerrarCuentaModal">&times;</button>

        <div class="modal-header">
            <h2 class="modal-title">Mi Cuenta</h2>
            <button class="btn btn-primary edit-button" id="editarPerfilBtn">Editar Perfil</button>
        </div>

        <div class="modal-body">

            {{-- Nombre --}}
            <div class="user-info-item form-row">
                <strong>Nombre:</strong>
                <span id="infoNombreDisplay" class="display-field" style="display: inline-block;"></span> {{-- Estado de visualización --}}
                <div class="error">
                <input type="text" id="infoNombreEdit" name="nombre" class="edit-field input" style="display: none;" required>
                <p id="infoNombreError" class="client-side-field-error error-text" style="display: none;"></p>
                </div>            
            </div>

            {{-- Apellidos --}}
            <div class="user-info-item form-row">
                <strong>Apellidos:</strong>
                <span id="infoApellidosDisplay" class="display-field"></span> {{-- Estado de visualización --}}
                <div class="error">
                <input type="text" id="infoApellidosEdit" name="apellidos" class="edit-field input" style="display: none;" required>
                <p id="infoApellidosError" class="client-side-field-error error-text" style="display: none;"></p>
                </div>
            </div>

            {{-- Email (NO Editable) --}}
            <div class="user-info-item form-row">
                <strong>Email:</strong>
                <span id="infoEmailDisplay" class="display-field" style="display: inline-block;"></span> {{-- Solo visualización --}}
                <input type="email" id="infoEmailEdit" name="email" class="edit-field input" style="display: none;" disabled>
            </div>

            {{-- Fecha de Nacimiento --}}
            <div class="user-info-item form-row">
                <strong>Fecha de Nacimiento:</strong>
                <span id="infoFechaNacimientoDisplay" class="display-field" style="display: inline-block;"></span> {{-- Solo visualización --}}
                <input type="date" id="infoFechaNacimientoEdit" name="fecha_nacimiento" class="edit-field input" style="display: none;" disabled>
            </div>

            {{-- Teléfono --}}
            <div class="user-info-item form-row">
                <strong>Teléfono:</strong>
                <span id="infoTelefonoDisplay" class="display-field" style="display: inline-block;"></span> {{-- Estado de visualización --}}
                <div class="error">
                <input type="text" id="infoTelefonoEdit" name="numero_telefono" class="edit-field input" style="display: none;" required maxlength="9" minlength="9">
                <p id="infoTelefonoError" class="client-side-field-error error-text" style="display: none;"></p>
                </div>
            </div>

            {{-- DNI --}}
            <div class="user-info-item form-row">
                <strong>DNI:</strong>
                <span id="infoDniDisplay" class="display-field" style="display: inline-block;"></span> {{-- Solo visualización --}}
                <input type="text" id="infoDniEdit" name="dni" class="edit-field input" style="display: none;" disabled>
            </div>

            {{-- Dirección --}}
            <div class="user-info-item form-row">
                <strong>Dirección:</strong>
                <span id="infoDireccionDisplay" class="display-field" style="display: inline-block;"></span> {{-- Estado de visualización --}}
                <div class="error">
                <input type="text" id="infoDireccionEdit" name="direccion" class="edit-field input" style="display: none;">
                <p id="infoDireccionError" class="client-side-field-error error-text" style="display: none;"></p>
                </div>
            </div>

            {{-- Ciudad --}}
            <div class="user-info-item form-row">
                <strong>Ciudad:</strong>
                <span id="infoCiudadDisplay" class="display-field" style="display: inline-block;"></span> {{-- Estado de visualización --}}
                <div class="error">
                <select id="infoCiudadEdit" name="ciudad_id" class="edit-field input" style="display: none;" required>
                    <option value="">Ciudades</option>
                </select>
                <p id="infoCiudadError" class="client-side-field-error error-text" style="display: none;"></p>
                </div>
            </div>

            {{-- Código Postal --}}
            <div class="user-info-item form-row">
                <strong>Código Postal:</strong>
                <span id="infoCodigoPostalDisplay" class="display-field" style="display: inline-block;"></span> {{-- Estado de visualización --}}
                <div class="error">
                <input type="text" id="infoCodigoPostalEdit" name="codigo_postal" class="edit-field input" style="display: none;" required maxlength="5" minlength="5">
                <p id="infoCodigoPostalError" class="client-side-field-error error-text" style="display: none;"></p>
                </div>
            </div>

            {{-- ID Descuento --}}
            <div class="user-info-item form-row">
                <strong>ID Descuento:</strong>
                <span id="infoIdDescuentoDisplay" class="display-field"></span> {{-- Solo visualización --}}
            </div>

            <div class="modal-actions">

                <button class="btn btn-primary" id="guardarCambiosBtn" style="display: none;">Guardar Cambios</button>

                <button class="btn btn-secondary" id="cancelarEdicionBtn" style="display: none;">Cancelar</button>

            </div>

            <div id="profileUpdateSuccessMessage">
            </div>

            <div class="client-side-errors error-messages" style="display: none">
                <ul>
                </ul>
            </div>
        </div>
    </div>
</div>