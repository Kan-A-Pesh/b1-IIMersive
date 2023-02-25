// Toggle sidebar when menu icon is clicked
document
    .querySelector("nav .menu")
    .addEventListener("click", () => {
        const sidebar = document.querySelector(".sidebar");
        sidebar.classList.toggle("openned");
    });


// Logout
document
    .querySelector("#logout-link")
    .addEventListener("click", () => {
        document.cookie = "session_id=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;"
        window.location.href = "/login";
    });

// Search
document
    .querySelector("nav #search-submit")
    .addEventListener("click", () => {
        const search = document.querySelector("nav #search-input").value;
        loadPage(`home?q=${search}`);
    });

// Sidebar links
const menuLinks = document.querySelectorAll('.sidebar a');

menuLinks.forEach((link) => {
    link.addEventListener('click', (event) => {
        event.preventDefault();

        const page = link.getAttribute('href').substring(1);
        loadPage(page);
    });
});