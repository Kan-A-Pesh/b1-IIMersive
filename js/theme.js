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

const setTheme = (theme) => {
    localStorage.setItem('theme', theme);
    loadThemeStyle(theme);
}

loadThemeStyle(localStorage.getItem('theme') || 'dark');