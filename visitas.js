document.addEventListener("DOMContentLoaded", function () {
    const elVisitas = document.getElementById('contador-visitas');
    if (!elVisitas) return;

    const novelaId = elVisitas.getAttribute('data-id');
    if (!novelaId) return;

    // Cambiamos a método GET usando la URL directamente para evitar el bloqueo POST del hosting gratuito
    fetch('api_registrar_vista.php?novela_id=' + encodeURIComponent(novelaId), {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success && data.nuevas_visitas !== undefined) {
            elVisitas.textContent = data.nuevas_visitas;
        }
    })
    .catch(error => console.error('Error incrementando vista:', error));
});