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