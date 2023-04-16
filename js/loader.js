const mainElement = document.querySelector('main');
let queryPath = null;
let queryParams = null;

/**
 * Load a app page
 * @param {string} page - Page name
 */
const loadPage = (queryString) => {

    queryPath = queryString
        .split(/(&amp;|\?)/)[0]
        .split('/');

    queryParams = queryString
        .split(/(&amp;|\?|&)/)
        .slice(1)
        .reduce((acc, param) => {
            const [key, value] = param.split('=');
            acc[key] = value;
            return acc;
        }, {});

    const page = queryPath[0] || 'home';

    // Load page styles (CSS)
    let pageStyle = document.getElementById('page-style');

    if (!pageStyle) {
        pageStyle = document.createElement('link');
        pageStyle.id = 'page-style';
        pageStyle.rel = 'stylesheet';
        document.head.appendChild(pageStyle);
    }

    pageStyle.href = `/pages/${page}/style.css`;


    // Load page content (HTML)
    const xhr = new XMLHttpRequest();

    xhr.onload = () => {
        if (xhr.status === 200) {
            mainElement.innerHTML = xhr.responseText;

            // Load page scripts (JS)
            const script = document.createElement('script');
            script.src = `/pages/${page}/script.js`;
            script.id = 'page-script';
            document.head.appendChild(script);

            // Close sidebar
            const sidebar = document.querySelector(".sidebar");
            sidebar.classList.remove("openned");
        } else {
            loadPage('notfound');
        }
    }

    xhr.open('GET', `/pages/${page}/index.html`);

    // Remove previous page scripts (JS)
    const previousScript = document.getElementById('page-script');
    if (previousScript) {
        previousScript.remove();
    }

    xhr.send();

    // Update URL
    // queryString replace the first &amp; by a ? and the others by a &
    // replace the first only
    const queryEncoded = queryString.replace(/&amp;/, '?').replace(/&amp;/g, '&');
    window.history.pushState({}, queryString, `/${queryEncoded}`);
}

// Load from URL parameter
loadPage(queryString);
