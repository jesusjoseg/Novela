document.addEventListener('DOMContentLoaded', () => {
    const editor = document.getElementById('md-editor');
    const preview = document.getElementById('md-preview');

    function procesarNotasPie(texto) {
        if (!texto) return '';

        // 1. Reemplazar las DEFINICIONES de notas al pie [^1]: Texto...
        let md = texto.replace(
            /^\[\^([a-zA-Z0-9_-]+)\]:\s*(.*)$/gm,
            '<div class="footnote-item" id="fn-$1" style="margin-top: 10px; font-size: 0.9em; opacity: 0.8;"><strong>[$1]</strong> $2 <a href="#fnref-$1" onclick="event.preventDefault(); document.getElementById(\'fnref-$1\')?.scrollIntoView({behavior: \'smooth\'});">↩</a></div>'
        );

        // 2. Reemplazar las REFERENCIAS [^1] por superíndices cliqueables
        md = md.replace(
            /\[\^([a-zA-Z0-9_-]+)\](?!\:)/g,
            '<sup class="footnote-ref"><a href="#fn-$1" id="fnref-$1" onclick="event.preventDefault(); document.getElementById(\'fn-$1\')?.scrollIntoView({behavior: \'smooth\'});">[$1]</a></sup>'
        );

        return md;
    }

    if (editor && preview) {
        const updatepreview = () => {
            const rawtext = editor.value;
            // Procesar primero las notas al pie y luego convertir Markdown a HTML
            const textoProcesado = procesarNotasPie(rawtext);

            if (typeof marked !== 'undefined') {
                preview.innerHTML = marked.parse(textoProcesado);
            } else {
                preview.innerHTML = textoProcesado;
            }
        };

        editor.addEventListener('input', updatepreview);
        updatepreview();
    }

    // --- NUEVA LÓGICA PARA EDITAR NOVELAS Y CAPÍTULOS ---

    // 1. Cargar datos de la Novela seleccionada para editar
    const selectEditNovela = document.getElementById('select_edit_novela');
    if (selectEditNovela) {
        selectEditNovela.addEventListener('change', (e) => {
            const selectedOption = e.target.options[e.target.selectedIndex];
            if (selectedOption && selectedOption.value) {
                document.getElementById('edit_titulo').value = selectedOption.getAttribute('data-titulo') || '';
                document.getElementById('edit_portada').value = selectedOption.getAttribute('data-portada') || '';
                document.getElementById('edit_estado').value = selectedOption.getAttribute('data-estado') || 'Pendiente';
                document.getElementById('edit_descripcion').value = selectedOption.getAttribute('data-descripcion') || '';
            }
        });
    }

    // 2. Cargar capítulos vía REST API cuando cambia la novela en "Editar Capítulo"
    const capNovelaSelect = document.getElementById('cap_novela_select');
    if (capNovelaSelect) {
        capNovelaSelect.addEventListener('change', async (e) => {
            const novelaId = e.target.value;
            const selectCap = document.getElementById('edit_capitulo_id');
            selectCap.innerHTML = '<option value="">Cargando capítulos...</option>';

            if (!novelaId) return;

            try {
                const response = await fetch(`https://ahprflxvnrovrwxaojrw.supabase.co/rest/v1/capitulos?select=id,Titulo,Contenido_markdown,Capitulo&novela_id=eq.${novelaId}&order=Capitulo.asc`, {
                    headers: {
                        'apikey': 'sb_publishable_W0IkvLXPpoLZ0fNBk_RENg_iNcnFRuf',
                        'Authorization': 'Bearer sb_publishable_W0IkvLXPpoLZ0fNBk_RENg_iNcnFRuf'
                    }
                });
                const data = await response.json();

                selectCap.innerHTML = '<option value="">-- Seleccionar Capítulo --</option>';
                data.forEach(cap => {
                    const opt = document.createElement('option');
                    opt.value = cap.id;
                    opt.textContent = `Cap. ${cap.Capitulo}: ${cap.Titulo}`;
                    opt.dataset.titulo = cap.Titulo;
                    opt.dataset.markdown = cap.Contenido_markdown;
                    selectCap.appendChild(opt);
                });
            } catch (err) {
                selectCap.innerHTML = '<option value="">Error al cargar capítulos</option>';
            }
        });
    }

    // 3. Rellenar campos del capítulo seleccionado
    const editCapituloId = document.getElementById('edit_capitulo_id');
    if (editCapituloId) {
        editCapituloId.addEventListener('change', (e) => {
            const selectedOption = e.target.options[e.target.selectedIndex];
            if (selectedOption && selectedOption.value) {
                document.getElementById('edit_titulo_capitulo').value = selectedOption.dataset.titulo || '';
                document.getElementById('edit_contenido_markdown').value = selectedOption.dataset.markdown || '';
            }
        });
    }
});

/** 
 * Cambiar entre pestañas en el Dashboard
 * @param {string} tabId
 */
function switchTab(tabId) {
    const tabs = document.querySelectorAll('.tab-content');
    const buttons = document.querySelectorAll('.tab-btn');

    tabs.forEach(tab => tab.classList.remove('active'));
    buttons.forEach(btn => btn.classList.remove('active'));

    const selectedTab = document.getElementById(tabId);
    if (selectedTab) {
        selectedTab.classList.add('active');
    }

    const activeBtn = Array.from(buttons).find(btn =>
        btn.getAttribute('onclick')?.includes(tabId)
    );
    if (activeBtn) {
        activeBtn.classList.add('active');
    }
}