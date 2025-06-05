document.addEventListener("DOMContentLoaded", function () {
    console.log("LOGIN.JS SCRIPT EJECUTÁNDOSE Y DOMContentLoaded DISPARADO");
    const modalLogin = document.getElementById("modalLogin");
    if (!modalLogin) {
        console.log("[Login] Modal de login no encontrado. Saliendo.");
        return;
    }

    const mostrarLoginBtn = document.getElementById("mostrarLogin");
    const cerrarLoginBtnModalLogin = modalLogin?.querySelector("#cerrarLogin");
    const volverLoginModalBtn = modalLogin?.querySelector("#volverLoginModal");
    const formLogin = modalLogin?.querySelector("form#login-form");
    const clientSideErrorList = modalLogin.querySelector(".client-side-errors1 ul");
    const clientSideErrorContainer = clientSideErrorList?.closest(".client-side-errors1");

    let loginRecaptchaWidgetId = null;
    let loginRecaptchaRendered = false; // Flag para el estado de renderizado

    function renderLoginRecaptcha() {
        console.log("[Login] FNC: renderLoginRecaptcha. Rendered:", loginRecaptchaRendered, "WidgetId:", loginRecaptchaWidgetId);
        const container = document.getElementById('recaptcha-login-container');

        if (!container) {
            console.error("[Login] ERR: Contenedor 'recaptcha-login-container' NO encontrado.");
            return;
        }
        // console.log("[Login] DBG: Estilo display del contenedor:", window.getComputedStyle(container).display);

        if (typeof grecaptcha !== 'undefined' && grecaptcha.render) {
            console.log("[Login] INF: grecaptcha.render disponible.");
            if (!loginRecaptchaRendered) {
                console.log("[Login] INF: Intentando renderizar por primera vez (rendered=false).");
                console.log("[Login] DBG: Sitekey a usar:", container.dataset.sitekey);
                if (!container.dataset.sitekey) {
                    console.error("[Login] ERR: Sitekey está vacío o no definido en el data-attribute!");
                    return;
                }
                try {
                    loginRecaptchaWidgetId = grecaptcha.render('recaptcha-login-container', {
                        'sitekey': container.dataset.sitekey,
                        'callback': function(response) {
                            console.log('[Login] CBK: reCAPTCHA completado. Respuesta:', response);
                        },
                        'expired-callback': function() {
                            console.log('[Login] CBK: reCAPTCHA expirado.');
                            if (loginRecaptchaWidgetId !== null) grecaptcha.reset(loginRecaptchaWidgetId);
                        },
                        'error-callback': function() {
                            console.error('[Login] CBK_ERR: error-callback de reCAPTCHA disparado.');
                            loginRecaptchaRendered = false;
                            loginRecaptchaWidgetId = null;
                        }
                    });
                    if (typeof loginRecaptchaWidgetId === 'number') {
                        loginRecaptchaRendered = true;
                        console.log("[Login] INF: Renderizado solicitado. WidgetId asignado:", loginRecaptchaWidgetId);
                    } else {
                        console.warn("[Login] WARN: grecaptcha.render no devolvió un widgetId numérico. Renderizado podría haber fallado. WidgetId:", loginRecaptchaWidgetId);
                    }
                } catch (e) {
                    console.error("[Login] ERR_CATCH: Error en grecaptcha.render:", e);
                    loginRecaptchaRendered = false;
                }
            } else if (loginRecaptchaWidgetId !== null) {
                console.log("[Login] INF: Widget ya renderizado, reseteando. WidgetId:", loginRecaptchaWidgetId);
                grecaptcha.reset(loginRecaptchaWidgetId);
            } else {
                console.warn("[Login] WARN: Marcado como renderizado pero sin WidgetId. Algo inconsistente.");
                loginRecaptchaRendered = false;
            }
        } else {
            console.error("[Login] ERR: grecaptcha o grecaptcha.render no están disponibles.");
        }
    }

    modalLogin.querySelectorAll(".password-input-container").forEach((container) => {
        const passwordInput = container.querySelector('input[type="password"], input[type="text"]');
        const toggleIcon = container.querySelector(".toggle-password");
        if (passwordInput && toggleIcon) {
            toggleIcon.addEventListener("click", function () {
                const currentType = passwordInput.getAttribute("type");
                const newType = currentType === "password" ? "text" : "password";
                passwordInput.setAttribute("type", newType);
                toggleIcon.textContent = newType === "password" ? "👁️" : "🙈";
            });
        }
    });

    function hideGeneralLoginErrors() {
        if (!clientSideErrorList || !clientSideErrorContainer) return;
        clientSideErrorList.innerHTML = "";
        clientSideErrorContainer.style.display = "none";
    }

    function clearInvalidClassesLogin() {
        modalLogin?.querySelectorAll("input.invalid").forEach((el) => el.classList.remove("invalid"));
    }

    function displayFieldErrorLogin(inputElement, message) {
        if (!inputElement) return;
        const formRow = inputElement.closest(".form-row");
        if (formRow) {
            const errorElement = formRow.querySelector(".client-side-field-error");
            if (errorElement) {
                errorElement.textContent = message;
                errorElement.style.display = "block";
            }
            inputElement.classList.add("invalid");
        }
    }

    function clearFieldErrorLogin(inputElement) {
        if (!inputElement) return;
        const formRow = inputElement.closest(".form-row");
        if (formRow) {
            const errorElement = formRow.querySelector(".client-side-field-error");
            if (errorElement) {
                errorElement.textContent = "";
                errorElement.style.display = "none";
            }
            inputElement.classList.remove("invalid");
        }
    }

    function clearClientFieldErrorsLogin() {
        modalLogin?.querySelectorAll(".form-row .client-side-field-error").forEach((el) => {
            el.textContent = "";
            el.style.display = "none";
        });
    }

    function showGeneralClientErrors(messages) {
        if (!clientSideErrorList || !clientSideErrorContainer) return;
        clientSideErrorList.innerHTML = "";
        messages.forEach((msg) => {
            const li = document.createElement("li");
            li.textContent = msg;
            clientSideErrorList.appendChild(li);
        });
        clientSideErrorContainer.style.display = messages.length > 0 ? "block" : "none";
    }

    function openModalLogin() {
        console.log("[Login] FNC: openModalLogin");
        if (modalLogin) {
            modalLogin.classList.remove("hidden");
            modalLogin.classList.add("flex");
            document.body.classList.add("modal_abierto");
            renderLoginRecaptcha();
            const firstInput = modalLogin.querySelector('input:not([type="hidden"]), select, textarea');
            if (firstInput) {
                setTimeout(() => firstInput.focus(), 50);
            }
        }
    }

    function closeModalLogin(reset = false) {
        console.log("[Login] FNC: closeModalLogin. Reset:", reset);
        if (modalLogin) {
            modalLogin.classList.remove("flex");
            modalLogin.classList.add("hidden");
            document.body.classList.remove("modal_abierto");
            if (reset && formLogin) {
                formLogin.reset();
                clearClientFieldErrorsLogin();
                hideGeneralLoginErrors();
                clearInvalidClassesLogin();
                modalLogin.querySelectorAll(".error-text").forEach((el) => {
                    el.textContent = "";
                    el.style.display = "none";
                });
                if (typeof grecaptcha !== "undefined" && grecaptcha.reset && loginRecaptchaWidgetId !== null) {
                    console.log("[Login] DBG: Reseteando reCAPTCHA al cerrar modal. WidgetId:", loginRecaptchaWidgetId);
                    grecaptcha.reset(loginRecaptchaWidgetId);
                }
            }
        }
    }

    if (mostrarLoginBtn) mostrarLoginBtn.addEventListener("click", openModalLogin);
    if (cerrarLoginBtnModalLogin) cerrarLoginBtnModalLogin.addEventListener("click", () => closeModalLogin(true));
    if (volverLoginModalBtn) volverLoginModalBtn.addEventListener("click", () => closeModalLogin(true));

    if (formLogin) {
        formLogin.addEventListener("submit", async function (event) {
            console.log("[Login] EVT: Submit interceptado.");
            event.preventDefault();
            hideGeneralLoginErrors();
            clearInvalidClassesLogin();
            clearClientFieldErrorsLogin();
            modalLogin.querySelectorAll(".error-text").forEach((el) => (el.style.display = "none"));

            let isFormValid = true;
            const emailInput = formLogin.querySelector('[name="login_email"]');
            const passwordInput = formLogin.querySelector('[name="login_password"]');

            if (!emailInput || !emailInput.value?.trim()) {
                displayFieldErrorLogin(emailInput, "El email es obligatorio.");
                isFormValid = false;
            } else if (!/\S+@\S+\.\S+/.test(emailInput.value)) {
                displayFieldErrorLogin(emailInput, "Por favor, introduce un email válido.");
                isFormValid = false;
            } else {
                clearFieldErrorLogin(emailInput);
            }

            if (!passwordInput || !passwordInput.value?.trim()) {
                displayFieldErrorLogin(passwordInput, "La contraseña es obligatoria.");
                isFormValid = false;
            } else {
                clearFieldErrorLogin(passwordInput);
            }

            console.log("[Login] DBG: Validando reCAPTCHA. WidgetId:", loginRecaptchaWidgetId);
            const recaptchaLoginContainer = document.getElementById("recaptcha-login-container");
            let recaptchaResponseLogin = "";
            if (typeof grecaptcha !== "undefined" && loginRecaptchaWidgetId !== null) {
                recaptchaResponseLogin = grecaptcha.getResponse(loginRecaptchaWidgetId);
                console.log("[Login] DBG: Respuesta reCAPTCHA:", recaptchaResponseLogin);
            } else {
                console.warn("[Login] WARN: No se pudo obtener respuesta de reCAPTCHA (grecaptcha no definido o widgetId es null).");
            }

            if (recaptchaLoginContainer && typeof grecaptcha !== "undefined" && loginRecaptchaWidgetId !== null && recaptchaResponseLogin.length === 0) {
                console.log("[Login] VALIDATION_ERR: reCAPTCHA vacío.");
                showGeneralClientErrors(["Por favor, completa el reCAPTCHA."]);
                isFormValid = false;
            } else if (recaptchaLoginContainer && (loginRecaptchaWidgetId === null || typeof grecaptcha === "undefined") && loginRecaptchaRendered) {
                console.log("[Login] VALIDATION_ERR: Problema con reCAPTCHA (renderizado pero sin ID/grecaptcha).");
                showGeneralClientErrors(["Hubo un problema con el reCAPTCHA. Inténtalo de nuevo."]);
                isFormValid = false;
            } else if (!recaptchaLoginContainer && document.querySelector('.g-recaptcha')) {
                console.log("[Login] VALIDATION_ERR: Contenedor reCAPTCHA específico no encontrado, pero existe un .g-recaptcha.");
                showGeneralClientErrors(["Error de configuración del reCAPTCHA."]);
                isFormValid = false;
            }

            console.log("[Login] FNC_END: Validación de formulario. Es válido:", isFormValid);
            if (isFormValid) {
                console.log("[Login] INF: Formulario válido, enviando...");
                event.target.submit();
            } else {
                console.log("[Login] INF: Formulario NO válido.");
            }
        });
    }

    const loginFormForServerErrors = document.getElementById("login-form");
    let hasLoginServerErrors = false;
    if (loginFormForServerErrors) {
        const serverErrorMessages = loginFormForServerErrors.querySelectorAll(".error-text");
        serverErrorMessages.forEach((el) => {
            if (el.textContent.trim() !== "") {
                hasLoginServerErrors = true;
                el.style.display = "block";
            }
        });
    }

    if (hasLoginServerErrors) {
        console.log("[Login] DBG: Errores de backend detectados al cargar, abriendo modal.");
        if (modalLogin && (modalLogin.classList.contains("hidden") || !modalLogin.classList.contains("flex"))) {
            openModalLogin();
        }
    }

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape" || event.keyCode === 27) {
            if (modalLogin?.classList.contains("flex")) closeModalLogin(true);
        }
    });

    if (modalLogin) {
        modalLogin.addEventListener("click", function (event) {
            if (event.target === modalLogin && modalLogin.classList.contains("flex")) {
                closeModalLogin(true);
            }
        });
    }
});