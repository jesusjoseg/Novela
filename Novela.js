document.addEventListener('DOMContentLoaded',()=>{
    const editor = document.getElementById('md-editor');
    const preview =document.getElementById('md-preview');
    function procesarNotaPies(texto){
       if (!markdown) return '';

        // 1. Reemplazar las DEFINICIONES en Markdown plano antes de pasarlo a Marked
        // Convierte [^id]: texto en un div contenedor de la nota
        let md = markdown.replace(
            /^\[\^([a-zA-Z0-9_-]+)\]:\s*(.*)$/gm,
            '<div class="footnote-item" id="fn-$1" style="margin-top: 10px; font-size: 0.9em; opacity: 0.8;"><strong>[$1]</strong> $2 <a href="#fnref-$1" onclick="event.preventDefault(); document.getElementById(\'fnref-$1\')?.scrollIntoView({behavior: \'smooth\'});">↩</a></div>'
        );

        // 2. Reemplazar las REFERENCIAS [^id] por el superíndice
        md = md.replace(
            /\[\^([a-zA-Z0-9_-]+)\](?!\:)/g,
            '<sup class="footnote-ref"><a href="#fn-$1" id="fnref-$1" onclick="event.preventDefault(); document.getElementById(\'fn-$1\')?.scrollIntoView({behavior: \'smooth\'});">[$1]</a></sup>'
        );

        return md;
    }
    if (editor && preview){
        const updatepreview =()=>{
            const rawtext =editor.value;
            if(typeof marked !=='undefined'){
                preview.innerHTML =marked.parse(rawtext);
            }
            else{
                preview.innerHTML=rawtext;
            }
        };
        editor.addEventListener('input',updatepreview);
        updatepreview();
    }
});
/** 
*@param {String} tabId
*
*/
function switchTab(tabId){
    const tabs = document.querySelectorAll('.tab-content');
    const buttons =document.querySelectorAll('.tab-btn');
    tabs.forEach(tab=>tab.classList.remove('active'));
    buttons.forEach(btn=> btn.classList.remove('active'));
    const selectedTab = document.getElementById(tabId);
    if (selectedTab){
        selectedTab.classList.add('active');
    }
    const activeBtn =Array.from(buttons).find(btn=>
        btn.getAttribute('onclick')?.includes(tabId)
    );
    if(activeBtn){
        activeBtn.classList.add('active');
    }
}