/**
 * Load theme style
 * @param {string} theme - Theme name
 */
const loadThemeStyle = (theme) => {
    if (theme === "system") {
        // Get system theme
        theme = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
    }

    let themeStyle = document.getElementById('theme-style');

    if (!themeStyle) {
        themeStyle = document.createElement('link');
        themeStyle.id = 'theme-style';
        themeStyle.rel = 'stylesheet';
        document.head.appendChild(themeStyle);
    }

    themeStyle.href = `/css/themes/${theme}.css`;
}

// Load theme from local storage
loadThemeStyle(localStorage.getItem('theme') || 'dark');