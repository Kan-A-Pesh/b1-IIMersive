(() => {
    let page = 0;
    let loading = false;
    let end = false;

    const loadPosts = async () => {
        if (loading || end) {
            return;
        }
        loading = true;

        params = {
            limit: 25,
            offset: page * 25
        }

        if (queryParams.q) {
            params.query = queryParams.q;
        }

        const posts = await GET('/posts', params);

        for (let i = 0; i < posts.payload.length; i++) {
            const post = posts.payload[i];
            const authorRequest = await GET(`/users/${post.author_handle}`);
            const author = authorRequest.payload;

            const likedRequest = await GET(`/posts/${post.id}/like`);
            const liked = likedRequest.payload;
            appendPost(post, author, liked);
        }
        

        if (posts.payload.length < 25) {
            end = true;

            const endMessage = document.createElement('p');
            endMessage.classList.add('end-message');

            if (page > 15)
                endMessage.innerText = 'Vous avez beaucoup de temps libre, n\'est-ce pas ?';
            else if (page > 5)
                endMessage.innerText = 'Vous avez fini de lire tout ce que nous avions à vous proposer !';
            else if (page > 1)
                endMessage.innerText = 'Il n\'y a pas grand chose à lire ici, mais vous pouvez toujours essayer de poster quelque chose !';
            else if (posts.payload.length == 0)
                endMessage.innerText = 'Oups... C\'est vide ici !';
            else
                endMessage.innerText = 'C\'est déjà fini ?';

            postSection.appendChild(endMessage);
        }

        page++;
        loading = false;
    };

    const postSection = document.querySelector('section.posts');

    const appendPost = (post, author, liked) => {

        let mediaHTML = '';
        if (post.media_paths) {
            post.media_paths.forEach(media => {
                mediaHTML += parseMedia(media, '');
            });
        }

        let trashButton = '';
        if (author.id == USER_HANDLE) {
            trashButton = `
                <div class="delete color-secondary">
                    <img src="/img/icons/trash.svg" alt="🗑️">
                </div>
            `;
        }

        const article = document.createElement('article');
        article.classList.add('card', 'bg-linear', 'feed-post', `tag-${post.tag}`);

        article.innerHTML = `
            <div class="header">
                ${parseMedia(author.avatar_path, '<img src="/img/defaults/profile_pic.png"/>')}
                <div class="info">
                    <a href="/profile/${author.handle}">
                        <div class="names">
                            <h3 class="opacity-9">${author.display_name}</h3>
                            <p class="opacity-5">@${author.handle} • ${toRelativeTime(post.created_at)}</p>
                        </div>
                    </a>
                    <div class="tag-buttons">
                        <button style="--id: ${post.tag}">
                            <p>Informatique</p>
                        </button>
                    </div>
                </div>
            </div>
            <div class="content">
                <p>${
                    post.content
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/&/g, '&amp;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#039;')
                        .replace(/(https?:\/\/[^\s]+)/g, '<a class="ext" href="$1">$1</a>')
                        .replace(/@([a-zA-Z0-9_]+)/g, '<a href="/profile/$1">@$1</a>')
                        .replace(/#([a-zA-Z0-9_]+)/g, '<a href="/home?q=%23$1">#$1</a>')
                }</p>
                ${mediaHTML}
            </div>
            <div class="footer">
                <div class="likes ${liked ? 'unlike' : 'like'}">
                    <img class="icons" src="/img/icons/heart${liked ? '-filled' : ''}.svg" alt="🤍">
                    <p>${post.likes}</p>
                </div>
                <div class="comments">
                    <img class="icons" src="/img/icons/message-square.svg" alt="💬">
                    <p>${post.comments}</p>
                </div>
                <div class="views">
                    <img class="icons" src="/img/icons/eye.svg" alt="👁️">
                    <p>${post.views}</p>
                </div>
                ${trashButton}
            </div>
        `;

        const profileLike = article.querySelector('a:not(.ext)');
        profileLike.addEventListener('click', (e) => {
            e.preventDefault();
            loadPage(`profile/${author.handle}`);
        });

        const likeButton = article.querySelector('.likes');
        likeButton.addEventListener('click', async () => {
            const likeText = likeButton.querySelector('p');

            if (likeButton.classList.contains('like')) {
                await POST(`/posts/${post.id}/like`);
                likeButton.classList.remove('like');
                likeButton.classList.add('unlike');
                likeButton.querySelector('img').src = '/img/icons/heart-filled.svg';
                likeText.textContent = parseInt(likeText.textContent) + 1;
            } else {
                await DELETE(`/posts/${post.id}/like`);
                likeButton.classList.remove('unlike');
                likeButton.classList.add('like');
                likeButton.querySelector('img').src = '/img/icons/heart.svg';
                likeText.textContent = parseInt(likeText.textContent) - 1;
            }
        });

        const commentButton = article.querySelector('.comments');
        commentButton.addEventListener('click', () => {
            loadPage(`post/${post.id}`);
        });

        const deleteButton = article.querySelector('.delete');
        if (deleteButton) {
            deleteButton.addEventListener('click', async () => {
                if (confirm('Are you sure you want to delete this post?')) {
                    await DELETE(`/posts/${post.id}`);
                    article.remove();
                }
            });
        }

        postSection.appendChild(article);
    };

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
})();