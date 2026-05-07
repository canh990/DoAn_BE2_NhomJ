// Theme toggle: toggles 'dark' class on <html> and persists choice in localStorage
// Theme toggle: toggles 'dark' class on <html> and persists choice in localStorage
(function(){
    const toggle = document.getElementById('theme-toggle');
    if (!toggle) return;

    function applyTheme(theme){
        if (theme === 'light'){
            document.documentElement.classList.remove('dark');
            document.documentElement.classList.add('light');
            document.body.style.backgroundColor = '#ffffff';
            document.body.style.color = '#000000';
        } else {
            document.documentElement.classList.remove('light');
            document.documentElement.classList.add('dark');
            // restore dark inline background used in the view
            document.body.style.backgroundColor = '#0a0e1a';
            document.body.style.color = '';
        }
        try{ localStorage.setItem('theme', theme); } catch(e){}
        toggle.checked = (theme === 'dark');
    }

    // initialize from saved preference or current html class
    try{
        const saved = localStorage.getItem('theme');
        if (saved === 'light' || saved === 'dark') {
            applyTheme(saved);
        } else {
            const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            const hasDarkClass = document.documentElement.classList.contains('dark');
            applyTheme(hasDarkClass || prefersDark ? 'dark' : 'light');
        }
    }catch(e){
        const hasDarkClass = document.documentElement.classList.contains('dark');
        applyTheme(hasDarkClass ? 'dark' : 'light');
    }

    // toggle handler
    toggle.addEventListener('change', function(){
        const theme = this.checked ? 'dark' : 'light';
        applyTheme(theme);

        // send to user's personal controller endpoint to persist
        try{
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            const token = tokenMeta ? tokenMeta.getAttribute('content') : null;
            fetch('/settings/personal/theme', {
                method: 'POST',
                credentials: 'same-origin',
                headers: Object.assign({ 'Content-Type': 'application/json', 'Accept': 'application/json' }, token ? { 'X-CSRF-TOKEN': token } : {}),
                body: JSON.stringify({ theme })
            }).catch(()=>{});
        }catch(e){}
    });

    // make the visible switch clickable by toggling the hidden checkbox when its wrapper is clicked
    try{
        const wrapper = toggle.parentElement;
        if (wrapper){
            wrapper.style.cursor = 'pointer';
            wrapper.addEventListener('click', function(e){
                // ignore clicks directly on interactive elements
                if (e.target === toggle) return;
                toggle.checked = !toggle.checked;
                toggle.dispatchEvent(new Event('change', { bubbles: true }));
            });
        }
    }catch(e){}
})();
