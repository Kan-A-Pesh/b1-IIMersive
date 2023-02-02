/**
 * Load theme style
 * @param {string} theme - Theme name
 */
const loadThemeStyle = (theme) => {
    let themeStyle = document.getElementById('theme-style');

    if (!themeStyle) {
        themeStyle = document.createElement('link');
        themeStyle.id = 'theme-style';
        themeStyle.rel = 'stylesheet';
        document.head.appendChild(themeStyle);
    }

    themeStyle.href = `css/themes/${theme}.css`;
}

/**
 * Set theme
 * @param {string} theme - Theme name
 */
const setTheme = (theme) => {
    localStorage.setItem('theme', theme);
    loadThemeStyle(theme);
}

// Load theme from local storage
loadThemeStyle(localStorage.getItem('theme') || 'dark');