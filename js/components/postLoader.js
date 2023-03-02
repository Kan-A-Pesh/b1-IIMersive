const tags = [
    "Informatique",
    "Téléphonie",
    "Électronique",
    "Jeux vidéo",
    "Réalité virtuelle",
    "Intelligence artificielle",
    "Domotique",
    "Objets connectés",
    "Technologie",
    "Innovation"
]

const appendPost = (section, post, author, liked) => {

    let mediaHTML = '';
    if (post.media_paths) {
        post.media_paths.forEach(media => {
            mediaHTML += parseMedia(media, '');
        });
    }

    let trashButtonHTML = '';
    if (author.handle == USER_HANDLE) {
        trashButtonHTML = `
            <div class="delete color-secondary">
                <img src="/img/icons/trash.svg" alt="🗑️">
            </div>
        `;
    }

    let likeButtonHTML = '';
    if (liked != null) {
        likeButtonHTML = `<div class="likes ${liked ? 'unlike' : 'like'}">
            <img class="icons" src="/img/icons/heart${liked ? '-filled' : ''}.svg" alt="🤍">
            <p>${post.likes}</p>
        </div>`;
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
                        <p class="opacity-5">@${author.handle} • ${toRelativeTime(parseDate(post.created_at))}</p>
                    </div>
                </a>
                <div class="tag-buttons">
                    <button style="--id: ${post.tag}">
                        <p>${tags[post.tag]}</p>
                    </button>
                </div>
            </div>
        </div>
        <div class="content">
            <p>${
                parseText(post.content)
            }</p>
            ${mediaHTML}
        </div>
        <div class="footer">
            ${likeButtonHTML}
            <div class="comments">
                <img class="icons" src="/img/icons/message-square.svg" alt="💬">
                <p>${post.comments}</p>
            </div>
            <div class="views">
                <img class="icons" src="/img/icons/eye.svg" alt="👁️">
                <p>${post.views}</p>
            </div>
            ${trashButtonHTML}
        </div>
    `;

    const profileLike = article.querySelector('a:not(.ext)');
    profileLike.addEventListener('click', (e) => {
        e.preventDefault();
        loadPage(`profile/${author.handle}`);
    });

    if (liked != null) {
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
    }

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

    section.appendChild(article);
};

const fetchPostMetadata = async (post) => {

    const [authorRequest, likedRequest] = await Promise.all([
        GET(`/users/${post.author_handle}`),
        USER_HANDLE != null ? GET(`/posts/${post.id}/like`) : null
    ]);
    
    const author = authorRequest?.payload ?? null;
    const liked = likedRequest?.payload ?? null;

    return [author, liked];
}


/**
 * Loads posts from the API and appends them to the page
 * 
 * @param {HTMLElement} container The container to append the posts to
 * @param {number} page The page number to load
 * @param {Object} additionnalParams Additionnal parameters to pass to the API
 * @returns {Promise<boolean>} Whether the end of the posts has been reached
 */
const fetchPosts = async (container, page, additionnalParams = {}) => {
    let end = false;

    params = {
        limit: 25,
        offset: page * 25,
        ...additionnalParams
    }

    const posts = await GET('/posts', params);

    // Fetch all posts together to avoid waiting for each request
    const postRequests = await Promise.all(posts.payload.map(async (post) => {
        const [author, liked] = await fetchPostMetadata(post);
        return [post, author, liked];
    }));

    postRequests.forEach(([post, author, liked]) => {
        appendPost(container, post, author, liked);
    });

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

            container.appendChild(endMessage);
    }

    return end;
};