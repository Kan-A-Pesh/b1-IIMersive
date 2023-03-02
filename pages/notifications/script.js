(() => {
    // Elements
    const main = document.querySelector('main');

    // Append notification
    const appendNotification = (notification) => {
        // Create notification
        const notificationHTML = `
        <section class="card bg-linear">
            <img src="${notification.image}" alt="Image de notification" height="48px" width="48px">
            <p>${notification.content}</p>
        </section>`;

        // Append notification
        main.insertAdjacentHTML('afterbegin', notificationHTML);
    };

    // Fetch notifications
    GET('/notifications')
        .then(response => {
            if (response.payload.length === 0)
            {
                main.innerHTML = `<p class="end-message">Vous n'avez aucune notification.</p>`;
                return;
            }

            main.innerHTML = '';
            response.payload.forEach(notificationData => {
                
                const notification = {
                    image: parseMedia(notificationData.image, '/img/defaults/profile_pic.png', urlOnly=true),
                    content: parseText(notificationData.content),
                };

                appendNotification(notification);
            });
        });

})();