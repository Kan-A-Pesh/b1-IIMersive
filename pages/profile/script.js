(() => {
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
                    fromUser: "Kan_A_Pesh",
                    replyTo: "none"
                });
                
            } else if (tab.getAttribute('data-tab') === 'replies') {
                loadPosts({
                    fromUser: "Kan_A_Pesh",
                    replyTo: "any"
                });

            } else if (tab.getAttribute('data-tab') === 'media') {
                loadPosts({
                    fromUser: "Kan_A_Pesh",
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
        fromUser: "Kan_A_Pesh",
        replyTo: "none"
    });
})()