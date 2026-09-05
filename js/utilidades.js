// Utilidades generales de AutoGest

const nombreAplicacion = "AutoGest";
const anioActual = new Date().getFullYear();
const telefonoMinimo = 7;

// Arreglo con los tipos de servicios disponibles
const serviciosDisponibles = [
    "Cambio de aceite",
    "Revisión de frenos",
    "Alineación y balanceo",
    "Diagnóstico general"
];

// Devuelve el texto sin espacios innecesarios
function limpiarTexto(texto) {
    return texto.trim();
}

// Comprueba si un valor está vacío
function estaVacio(valor) {
    return limpiarTexto(valor) === "";
}

// Convierte un texto a número
function convertirANumero(valor) {
    return Number(valor);
}

// Comprueba si un teléfono tiene una cantidad mínima de dígitos
function telefonoValido(telefono) {
    return limpiarTexto(telefono).length >= telefonoMinimo;
}