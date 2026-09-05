// Validaciones del formulario de citas de AutoGest

// Valida que un campo obligatorio no esté vacío
function validarObligatorio(valor) {
    return !estaVacio(valor);
}

// Valida que el correo tenga un formato básico correcto
function validarCorreo(correo) {
    const patronCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return patronCorreo.test(limpiarTexto(correo));
}

// Valida que el teléfono tenga un formato permitido
function validarTelefono(telefono) {
    const telefonoLimpio = limpiarTexto(telefono);
    const digitos = telefonoLimpio.replace(/\D/g, "");

    return /^[\d\s()+-]+$/.test(telefonoLimpio) &&
           telefonoValido(digitos);
}

// Valida que el año del vehículo sea un número válido
function validarAnio(anio) {
    const anioNumero = convertirANumero(anio);
    const anioActual = new Date().getFullYear();

    return Number.isInteger(anioNumero) &&
           anioNumero >= 1900 &&
           anioNumero <= anioActual;
}