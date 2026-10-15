// Mejora progresiva: navegación, formularios y confirmaciones funcionan sin JavaScript.
const cabecera = document.querySelector('[data-cabecera]');
const menuToggle = document.querySelector('.menu-toggle');
const navegacion = document.querySelector('#navegacion');
if (cabecera && menuToggle && navegacion) {
    const movil = window.matchMedia('(max-width: 700px)');
    const cambiarMenu = (abierto) => {
        menuToggle.setAttribute('aria-expanded', String(abierto));
        navegacion.dataset.open = String(abierto);
    };
    menuToggle.hidden = false;
    cabecera.dataset.navReady = 'true';
    cambiarMenu(!movil.matches);
    movil.addEventListener('change', () => cambiarMenu(!movil.matches));
    menuToggle.addEventListener('click', () => cambiarMenu(menuToggle.getAttribute('aria-expanded') !== 'true'));
    navegacion.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && movil.matches) {
            cambiarMenu(false);
            menuToggle.focus();
        }
    });
}

const buscar = document.querySelector('#buscar');
if (buscar) {
    document.querySelector('[data-buscador]').hidden = false;
    const tarjetas = [...document.querySelectorAll('[data-servicio]')];
    const resultado = document.querySelector('#resultado-busqueda');
    const normalizar = (texto) => texto.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
    const filtrarServicios = () => {
        const consulta = normalizar(buscar.value);
        tarjetas.forEach((tarjeta) => { tarjeta.hidden = !normalizar(tarjeta.dataset.servicio).includes(consulta); });
        const cantidad = tarjetas.filter((tarjeta) => !tarjeta.hidden).length;
        document.querySelector('#sin-resultados').hidden = cantidad > 0;
        resultado.hidden = false;
        resultado.textContent = `${cantidad} ${cantidad === 1 ? 'servicio encontrado' : 'servicios encontrados'}.`;
    };
    buscar.addEventListener('input', filtrarServicios);
    filtrarServicios();
}

// Se bloquea sólo el envío repetido en la interfaz; Laravel sigue validando cada petición.
const formulariosPendientes = new Map();
document.querySelectorAll('[data-submit-form]').forEach((formulario) => {
    formulario.addEventListener('submit', (event) => {
        if (formulariosPendientes.has(formulario)) {
            event.preventDefault();
            return;
        }
        if (event.defaultPrevented) return;
        const botones = [...formulario.querySelectorAll('button[type="submit"]')];
        formulariosPendientes.set(formulario, botones.map((boton) => ({ boton, texto: boton.textContent, disabled: boton.disabled })));
        formulario.setAttribute('aria-busy', 'true');
        botones.forEach((boton) => {
            boton.disabled = true;
            boton.textContent = boton.dataset.loadingText || 'Enviando…';
        });
    });
});
// Al volver con Atrás, el navegador puede recuperar una página con controles deshabilitados.
window.addEventListener('pageshow', () => {
    formulariosPendientes.forEach((botones, formulario) => {
        formulario.removeAttribute('aria-busy');
        botones.forEach(({ boton, texto, disabled }) => {
            boton.disabled = disabled;
            boton.textContent = texto;
        });
    });
    formulariosPendientes.clear();
});

(document.querySelector('[data-validation-summary]') || document.querySelector('[data-confirmacion]'))?.focus();
