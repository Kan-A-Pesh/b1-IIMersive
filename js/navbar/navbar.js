document
    .querySelector("nav .menu")
    .addEventListener("click", () => {
        const sidebar = document.querySelector(".sidebar");
        sidebar.classList.toggle("openned");
    });
