/**
 * Antigravity Theme Engine v1.0
 * Handles theme switching, persistence, and system preferences
 */

(function() {
    const THEME_KEY = 'theme';
    const HTML = document.documentElement;

    /**
     * Apply the theme to the document
     * @param {string} theme - 'light' or 'dark'
     */
    function applyTheme(theme) {
        if (theme === 'dark') {
            HTML.classList.add('dark');
            HTML.setAttribute('data-bs-theme', 'dark'); // Maintain Bootstrap 5 compatibility
        } else {
            HTML.classList.remove('dark');
            HTML.setAttribute('data-bs-theme', 'light');
        }
        
        localStorage.setItem(THEME_KEY, theme);
        updateToggleIcons(theme);
    }

    /**
     * Update toggle icons based on current theme
     * @param {string} theme 
     */
    function updateToggleIcons(theme) {
        const icons = document.querySelectorAll('#theme-icon');
        icons.forEach(icon => {
            if (theme === 'dark') {
                icon.className = 'bi bi-moon-stars-fill';
            } else {
                icon.className = 'bi bi-sun-fill';
            }
        });
    }

    /**
     * Initialize theme on load
     */
    function initTheme() {
        const storedTheme = localStorage.getItem(THEME_KEY);
        const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        const initialTheme = storedTheme || systemTheme;
        
        applyTheme(initialTheme);
    }

    // Run initialization
    initTheme();

    // Event listener for toggle buttons
    document.addEventListener('DOMContentLoaded', () => {
        const toggleButtons = document.querySelectorAll('#theme-toggle');
        
        toggleButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const currentTheme = HTML.classList.contains('dark') ? 'dark' : 'light';
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                applyTheme(newTheme);
            });
        });
    });

    // Listen for system theme changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
        if (!localStorage.getItem(THEME_KEY)) {
            applyTheme(e.matches ? 'dark' : 'light');
        }
    });
})();
