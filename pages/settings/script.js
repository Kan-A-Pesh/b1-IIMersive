// Get elements
const themeLight = document.querySelector("#theme-light");
const themeDark = document.querySelector("#theme-dark");
const themeBlack = document.querySelector("#theme-black");
const themeSystem = document.querySelector("#theme-system");

const modal = document.querySelector("#modal");
const modalSave = document.querySelector("#modal-save");
const modalDismiss = document.querySelector("#modal-dismiss");

// Show modal
const showModal = () => {
    modal.classList.add("show");
};

modalSave.addEventListener("click", () => {
    // Save theme
    if (themeLight.checked) {
        localStorage.setItem("theme", "light");
    } else if (themeDark.checked) {
        localStorage.setItem("theme", "dark");
    } else if (themeBlack.checked) {
        localStorage.setItem("theme", "black");
    } else if (themeSystem.checked) {
        localStorage.setItem("theme", "system");
    }

    // Dismiss modal
    modal.classList.remove("show");
});

modalDismiss.addEventListener("click", () => {
    // Dismiss changes (reload page)
    window.location.reload();
});

// Register theme change
const registerThemeChange = (theme) => {
    // Set theme (using theme.js)
    loadThemeStyle(theme);

    // Show modal
    showModal();
};

themeLight.addEventListener("change", () => {
    registerThemeChange("light");
});

themeDark.addEventListener("change", () => {
    registerThemeChange("dark");
});

themeBlack.addEventListener("change", () => {
    registerThemeChange("black");
});

themeSystem.addEventListener("change", () => {
    registerThemeChange("system");
});

// Load theme
const loadedTheme = localStorage.getItem("theme") || "dark";

switch (loadedTheme) {
    case "light":
        themeLight.checked = true;
        break;
    case "dark":
        themeDark.checked = true;
        break;
    case "black":
        themeBlack.checked = true;
        break;
    case "system":
        themeSystem.checked = true;
        break;
}