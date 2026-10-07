// Mejora progresiva: los formularios funcionan también sin JavaScript.
const buscar = document.querySelector('#buscar');
if (buscar) {
    document.querySelector('[data-buscador]').hidden = false;
    const tarjetas = [...document.querySelectorAll('[data-servicio]')];
    const normalizar = (texto) => texto.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
    buscar.addEventListener('input', () => {
        const consulta = normalizar(buscar.value);
        tarjetas.forEach((tarjeta) => { tarjeta.hidden = !normalizar(tarjeta.dataset.servicio).includes(consulta); });
        document.querySelector('#sin-resultados').hidden = tarjetas.some((tarjeta) => !tarjeta.hidden);
    });
}
