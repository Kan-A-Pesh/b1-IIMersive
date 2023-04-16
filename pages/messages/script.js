(() => {
    const chatContainer = document.querySelector(".chat-container");
    const backButton = document.querySelector(".back-button");

    const messageList = document.querySelector(".message-list");
    const messageInput = document.querySelector(".chat-container input");
    const messageSubmit = document.querySelector(".chat-container button");
    const messageUsers = document.querySelector(".user-list");

    let opennedChat = null;

    backButton.addEventListener("click", () => {
        chatContainer.classList.add("hidden");
    });

    /**
     * Load messages from the server and display them
     * @param {string} handle - The user handle
     */
    const loadMessages = (handle) => {
        opennedChat = handle;

        GET(`/messages/${handle}`)
            .then(async (response) => {
                const messages = response.payload;
                const messageAuthors = messages.map((message) => message.author_handle);
                const authors = await parseAuthors(messageAuthors);

                clearMessagesHTML();

                const messagesList = [];

                messages.forEach((message) => {
                    lastMessage = messagesList[0] || null;

                    if (message.author_handle === lastMessage?.author_handle) {
                        lastMessage.content.unshift(message.content);
                        return;
                    }

                    message.from = authors[message.author_handle];
                    message.content = [message.content];
                    messagesList.unshift(message);
                });

                messagesList.forEach((message) => {
                    addMessageHTML(message);
                });
            });
    };

    /**
     * Add a message to the message list
     * @param {Object} message - The message content
     * @param {string} message.id - The message id
     * @param {Array} message.content - The message content
     * @param {string} message.date - ISO date
     * @param {Object} message.from - The user who sent the message
     * @param {string} message.from.handle - The user handle
     * @param {string} message.from.display_name - The user name
     * @param {string} message.from.avatar_path - The user avatar
     */
    const addMessageHTML = (message) => {

        let contentHTML = "";
        message.content.forEach((content) => {
            contentHTML += `<p>${content}</p>`;
        });

        const messageHTML = `
        <article class="message">
            <img src="${
                parseMedia(message.from.avatar_path, "./img/defaults/profile_pic.png", urlOnly = true)
            }" alt="Image de profil" height="40px" />
            <div class="message-content">
                <h3>${message.from.display_name}</h3>
                ${contentHTML}
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

        if (!message) return;
        if (!opennedChat) return;

        messageInput.value = "";
        messageSubmit.disabled = true;

        POST(`/messages/${opennedChat}`, { content: message })
            .then((response) => {
                loadMessages(opennedChat);
                messageSubmit.disabled = false;
            });
    }

    // Send message on enter key
    messageInput.addEventListener("keyup", (e) => {
        if (e.key === "Enter") {
            sendMessage();
        }
    });

    // Send message on button click
    messageSubmit.addEventListener("click", sendMessage);

    const parseAuthor = async (author) => {
        const response = await GET(`/users/${author}`);
        return response.payload;
    }

    const parseAuthors = async (authors) => {
        // Remove duplicates
        const uniqueAuthors = [...new Set(authors)];

        const promises = uniqueAuthors.map(async (author) => {
            try {
                const response = await GET(`/users/${author}`);
                return response.payload;
            }
            catch (e) {
                return null;
            }
        });

        const authorsData = await Promise.all(promises);
        const authorsDict = {};

        authorsData.forEach((author) => {
            authorsDict[author.handle] = author;
        });

        return authorsDict;
    }

    /**
     * Transform an ISO date to a relative time (ex: 2h)
     * @param {string} date - ISO date
     * @returns {string} - Relative time
     */
    const relativeTime = (date) => {
        const now = new Date();
        const messageDate = new Date(date);
        const diff = now - messageDate;

        const seconds = Math.floor(diff / 1000);
        const minutes = Math.floor(seconds / 60);
        const hours = Math.floor(minutes / 60);
        const days = Math.floor(hours / 24);
        const months = Math.floor(days / 30);
        const years = Math.floor(months / 12);

        if (seconds < 60) {
            return `${seconds}s`;
        } else if (minutes < 60) {
            return `${minutes}mn`;
        } else if (hours < 24) {
            return `${hours}h`;
        } else if (days < 30) {
            return `${days}j`;
        } else if (months < 12) {
            return `${months}m`;
        } else {
            return `${years}a`;
        }
    };

    /**
     * Add a user to the user list
     * @param {Object} lastMsg - The last message sent by the user
     * @param {string} lastMsg.handle - The user handle
     * @param {string} lastMsg.name - The user name
     * @param {string} lastMsg.avatar - The user avatar
     * @param {string} lastMsg.lastMessageContent - The last message content 
     * @param {string} lastMsg.lastMessageDate - ISO date
     */
    const addUserHTML = (lastMsg) => {

        const userElement = document.createElement("div");
        userElement.classList.add("user-card");
        userElement.classList.add("bg-linear");

        userElement.innerHTML = `
            <img src="${
                parseMedia(lastMsg.author.avatar_path, "./img/defaults/profile_pic.png", urlOnly = true)
            }" alt="Image de profil" height="48px" />
            <div class="user-info">
                <h3>${lastMsg.author.display_name}</h3>
                ${lastMsg.lastMessageContent
                ? `<p>${lastMsg.lastMessageContent.length > 15 ?
                    lastMsg.lastMessageContent.substring(0, 15) + "..." :
                    lastMsg.lastMessageContent
                }
                 •
                ${lastMsg.lastMessageDate
                    ? relativeTime(lastMsg.lastMessageDate)
                    : ""
                }
                </p>`
                : ""}
            </div>`;

        userElement.addEventListener("click", () => {
            loadMessages(lastMsg.author.handle);

            userElement.querySelector(".notification-circle")?.remove();
            chatContainer.classList.remove("hidden");
        });

        messageUsers.appendChild(userElement);
    };

    const modalElement = document.querySelector(".modal");

    /**
     * Clear the user list
     */
    const clearUsersHTML = () => {
        messageUsers.innerHTML = "";
    }

    // TODO: Get users from server
    const loadUsers = () => {
        GET("/messages/latest")
            .then(async (response) => {

                clearUsersHTML();
                
                const buttonElement = document.createElement("div");
                buttonElement.classList.add("user-card");
                buttonElement.classList.add("bg-linear");
                buttonElement.classList.add("send-message");

                buttonElement.innerHTML = `<p>Envoyer un message</p>`;

                buttonElement.addEventListener("click", () => {
                    modalElement.classList.add("shown");
                });

                messageUsers.appendChild(buttonElement);

                let processedRecipient = [];

                response.payload.forEach(async lastUserMessage => {
                    
                    // author
                    let author = lastUserMessage.author_handle;
                    if (author === USER_HANDLE)
                        author = lastUserMessage.recipient_handle;

                    // prevent duplicates
                    if (processedRecipient.includes(author))
                        return;

                    processedRecipient.push(author);

                    lastUserMessage.author = await parseAuthor(author);

                    // content
                    lastUserMessage.lastMessageContent = lastUserMessage.content;

                    // date
                    lastMessageDate = parseDate(lastUserMessage.created_at);
                    lastUserMessage.lastMessageDate = lastMessageDate;

                    addUserHTML(lastUserMessage);
                });
            });
    };

    loadUsers();

    const closeModalElement = document.querySelector("#close-modal");
    closeModalElement.addEventListener("click", () => {
        modalElement.classList.remove("shown");
    });

    /* Search bar */
    const searchInput = document.querySelector("#search");
    const searchList = document.querySelector("#modal-user-list");

    let searchRequest = null;

    searchInput.addEventListener("keyup", (e) => {
        if (searchRequest) {
            searchRequest.abort();
        }

        const search = e.target.value;
        if (!search) {
            searchList.innerHTML = "<span>Recherchez quelqu'un 🔎</span>";
            return;
        }

        searchRequest = new XMLHttpRequest();

        searchRequest.addEventListener("load", () => {
            const response = JSON.parse(searchRequest.response);

            if (!response.success) {
                searchList.innerHTML = "<span>Oups, une erreur est survenue</span>";
                return;
            }

            const users = response.payload;
            if (users.length === 0) {
                searchList.innerHTML = "<span>Aucun résultat</span>";
                return;
            }

            searchList.innerHTML = "";
            users.forEach(user => {
                if (user.handle === USER_HANDLE) return;

                const userElement = document.createElement("div");
                userElement.classList.add("user-card");
                userElement.classList.add("bg-linear");

                userElement.innerHTML = `
                    <img src="${
                        parseMedia(user.avatar_path, "./img/defaults/profile_pic.png", urlOnly = true)
                    }" alt="Image de profil" height="48px" />
                    <div class="user-info">
                        <h3>${user.display_name}</h3>
                    </div>`;

                userElement.addEventListener("click", () => {
                    loadMessages(user.handle);
                    chatContainer.classList.remove("hidden");
                    modalElement.classList.remove("shown");
                });

                searchList.appendChild(userElement);
            });
        });

        searchRequest.open("GET", `/api/users?query=${search}`);
        searchRequest.send();
    });

    socket.on("WP-MSG", (message) => {
        loadUsers();
        if (opennedChat) {
            loadMessages(opennedChat);
        }
    })
})()