(() => {
    let page = 0;
    let loading = false;
    let end = false;

    const originalPostId = queryPath[1];

    console.trace(queryString, originalPostId, queryPath[1], (originalPostId) ? true : false)

    // Load original post
    const originalPostSection = document.querySelector('section.reply');
    fetchPost(originalPostSection, originalPostId);

    const postSection = document.querySelector('section.posts');

    const loadPosts = async () => {
        if (loading || end) return;
        loading = true;

        params = {
            replyTo: originalPostId
        };
        if (queryParams.q) {
            params.query = queryParams.q;
        }

        end = await fetchPosts(postSection, page, params);
        page ++;
        loading = false;
    }

    loadPosts();

    window.addEventListener('scroll', () => {
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight) {
            loadPosts();
            console.log('Loading more posts...');
        }
    });

    /* Utility functions */
    const activateButton = (buttonList, buttonId) => {
        for (let i = 0; i < buttonList.length; i++) {
            if (i == buttonId) {
                buttonList[i].classList.add('active');
            } else {
                buttonList[i].classList.remove('active');
            }
        }
    }

    /* Post modal */
    const modal = document.querySelector('div.modal');
    const modalClose = document.querySelector('div.modal .header>button');
    const postButton = document.querySelector('button.add-post');

    // Open and close modal
    postButton.addEventListener('click', () => {
        modal.classList.add('show');
    });

    modalClose.addEventListener('click', () => {
        modal.classList.remove('show');
    });

    // Selection state
    let modalSelected = {
        text: '',
        tag: 0,
        medias: []
    };

    // Tag buttons
    const modalTagButtons = document.querySelectorAll('div.modal .tag-buttons>button');

    for (let i = 0; i < modalTagButtons.length; i++) {
        modalTagButtons[i].addEventListener('click', () => {
            activateButton(modalTagButtons, i);
            modalSelected.tag = i;
            saveModal();
        });
    }

    // Text input
    const modalTextInput = document.querySelector('div.modal textarea');

    modalTextInput.addEventListener('input', () => {
        modalSelected.text = modalTextInput.value;
        saveModal();
    });

    // Image upload
    const modalMediaContainer = document.querySelector('div.modal .image-upload');
    const modalMediaInput = document.querySelector('div.modal .image-upload button.add-image');

    const appendMediaButton = (ext, data) => {
        const mediaButton = document.createElement('button');

        let mediaContent = "";

        if (imageExtensions.includes(ext))
        {
            mediaContent = `<img src="${data}" />`;
        }
        else if (videoExtensions.includes(ext))
        {
            mediaContent = `<video src="${data}" autoplay muted loop />`;
        }
        else if (audioExtensions.includes(ext))
        {
            mediaContent = `<audio src="/${data}" controls />`;
        }

        mediaButton.innerHTML = mediaContent;
        modalMediaContainer.insertBefore(mediaButton, modalMediaContainer.firstChild);

        // Remove image from modal on click
        mediaButton.addEventListener('click', () => {
            modalMediaContainer.removeChild(mediaButton);
            modalSelected.medias = modalSelected.medias.filter((item) => {
                return item != {
                    ext: ext,
                    data: data
                };
            });

            saveModal();
        });
    };

    modalMediaInput.addEventListener('click', () => {

        // Create a file picker input and click it
        const filePicker = document.createElement('input');
        filePicker.type = 'file';
        filePicker.accept =
            'image/jpg, image/jpeg, image/png, image/gif, '+
            'video/mp4, video/webm, video/ogg, '+
            'audio/mp3, audio/wav';

        filePicker.addEventListener('change', () => {

            const file = filePicker.files[0];

            // Check if file exists
            if (!file) {
                return;
            }

            // Prevent too many medias (max 4)
            if (modalSelected.medias.length >= 4) {
                alert('Too many medias (max 4)');
                return;
            }

            // Convert to base64
            toBase64(file)
                .then((data) => {
                    // Check file length
                    if (data.length > 8000000) {
                        alert('File too large (max 8MB)');
                        return;
                    }

                    const ext = file.name.split('.').pop();

                    // Add medias to modal
                    appendMediaButton(ext, data);

                    // Add image to list of selected medias and save modal
                    modalSelected.medias.push({
                        ext: ext,
                        data: data
                    });

                    saveModal();
                });
        });

        filePicker.click();
    });

    /* Local storage */
    const loadModal = () => {

        const modalDraft = localStorage.getItem('modalDraft');

        if (modalDraft) {
            modalSelected = {
                medias: [],
                ...JSON.parse(modalDraft)
            };
            modalTextInput.value = modalSelected.text;
            activateButton(modalTagButtons, modalSelected.tag);
        }
    }

    const saveModal = () => {
        localStorage.setItem('modalDraft', JSON.stringify({
            text: modalSelected.text,
            tag: modalSelected.tag
        }));
    }

    loadModal();

    /* Post submission */
    const modalSubmit = document.querySelector('div.modal .modal-buttons>button');

    modalSubmit.addEventListener('click', () => {
        // Submit post

        console.log(modalSelected);
        
        POST('/posts', {
            tag: modalSelected.tag,
            content: modalSelected.text,
            medias: modalSelected.medias,
            replyTo: originalPostId
        })
            .then(response => {
                localStorage.removeItem('modalDraft');
                loadPage("post/" + response.payload.id);
            });
    });
})();