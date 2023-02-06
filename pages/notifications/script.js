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
fetch('/api/notifications')
    .then(response => response.json())
    .then(data => {

        if (data.error) {
            alert(data.error);
            return;
        }

        // Create notifications
        data.forEach(notification => {
            appendNotification(notification);
        });
    });

// Test
// TODO: Remove this when backend is done
appendNotification({
    image: 'img/defaults/profile_pic.png',
    content: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec auctor, nisl eget ultricies lacinia, nisl nisl aliquet nisl, eget aliquet nunc nisl eget nisl. Donec auctor, nisl eget ultricies lacinia, nisl nisl aliquet nisl, eget aliquet nunc nisl eget nisl.'
});

setTimeout(() => {
    appendNotification({
        image: 'img/defaults/profile_pic.png',
        content: 'Another notification'
    });
}, 3000);