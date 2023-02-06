// Get elements
const profileImg = document.querySelector("#profile-img");
const profileUpload = document.querySelector("#profile-upload");
const profileDelete = document.querySelector("#profile-delete");

const bannerImg = document.querySelector("#banner-img");
const bannerUpload = document.querySelector("#banner-upload");
const bannerDelete = document.querySelector("#banner-delete");

const usernameInput = document.querySelector("#username-input");
const bioInput = document.querySelector("#bio-input");

const modal = document.querySelector("#modal");
const modalSave = document.querySelector("#modal-save");
const modalDismiss = document.querySelector("#modal-dismiss");

// Show modal
const showModal = () => {
    modal.classList.add("show");
};

modalSave.addEventListener("click", () => {
    // Save changes
    fetch("/api/user/:handle", {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            username: usernameInput.value,
            bio: bioInput.value,
            avatar: profileImg.src,
            banner: bannerImg.src
        }),
    })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            // Redirect to profile page
            window.location.href = `/user/${data.handle}`;
        });
});

modalDismiss.addEventListener("click", () => {
    // Dismiss changes (reload page)
    window.location.reload();
});

// Upload profile image
profileUpload.addEventListener("click", () => {
    // Create file input and click it
    const fileInput = document.createElement("input");
    fileInput.type = "file";
    fileInput.accept = "image/*";

    fileInput.addEventListener("change", () => {
        // Get file
        const file = fileInput.files[0];

        // Apply file to image
        const reader = new FileReader();
        reader.onload = () => {
            profileImg.src = reader.result;
        };
        reader.readAsDataURL(file);

        // Show modal
        showModal();
    });

    fileInput.click();
});

// Delete profile image
profileDelete.addEventListener("click", () => {
    // Set image to default
    profileImg.src = "img/defaults/profile_pic.png";

    // Show modal
    showModal();
});

// Upload banner image
bannerUpload.addEventListener("click", () => {
    // Create file input and click it
    const fileInput = document.createElement("input");
    fileInput.type = "file";
    fileInput.accept = "image/*";

    fileInput.addEventListener("change", () => {
        // Get file
        const file = fileInput.files[0];

        // Apply file to image
        const reader = new FileReader();
        reader.onload = () => {
            bannerImg.src = reader.result;
        };
        reader.readAsDataURL(file);

        // Show modal
        showModal();
    });

    fileInput.click();
});

// Delete banner image
bannerDelete.addEventListener("click", () => {
    // Set image to default
    bannerImg.src = "img/defaults/banner.png";

    // Show modal
    showModal();
});

usernameInput.addEventListener("input", () => {
    // Show modal
    showModal();
});

bioInput.addEventListener("input", () => {
    // Show modal
    showModal();
});


// Load profile data
fetch("/api/user/:handle")
    .then(response => response.json())
    .then(data => {

        if (data.error) {
            alert(data.error);
            return;
        }

        // Set profile data
        profileImg.src = data.avatar;
        bannerImg.src = data.banner;
        usernameInput.value = data.username;
        bioInput.value = data.bio;
    });
