(() => {

    // PROFILE HEADER
    let profile_handle = queryPath[1] || USER_HANDLE || null;
    
    if (profile_handle === null) {
        window.location.href = '/notfound';
        return;
    }

    GET('/users/' + profile_handle)
        .then(async (response) => {
            const user = response.payload;
            
            const avatarImg = document.querySelector('main>section.header>img');
            const bannerImg = document.querySelector('main>div.banner>img');
            const biographyText = document.querySelector('main>section.biography>p');
            const nameText = document.querySelector('main>section.header>h3');
            const handleText = document.querySelector('main>section.header>p');

            avatarImg.src = parseMedia(user.avatar_path, '/img/defaults/profile_pic.png', urlOnly=true);
            bannerImg.src = parseMedia(user.banner_path, '/img/defaults/banner.jpg', urlOnly=true);
            biographyText.innerHTML = parseText(user.biography);
            nameText.innerText = user.display_name;
            handleText.innerText = '@' + user.handle;
        });

    // PROFILE POSTS
    let end = false;
    let loading = false;
    let page = 0;
    let lastPostParams = {};
    const posts = document.querySelector('.posts');

    const loadPosts = async (params) => {
        if (end || loading) return;
        loading = true;
        lastPostParams = params;

        end = await fetchPosts(posts, page, params);

        page ++;
        loading = false;
    };

    const tabs = document.querySelectorAll('.tabs button');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            if (loading) return;

            end = false;
            page = 0;
            posts.innerHTML = '';

            tabs.forEach(tab => tab.classList.remove('active'))
            tab.classList.add('active')

            if (tab.getAttribute('data-tab') === 'posts') {
                loadPosts({
                    fromUser: profile_handle,
                    replyTo: "none"
                });
                
            } else if (tab.getAttribute('data-tab') === 'replies') {
                loadPosts({
                    fromUser: profile_handle,
                    replyTo: "any"
                });

            } else if (tab.getAttribute('data-tab') === 'media') {
                loadPosts({
                    fromUser: profile_handle,
                    hasMedia: true
                });

            } else if (tab.getAttribute('data-tab') === 'likes') {
                loadPosts({
                    liked: true
                });
            }
        });
    });

    loadPosts({
        fromUser: profile_handle,
        replyTo: "none"
    });
})()