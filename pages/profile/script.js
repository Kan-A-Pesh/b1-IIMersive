(() => {
    const fetchPosts = (data) => {
        const posts = document.querySelector('.posts')

        fetch(
            '/api/posts',
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            }
        )
            .then(res => res.json())
            .then(data => {
                posts.innerHTML = ''

                data.forEach(post => {
                    posts.innerHTML += `
                    <article class="card bg-linear feed-post tag-0">
                        <div class="header">
                            <img src="/img/defaults/profile_pic.png" alt="Image de profil">
                            <div class="info">
                                <div class="names">
                                    <h3 class="opacity-9">Kan-à-Pesh</h3>
                                    <p class="opacity-5">@kanapesh • 4h</p>
                                </div>
                                <div class="tag-buttons">
                                    <button style="--id: 0">
                                        <p>Informatique</p>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="content">
                            <p>Voici ma nouvelle photo de profil!</p>
                            <img src="/img/defaults/profile_pic.png" alt="Image de l'article">
                        </div>
                        <div class="footer">
                            <div class="likes">
                                <img class="icons" src="/img/icons/heart.svg" alt="🤍">
                                <p>0</p>
                            </div>
                            <div class="comments">
                                <img class="icons" src="/img/icons/message-square.svg" alt="💬">
                                <p>0</p>
                            </div>
                            <div class="views">
                                <img class="icons" src="/img/icons/eye.svg" alt="👁️">
                                <p>0</p>
                            </div>
                            <div class="delete color-secondary hidden">
                                <img src="/img/icons/trash.svg" alt="🗑️">
                            </div>
                        </div>
                    </article>
                    `;
                });
            })
    }


    const tabs = document.querySelectorAll('.tabs button')

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(tab => tab.classList.remove('active'))
            tab.classList.add('active')

            if (tab.getAttribute('data-tab') === 'posts') {
                fetchPosts({
                    fromUser: "kanapesh",
                    replyTo: "none"
                })
            } else if (tab.getAttribute('data-tab') === 'replies') {
                fetchPosts({
                    fromUser: "kanapesh",
                    replyTo: "*"
                })
            } else if (tab.getAttribute('data-tab') === 'media') {
                fetchPosts({
                    fromUser: "kanapesh",
                    replyTo: "none",
                    hasMedia: true,
                })
            }
        });
    });

    // TODO: Fetch posts
})()