document.addEventListener('DOMContentLoaded', () => {
    const inputBuscador = document.getElementById('buscador-input');
    const ContenedorResultados = document.getElementById('resultados-busqueda');

    if (!inputBuscador || !ContenedorResultados) {
        console.error("El JS se detuvo porque no encontró los elementos en el DOM.");
        return;
    }

    // Estilos CSS dinámicos
    ContenedorResultados.style.position = 'absolute';
    ContenedorResultados.style.top = '100%';
    ContenedorResultados.style.left = '0';
    ContenedorResultados.style.width = '100%';
    ContenedorResultados.style.backgroundColor = '#1a1a1a';
    ContenedorResultados.style.boxShadow = '0px 4px 10px rgba(0,0,0,0.5)';
    ContenedorResultados.style.borderRadius = '4px';
    ContenedorResultados.style.zIndex = '99999';
    ContenedorResultados.style.maxHeight = '350px';
    ContenedorResultados.style.overflowY = 'auto';

    if (inputBuscador.parentElement) {
        inputBuscador.parentElement.style.position = 'relative';
    }

    inputBuscador.addEventListener('input', function () {
        let query = this.value.trim();

        if (query.length < 2) {
            ContenedorResultados.style.display = 'none';
            ContenedorResultados.innerHTML = '';
            return;
        }

        fetch('buscarajax.php?q=' + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
                ContenedorResultados.innerHTML = '';

                if (!Array.isArray(data) || data.length === 0) {
                    ContenedorResultados.innerHTML = '<p style="padding:10px;margin:0;color:#ff6b35;font-size:13px;">No se encontraron novelas</p>';
                    ContenedorResultados.style.display = 'block';
                    return;
                }

                data.forEach(novela => {
                    let item = document.createElement('a');
                    let cleanLink = novela.Link ? novela.Link.replace(/&amp;/g, '&') : '#';

                    item.href = cleanLink;
                    item.style.display = 'flex';
                    item.style.alignItems = 'center';
                    item.style.padding = '8px 10px';
                    item.style.gap = '10px';
                    item.style.textDecoration = 'none';
                    item.style.borderBottom = '1px solid #333';
                    item.style.color = '#ffffff';

                    item.innerHTML = `
                        <img src="${novela.Portada}" style="width:38px;height:52px;object-fit:cover;border-radius:3px;flex-shrink:0;">
                        <div style="flex-grow:1;overflow:hidden;">
                            <div style="font-size:13px;font-weight:bold;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${novela.Titulo}</div>
                            <div style="font-size:11px;color:#aaa;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${novela.Genero}</div>
                        </div>
                    `;

                    item.addEventListener('mouseenter', () => item.style.backgroundColor = 'rgba(255,107,53,0.2)');
                    item.addEventListener('mouseleave', () => item.style.backgroundColor = 'transparent');

                    ContenedorResultados.appendChild(item);
                });

                ContenedorResultados.style.display = 'block';
            })
            .catch(error => console.error("Error en la búsqueda:", error));
    });

    document.addEventListener('click', function (e) {
        if (!inputBuscador.contains(e.target) && !ContenedorResultados.contains(e.target)) {
            ContenedorResultados.style.display = 'none';
        }
    });
});