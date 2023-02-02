// Toggle sidebar when menu icon is clicked
document
    .querySelector("nav .menu")
    .addEventListener("click", () => {
        const sidebar = document.querySelector(".sidebar");
        sidebar.classList.toggle("openned");
    });
