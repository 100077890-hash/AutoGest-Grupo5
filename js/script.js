// ==========================================
// AUTOGEST - FILTRO DINÁMICO DE SERVICIOS
// ==========================================

// Arreglo con los servicios disponibles
const servicios = [
    {
        nombre: "Cambio de aceite",
        descripcion: "Cambio de aceite y revisión de niveles del vehículo."
    },
    {
        nombre: "Revisión de frenos",
        descripcion: "Inspección de pastillas, discos y sistema de frenado."
    },
    {
        nombre: "Diagnóstico computarizado",
        descripcion: "Diagnóstico electrónico para detectar fallas del vehículo."
    },
    {
        nombre: "Alineación y balanceo",
        descripcion: "Alineación de ruedas y balanceo para mejorar la conducción."
    },
    {
        nombre: "Cambio de batería",
        descripcion: "Revisión y reemplazo de la batería del vehículo."
    },
    {
        nombre: "Mantenimiento preventivo",
        descripcion: "Mantenimiento general para conservar el vehículo en buenas condiciones."
    },
    {
        nombre: "Cambio de neumáticos",
        descripcion: "Instalación y revisión de neumáticos para mayor seguridad."
    },
    {
        nombre: "Revisión del motor",
        descripcion: "Inspección general del motor y sus componentes principales."
    }
];

// Obtener elementos del HTML
const buscador = document.getElementById("buscador");
const listaServicios = document.getElementById("lista-servicios");
const mensajeSinResultados = document.getElementById("sin-resultados");

// Mostrar todos los servicios al cargar la página
mostrarServicios(servicios);

// Detectar lo que escribe el usuario
buscador.addEventListener("input", function () {

    const textoBuscado = buscador.value.toLowerCase().trim();

    // Filtrar servicios según el texto escrito
    const serviciosFiltrados = servicios.filter(function (servicio) {
        return servicio.nombre.toLowerCase().includes(textoBuscado);
    });

    // Mostrar resultados
    mostrarServicios(serviciosFiltrados);
});


// ==========================================
// FUNCIÓN PARA MOSTRAR LOS SERVICIOS
// ==========================================

function mostrarServicios(serviciosMostrar) {

    // Limpiar resultados anteriores
    listaServicios.innerHTML = "";

    // Si no existen resultados
    if (serviciosMostrar.length === 0) {

        mensajeSinResultados.style.display = "block";

        return;
    }

    // Ocultar mensaje de "sin resultados"
    mensajeSinResultados.style.display = "none";

    // Crear las tarjetas de los servicios
    serviciosMostrar.forEach(function (servicio) {

        const tarjeta = document.createElement("div");

        tarjeta.classList.add("tarjeta-servicio");

        tarjeta.innerHTML = `
            <h3>${servicio.nombre}</h3>
            <p>${servicio.descripcion}</p>
        `;

        listaServicios.appendChild(tarjeta);
    });
}