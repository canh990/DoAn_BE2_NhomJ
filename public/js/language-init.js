// Initialize language from localStorage on page load
(function() {
    // Check if localStorage has a saved language preference
    const savedLocale = localStorage.getItem('app-language');
    const currentLang = document.documentElement.lang || 'vi';
    
    // Sync localStorage with the language rendered by the server
    if (savedLocale && savedLocale !== currentLang) {
        localStorage.setItem('app-language', currentLang);
    } else if (!savedLocale) {
        // First visit - save current language
        localStorage.setItem('app-language', currentLang);
    }
    
    // Also set the lang attribute if not already set
    if (!document.documentElement.lang) {
        document.documentElement.lang = currentLang;
    }
})();
