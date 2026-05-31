// Initialize language from localStorage on page load
(function() {
    // Check if localStorage has a saved language preference
    const savedLocale = localStorage.getItem('app-language');
    const currentLang = document.documentElement.lang;
    
    // Update HTML lang attribute if different from localStorage
    if (savedLocale && savedLocale !== currentLang) {
        // The page was already rendered in the wrong language
        // We'll need to reload to apply the correct language
        // This prevents the flash of wrong language
        const desiredLocale = savedLocale === 'vi' ? 'vi' : 'en';
        if (currentLang !== desiredLocale) {
            // Silently reload without showing the loader, as Laravel will handle the session
            window.location.href = window.location.href;
        }
    } else if (savedLocale) {
        // Language matches, good to go
        document.documentElement.lang = savedLocale;
    } else {
        // First visit - save current language
        const currentLocale = document.documentElement.lang || 'vi';
        localStorage.setItem('app-language', currentLocale);
    }
    
    // Also set the lang attribute if not already set
    if (!document.documentElement.lang) {
        document.documentElement.lang = localStorage.getItem('app-language') || 'vi';
    }
})();
