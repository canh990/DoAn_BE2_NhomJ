// Language selector: POST selected locale to server then reload
(function(){
    const sel = document.getElementById('language-select');
    if (!sel) return;

    sel.addEventListener('change', function(){
        const locale = this.value;
        try{
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            const token = tokenMeta ? tokenMeta.getAttribute('content') : null;
            fetch('/settings/personal/language', {
                method: 'POST',
                credentials: 'same-origin',
                headers: Object.assign({ 'Content-Type': 'application/json', 'Accept': 'application/json' }, token ? { 'X-CSRF-TOKEN': token } : {}),
                body: JSON.stringify({ locale })
            }).then(res => {
                if (res.ok) location.reload();
                else location.reload();
            }).catch(()=> location.reload());
        }catch(e){
            location.reload();
        }
    });
})();
