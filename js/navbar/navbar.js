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
        localStorage.removeItem("user_handle");
        sessionStorage.removeItem("user_handle");
        document.cookie = "session_id=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;"
        window.location.href = "/login";
    });

// Search
document
    .querySelector("nav #search-submit")
    .addEventListener("click", () => {
        const search = document.querySelector("nav #search-input").value;
        
        if (search === "")
            loadPage("home");
        else
            loadPage(`home?q=${search}`);
    });

// Sidebar links
const menuLinks = document.querySelectorAll('.sidebar a:not(.ext)');

menuLinks.forEach((link) => {
    link.addEventListener('click', (event) => {
        event.preventDefault();

        const page = link.getAttribute('href').substring(1);
        loadPage(page);
    });
});

// Sidebar profile
if (USER_HANDLE)
{
    document.querySelectorAll('section.sidebar .log-hidden').forEach((element) => {
        element.style.display = 'none';
    });

    GET('/users/' + USER_HANDLE)
        .then(async (response) => {
            const user = response.payload;
            
            const avatarImg = document.querySelector('section.sidebar>div.profile-card>img');
            const nameText = document.querySelector('section.sidebar>div.profile-card h2');

            avatarImg.src = parseMedia(user.avatar_path, '/img/defaults/profile_pic.png', urlOnly=true);
            nameText.innerText = user.display_name;
        });
}
else
{
    document.querySelectorAll('section.sidebar .anon-hidden').forEach((element) => {
        element.style.display = 'none';
    });
}