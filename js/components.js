const loadComponent = (element, component) => {
    fetch(`${component}.html`)
        .then((response) => response.text())
        .then((html) => {
            element.outerHTML = html;
        });
};

document.querySelectorAll("[data-dynamic]").forEach((element) => {
    loadComponent(element, element.dataset.dynamic);
});