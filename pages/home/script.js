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

/* Tag filter */
const tagButtons = document.querySelectorAll('section.posts>.tag-buttons>button');
const postContainer = document.querySelector('section.posts');

for (let i = 1; i < tagButtons.length; i++) {
    tagButtons[i].addEventListener('click', () => {
        applyTagFilter(i);
    });
}

tagButtons[0].addEventListener('click', () => {
    removeTagFilters();
});

const applyTagFilter = (tagId) => {
    for (let i = 0; i < tagButtons.length; i++) {
        if (i == tagId) {
            postContainer.style.setProperty('--filter-' + (i - 1), 'flex');
        } else {
            postContainer.style.setProperty('--filter-' + (i - 1), 'none');
        }
    }
    activateButton(tagButtons, tagId);
}

const removeTagFilters = () => {
    for (let i = 0; i < tagButtons.length; i++) {
        postContainer.style.setProperty('--filter-' + (i - 1), 'flex');
    }
    activateButton(tagButtons, 0);
}

removeTagFilters();


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
    images: []
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
const modalImageContainer = document.querySelector('div.modal .image-upload');
const modalImageInput = document.querySelector('div.modal .image-upload button.add-image');

const appendImageButton = (imageData) => {
    const imageButton = document.createElement('button');
    const imageImg = document.createElement('img');
    imageImg.src = imageData;
    imageButton.appendChild(imageImg);
    modalImageContainer.insertBefore(imageButton, modalImageContainer.firstChild);

    // Remove image from modal on click
    imageButton.addEventListener('click', () => {
        modalImageContainer.removeChild(imageButton);
        modalSelected.images = modalSelected.images.filter((item) => {
            return item != imageData;
        });

        saveModal();
    });
};

modalImageInput.addEventListener('click', () => {

    // Create a file picker input and click it
    const filePicker = document.createElement('input');
    filePicker.type = 'file';
    filePicker.accept = 'image/*';

    filePicker.addEventListener('change', () => {

        const file = filePicker.files[0];

        // Check if file exists
        if (!file) {
            return;
        }
        // Check if file is an image
        if (!file.type.startsWith('image/')) {
            return;
        }

        // Prevent too many images (max 4)
        if (modalSelected.images.length >= 4) {
            return;
        }

        const reader = new FileReader();
        reader.addEventListener('load', () => {

            // Add image to modal
            appendImageButton(reader.result);

            // Add image to list of selected images and save modal
            modalSelected.images.push(reader.result);
            saveModal();
        });

        reader.readAsDataURL(file);
    });

    filePicker.click();
});

/* Local storage */
const loadModal = () => {
    const modalDraft = localStorage.getItem('modalDraft');

    if (modalDraft) {
        modalSelected = JSON.parse(modalDraft);

        // Text
        modalTextInput.value = modalSelected.text;

        // Tag
        activateButton(modalTagButtons, modalSelected.tag);

        // Images
        for (let i = 0; i < modalSelected.images.length; i++) {
            appendImageButton(modalSelected.images[i]);
        }
    }
}

const saveModal = () => {
    localStorage.setItem('modalDraft', JSON.stringify(modalSelected));
}

loadModal();

/* Post submission */
const modalSubmit = document.querySelector('div.modal .modal-buttons>button');

modalSubmit.addEventListener('click', () => {
    // Submit post
    fetch('/api/post', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(modalSelected)
    }).then((response) => {
        return response.json();
    }).then((data) => {
        if (data.success) {
            // Clear modal draft
            localStorage.removeItem('modalDraft');

            // Go to post page
            window.location.href = '/post/' + data.postId;
        } else {
            alert("Error: " + data.error);
        }
    });
});