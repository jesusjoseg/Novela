document.addEventListener('DOMContentLoaded',()=>{
    const editor = document.getElementById('md-editor');
    const preview =document.getElementById('md-preview');

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