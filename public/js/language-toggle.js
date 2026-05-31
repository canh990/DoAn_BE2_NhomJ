// Enhanced Language Selector with localStorage persistence
(function(){
    const sel = document.getElementById('language-select');
    if (!sel) return;

    // Initialize language selector with saved preference
    const savedLocale = localStorage.getItem('app-language');
    if (savedLocale && sel.value !== savedLocale) {
        sel.value = savedLocale;
    }

    sel.addEventListener('change', function(){
        const locale = this.value;
        
        try{
            // Save language preference to localStorage
            localStorage.setItem('app-language', locale);
            
            // Also set HTML lang attribute for accessibility
            document.documentElement.lang = locale === 'vi' ? 'vi' : 'en';
            
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            const token = tokenMeta ? tokenMeta.getAttribute('content') : null;
            
            // Send language preference to backend
            fetch('/settings/personal/language', {
                method: 'POST',
                credentials: 'same-origin',
                headers: Object.assign(
                    { 'Content-Type': 'application/json', 'Accept': 'application/json' }, 
                    token ? { 'X-CSRF-TOKEN': token } : {}
                ),
                body: JSON.stringify({ locale })
            }).then(res => {
                // Reload page to apply translations immediately
                location.reload();
            }).catch(err => {
                console.error('Language change error:', err);
                location.reload();
            });
        }catch(e){
            console.error('Language selector error:', e);
            location.reload();
        }
    });
})();
