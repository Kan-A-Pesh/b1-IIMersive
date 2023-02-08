(() => {
    const messageList = document.querySelector(".message-list");
    const messageInput = document.querySelector(".chat-container input");
    const messageSubmit = document.querySelector(".chat-container button");
    const messageUsers = document.querySelector(".user-list");

    // Get read messages from local storage
    let readMessages = localStorage.getItem("messageRead") || "[]";
    readMessages = JSON.parse(readMessages);

    /**
     * Add a message to the read messages list
     * @param {string} messageId - The message id
     */
    const addReadMessage = (messageId) => {
        if (!readMessages.includes(messageId)) {
            readMessages.push(messageId);
            localStorage.setItem("messageRead", JSON.stringify(readMessages));
        }
    };

    /**
     * Load messages from the server and display them
     * @param {string} handle - The user handle
     */
    const loadMessages = (handle) => {
        // TODO: Get messages from server
        const messages = [
            {
                "id": "4d461733-161f-4778-87fd-d8aa3aeafd7c",
                "content": "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                "date": "2019-01-01T00:00:00.000Z",
                "from": {
                    "handle": "janedoe",
                    "name": "Jane Doe",
                    "avatar": "/img/defaults/profile_pic.png"
                }
            },
            {
                "id": "4d461733-161f-4778-87fd-d8aa3aeafd7c",
                "content": "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                "date": "2019-01-01T00:00:00.000Z",
                "from": {
                    "handle": "janedoe",
                    "name": "Jane Doe",
                    "avatar": "/img/defaults/profile_pic.png"
                }
            }
        ];

        clearMessagesHTML();
        messages.forEach((message) => {
            addMessageHTML(message);
        });
    };

    /**
     * Add a message to the message list
     * @param {Object} message - The message content
     * @param {string} message.id - The message id
     * @param {string} message.content - The message content
     * @param {string} message.date - ISO date
     * @param {Object} message.from - The user who sent the message
     * @param {string} message.from.handle - The user handle
     * @param {string} message.from.name - The user name
     * @param {string} message.from.avatar - The user avatar
     */
    const addMessageHTML = (message) => {
        const messageHTML = `
        <article class="message">
            <img src="${message.from.avatar}" alt="Image de profil" height="40px" />
            <div class="message-content">
                <h3>${message.from.name}</h3>
                <p>${message.content}</p>
            </div>
        </article>
        `;

        messageList.innerHTML += messageHTML;
    };

    /**
     * Clear the message list
     */
    const clearMessagesHTML = () => {
        messageList.innerHTML = "";
    }

    /**
     * Send a message to the server
     */
    const sendMessage = () => {
        const message = messageInput.value;

        if (message) {
            // TODO: Send message to server
            const user = {
                name: "John Doe",
                avatarUrl: "/img/defaults/profile_pic.png",
            };

            addMessageHTML(user, message);
        }
    }

    // Send message on enter key
    messageInput.addEventListener("keyup", (e) => {
        if (e.key === "Enter") {
            sendMessage();
        }
    });

    // Send message on button click
    messageSubmit.addEventListener("click", sendMessage);


    /**
     * Add a user to the user list
     * @param {Object} lastMsg - The last message sent by the user
     * @param {string} lastMsg.handle - The user handle
     * @param {string} lastMsg.name - The user name
     * @param {string} lastMsg.avatar - The user avatar
     * @param {string} lastMsg.lastMessageId - The last message id
     * @param {string} lastMsg.lastMessageContent - The last message content 
     * @param {string} lastMsg.lastMessageDate - ISO date
     */
    const addUserHTML = (lastMsg) => {
        messageRead = !readMessages.includes(lastMsg.lastMessageId);

        const userElement = document.createElement("div");
        userElement.classList.add("user-card");
        userElement.classList.add("bg-linear");

        userElement.innerHTML = `
            <img src="${lastMsg.avatar}" alt="Image de profil" height="48px" />
            <div class="user-info">
                <h3>${lastMsg.name}</h3>
                ${lastMsg.lastMessageContent
                ? `<p>${lastMsg.lastMessageContent.length > 15 ?
                    lastMsg.lastMessageContent.substring(0, 15) + "..." :
                    lastMsg.lastMessageContent
                }</p>`
                : ""}
            </div>
            ${messageRead
                ? `<div class="notification-circle"></div>`
                : ""}`;

        userElement.addEventListener("click", () => {
            loadMessages(lastMsg.handle);
            addReadMessage(lastMsg.lastMessageId);

            userElement.querySelector(".notification-circle")?.remove();
        });

        messageUsers.appendChild(userElement);
    };

    /**
     * Clear the user list
     */
    const clearUsersHTML = () => {
        messageUsers.innerHTML = "";
    }

    // TODO: Get users from server
    const lastUserMessages = [
        {
            handle: "janedoe",
            name: "Jane Doe",
            avatar: "/img/defaults/profile_pic.png",
            lastMessageId: "4d461733-161f-4778-87fd-d8aa3aeafd7c",
            lastMessageContent: "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
            lastMessageDate: "2019-01-01T00:00:00.000Z"
        },
        {
            handle: "janedoe",
            name: "Jane Doe",
            avatar: "/img/defaults/profile_pic.png",
            lastMessageId: "4d461733-161f-4778-87fd-d8aa3aeafd7c",
            lastMessageContent: "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
            lastMessageDate: "2019-01-01T00:00:00.000Z"
        }
    ];

    clearUsersHTML();
    lastUserMessages.forEach(lastUserMessage => {
        addUserHTML(lastUserMessage);
    });

})()