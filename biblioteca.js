document.addEventListener('DOMContentLoaded',()=>{
    const form = document.getElementById('finder-form');
    const grid = document.getElementById('biblioteca-grid');
    if(!form ||!grid)return;
    function actualizarFiltro(){
        const formData = new FormData(form);
        const params= new URLSearchParams();
        if(formData.get('texto')) params.append('texto',formData.get('texto'));
        if(formData.get('orden')) params.append('orden',formData.get('orden'));
        const generos= formData.getAll('Genero[]');
        generos.forEach(g => params.append('Genero[]',g));
        fetch(`filtrar_biblioteca.php?${params.toString()}`)
        .then(res=>res.json())
        .then(novelas=>{
            if(novelas.length === 0){
                grid.innerHTML='<p style="color:#b3b3b3;grid-column:1/-1;text-align:center;padding:40px;font-size:16">Niguna novela coiciede con la busquedar</p>';
                return;
            }
            grid.innerHTML=novelas.map(novela =>`
                <a href="${novela.Link}" class="novela-card">
                    <img src="${novela.Portada}" alt="${novela.Titulo}">
                    <div class="novela-info">
                        <span class="novela.titulo">${novela.Titulo}</span>
                        <small class="novela-genero">${novela.Genero}</small>
                    </div>
                </a>`).join('');
        })
        .catch(err =>console.error("Erro a filtrar la biblioteca:",err));
    }
    form.addEventListener('input',actualizarFiltro);
    form.addEventListener('change',actualizarFiltro);
    actualizarFiltro();
});