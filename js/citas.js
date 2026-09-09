// Funcionalidad del formulario de citas de AutoGest

const formulario = document.querySelector(".formulario-cita");
const mensajeExito = document.querySelector("#mensaje-exito");

// Obtiene un campo mediante su ID
function obtenerCampo(id) {
    return document.querySelector(`#${id}`);
}

// Muestra un mensaje de error junto al campo
function mostrarError(id, mensaje) {
    const mensajeError = document.querySelector(`#error-${id}`);

    if (mensajeError) {
        mensajeError.textContent = mensaje;
    }
}

// Limpia el mensaje de error de un campo
function limpiarError(id) {
    const mensajeError = document.querySelector(`#error-${id}`);

    if (mensajeError) {
        mensajeError.textContent = "";
    }
}

// Limpia todos los mensajes de error
function limpiarErrores() {
    const mensajes = document.querySelectorAll(".mensaje-error");

    mensajes.forEach((mensaje) => {
        mensaje.textContent = "";
    });
}

// Valida todos los campos obligatorios del formulario
function validarFormulario() {
    let formularioValido = true;

    const nombre = obtenerCampo("nombre").value;
    const apellido = obtenerCampo("apellido").value;
    const telefono = obtenerCampo("telefono").value;
    const correo = obtenerCampo("correo").value;
    const marca = obtenerCampo("marca").value;
    const modelo = obtenerCampo("modelo").value;
    const anio = obtenerCampo("anio").value;
    const placa = obtenerCampo("placa").value;
    const servicio = obtenerCampo("servicio").value;
    const fecha = obtenerCampo("fecha").value;
    const hora = obtenerCampo("hora").value;

    limpiarErrores();

    if (!validarObligatorio(nombre)) {
        mostrarError("nombre", "El nombre es obligatorio.");
        formularioValido = false;
    }

    if (!validarObligatorio(apellido)) {
        mostrarError("apellido", "El apellido es obligatorio.");
        formularioValido = false;
    }

    if (!validarTelefono(telefono)) {
        mostrarError("telefono", "Ingrese un teléfono válido.");
        formularioValido = false;
    }

    if (!validarCorreo(correo)) {
        mostrarError("correo", "Ingrese un correo electrónico válido.");
        formularioValido = false;
    }

    if (!validarObligatorio(marca)) {
        mostrarError("marca", "La marca es obligatoria.");
        formularioValido = false;
    }

    if (!validarObligatorio(modelo)) {
        mostrarError("modelo", "El modelo es obligatorio.");
        formularioValido = false;
    }

    if (!validarAnio(anio)) {
        mostrarError("anio", "Ingrese un año válido.");
        formularioValido = false;
    }

    if (!validarObligatorio(placa)) {
        mostrarError("placa", "La placa es obligatoria.");
        formularioValido = false;
    }

    if (!validarObligatorio(servicio)) {
        mostrarError("servicio", "Seleccione un servicio.");
        formularioValido = false;
    }

    if (!validarObligatorio(fecha)) {
        mostrarError("fecha", "Seleccione una fecha.");
        formularioValido = false;
    }

    if (!validarObligatorio(hora)) {
        mostrarError("hora", "Seleccione una hora.");
        formularioValido = false;
    }

    return formularioValido;
}

// Muestra la confirmación de la cita
function mostrarConfirmacion(nombre) {
    mensajeExito.textContent =
        `¡Cita registrada correctamente, ${nombre}! Hemos recibido su solicitud.`;

    mensajeExito.style.display = "block";
}

// Oculta el mensaje de confirmación
function limpiarConfirmacion() {
    mensajeExito.textContent = "";
    mensajeExito.style.display = "none";
}

// Interceptar el envío del formulario
formulario.addEventListener("submit", (evento) => {
    evento.preventDefault();

    limpiarConfirmacion();

    if (validarFormulario()) {
    const nombre = obtenerCampo("nombre").value;

    formulario.reset();
    limpiarErrores();
    mostrarConfirmacion(nombre);
    }
});

// Limpiar el error cuando el usuario modifica un campo
const camposFormulario = formulario.querySelectorAll("input, select, textarea");

camposFormulario.forEach((campo) => {
    campo.addEventListener("input", () => {
        limpiarError(campo.id);
        limpiarConfirmacion();
    });

    campo.addEventListener("change", () => {
        limpiarError(campo.id);
        limpiarConfirmacion();
    });
});

// Limpiar mensajes al restablecer el formulario
formulario.addEventListener("reset", () => {
    limpiarErrores();
});