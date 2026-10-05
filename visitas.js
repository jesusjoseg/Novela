document.addEventListener("DOMContentLoaded", function () {
    const elVisitas = document.getElementById('contador-visitas');
    if (!elVisitas) return;

    // Lee el ID de la novela desde el atributo data-id
    const novelaId = elVisitas.getAttribute('data-id');
    if (!novelaId) return;

    const formdata = new FormData();
    formdata.append('novela_id', novelaId);

    fetch('api_registrar_vista.php', {
        method: 'POST',
        body: formdata
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.nuevas_visitas !== undefined) {
            elVisitas.textContent = data.nuevas_visitas;
        }
    })
    .catch(error => console.error('Error incrementando vista:', error));
});