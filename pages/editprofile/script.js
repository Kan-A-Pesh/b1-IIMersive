(() => {

    if (USER_HANDLE === null) {
        window.location.href = '/login';
        return;
    }

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
        PUT("/users/" + USER_HANDLE, {
            name: usernameInput.value,
            biography: bioInput.value,
            avatar: profileImg.src.includes("data:image") ? {
                extension: profileImg.src.split(";base64,")[0].split("/").pop(),
                data: profileImg.src
            } : null,
            banner: bannerImg.src.includes("data:image") ? {
                extension: bannerImg.src.split(";base64,")[0].split("/").pop(),
                data: bannerImg.src
            } : null
        })
            .then(_ => {
                // Redirect to profile page
                window.location.href = `/profile/${USER_HANDLE}`;
            })
            .catch(error => {
                if (error.code === 409) {
                    alert("Username already taken");
                    return;
                }

                if (error.code === 429) {
                    alert("Too many requests! Calm down!");
                    return;
                }

                alert("Something went wrong (code: " + error.code + ")");
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
            toBase64(file)
                .then((data) => {
                    // Check file length
                    if (data.length > 8000000) {
                        alert('File too large (max 8MB)');
                        return;
                    }

                    // Update image
                    profileImg.src = data;
                    
                    // Show modal
                    showModal();
                });
        });

        fileInput.click();
    });

    // Delete profile image
    profileDelete.addEventListener("click", () => {
        // Set image to default
        profileImg.src = "/img/defaults/profile_pic.png";

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
            toBase64(file)
                .then((data) => {
                    // Check file length
                    if (data.length > 8000000) {
                        alert('File too large (max 8MB)');
                        return;
                    }

                    // Update image
                    bannerImg.src = data;
                    
                    // Show modal
                    showModal();
                });
        });

        fileInput.click();
    });

    // Delete banner image
    bannerDelete.addEventListener("click", () => {
        // Set image to default
        bannerImg.src = "/img/defaults/banner.jpg";

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
    GET('/users/' + USER_HANDLE)
        .then(async (response) => {
            const user = response.payload;
            
            profileImg.src = parseMedia(user.avatar_path, '/img/defaults/profile_pic.png', urlOnly=true);
            bannerImg.src = parseMedia(user.banner_path, '/img/defaults/banner.jpg', urlOnly=true);
            usernameInput.value = user.display_name;
            usernameInput.placeholder = user.display_name;
            bioInput.value = user.biography;
        });
})();