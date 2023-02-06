const mainElement = document.querySelector('main');

/**
 * Load a app page
 * @param {string} page - Page name
 */
const loadPage = (page) => {

    // Load page styles (CSS)
    let pageStyle = document.getElementById('page-style');

    if (!pageStyle) {
        pageStyle = document.createElement('link');
        pageStyle.id = 'page-style';
        pageStyle.rel = 'stylesheet';
        document.head.appendChild(pageStyle);
    }

    pageStyle.href = `pages/${page}/style.css`;


    // Load page content (HTML)
    const xhr = new XMLHttpRequest();

    xhr.onload = () => {
        if (xhr.status === 200) {
            mainElement.innerHTML = xhr.responseText;
        } else {
            loadPage('notfound');
        }
    }

    xhr.open('GET', `pages/${page}/index.html`);
    xhr.send();

    // Remove previous page scripts (JS)
    const previousScript = document.getElementById('page-script');
    if (previousScript) {
        previousScript.remove();
    }

    // Load page scripts (JS)
    const script = document.createElement('script');
    script.src = `pages/${page}/script.js`;
    script.id = 'page-script';
    document.head.appendChild(script);

    // Update URL
    window.history.pushState({}, page, `/${page}`);

    // Close sidebar
    const sidebar = document.querySelector(".sidebar");
    sidebar.classList.remove("openned");
}

// Load from URL parameter
const urlParams = new URLSearchParams(window.location.search);
const page = urlParams.get('page');

if (page) {
    loadPage(page);
} else {
    loadPage('home');
}

const menuLinks = document.querySelectorAll('.sidebar .links a');

menuLinks.forEach((link) => {
    link.addEventListener('click', (event) => {
        event.preventDefault();

        const page = link.getAttribute('href').substring(1);
        loadPage(page);
    });
});