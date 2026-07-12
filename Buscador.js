document.addEventListener('DOMContentLoaded',()=>{
    const inputBuscador =document.getElementById('buscador-input');
    const ContenedorResultados = document.getElementById('resultados-busqueda');
    console.log("Input encontrado:", inputBuscador);
    console.log("Contenedor encontrado:", ContenedorResultados);
    if (!inputBuscador || !ContenedorResultados){
        console.error("el js se detuvo x q no encroto los dato");
        return;
    }
    inputBuscador.addEventListener('input',function(){
        let query =this.value;
        if (query.length<2){
            ContenedorResultados.style.display='none';
            return;
        }
        fetch('buscarajax.php?q='+ encodeURIComponent(query))
        .then(response=> response.json())
        .then(data=>{
            ContenedorResultados.innerHTML='';
            if (data.length ===0){
                ContenedorResultados.innerHTML='<p style="padding:10px;margin:0;color:#ff6b35">No se encotraron Novelas</p>';
                ContenedorResultados.style.display='block';
                return;
            }
            data.forEach(novela=>{
                let item= document.createElement('a');
                item.href= novela.Link;
                item.style.display='flex';
                item.style.alignItems='center';
                item.style.padding='10px';
                item.style.gap='12px';
                item.style.textDecoration=' none';
                item.style.borderBottom='1px solid';
                item.style.color='';
                item.innerHTML=`
                <img src="${novela.Portada}" style="width:40px;height:55px;object-fit:cover; border-radius:4px">
                <div>
                    <div style="font-size:14px; line-height:1.2">${novela.Titulo}</div>
                </div>
                `;
                item.addEventListener('mouseenter',()=>item.style.backgroundColor='rgba(255,107,53,0.1)');
                item.addEventListener('mouseleave',()=>item.style.backgroundColor='transparent');
                ContenedorResultados.appendChild(item);
            });
            ContenedorResultados.style.display='block';
        })
        .catch(error => console.error("Error en la busqueda:", error));
    });
    document.addEventListener('click',function(e){
        if (!e.target.closest('#buscador-input')&& !e.target.closest('#resultados-busqueda')){
            ContenedorResultados.style.display='none';
        }
    });
});