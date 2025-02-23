function addEventListeners() {
    window.addEventListener("scroll", infiniteScroll);
    document.addEventListener('DOMContentLoaded', () => {
        fadeAlert();
        initializeNotificationTabs();
        showTab('all-notifications'); //default tab

        // initialize Pusher when userId is valid
        const interval = setInterval(() => {
            if (userId !== -1) {
                initializePusher(userId);
                clearInterval(interval);
            }
        }, 100); // Check every 100ms
    });

}



//Pusher notifications -----------------------------------------------------------------------------


//Trigger the notifications pop up when a notification is received
function triggerPopupNotification(message) {
    if (!message) return;
    
    const notificationPopup = document.createElement('div');
    notificationPopup.classList.add('p-4', 'mb-4', 'text-sm', 'text-green-800', 'rounded-lg', 'bg-green-50', 'dark:bg-gray-800', 'dark:text-green-400');
    notificationPopup.setAttribute('role', 'alert');

    notificationPopup.innerHTML = `
        <span class="font-medium"></span> ${message}
    `;
    
    document.body.appendChild(notificationPopup);

    // Apply position and animation styles
    notificationPopup.style.position = 'fixed';
    notificationPopup.style.top = '20px';
    notificationPopup.style.left = '50%';
    notificationPopup.style.zIndex = '9999';
    notificationPopup.style.opacity = '1';
    notificationPopup.style.transition = 'opacity 0.5s ease, transform 0.3s ease';

    setTimeout(() => {
        notificationPopup.style.opacity = '0';  // fade out
        setTimeout(() => {
            notificationPopup.remove();  // remove from DOM after fade out
        }, 500); // wait for fade out to finish
    }, 5000); // popup stays for 5 seconds
}

//initializes the pusher
function initializePusher(userId) {
    const pusher = new Pusher('0b3c646b9e8aeb6f4458', {
        cluster: 'eu',
        encrypted: true
    });

    const channel = pusher.subscribe('public-user.' + userId);

    // Handle "like" notifications
    channel.bind('notification-postlike', function(data) {
        const timestamp = data.timestamp || new Date().toISOString();
        triggerPopupNotification(data.message);
    });

    // Handle "comment" notifications
    channel.bind('notification-postcomment', function(data) {
        const timestamp = data.timestamp || new Date().toISOString();

        triggerPopupNotification(data.message);
    });

    // Handle "follow" notifications
    channel.bind('notification-follow', function (data) {
        const timestamp = data.timestamp || new Date().toISOString();
        const type = data.type === 'follow-request' ? 'follow-requests' : 'follows';
        triggerPopupNotification(data.message);
    });
 
}



//Notifications creation ------------------------------------------------------------------------------------------------------------------------------


//Creates the notification element (container and it's content)
function createNotificationElement(type, message, timestamp, data) {
    if (!data) {
        console.error("No data received for notification:", message);
        return;  // Exit if data is undefined
    }

    const notificationElement = document.createElement('div');
    notificationElement.classList.add('flex', 'items-center', 'p-4', 'mb-4', 'bg-white', 'rounded-lg', 'shadow-sm', 'border-b', 'border-gray-300');

    const formattedDate = timestamp

    let notificationContent = `
        <div class="flex-1">
            <div class="text-sm font-semibold text-gray-800">
                ${message}
            </div>
        </div>
        <div class="text-xs text-gray-400">
            ${formattedDate}
        </div>
    `;

    // Customize notification content based on type
    if (data) {

        let username = 'Unknown user';
        if(data.like) {
            username = data.like.user.username;
        }
        else if(data.follow) {
            username = data.follow.follower.username;
        }
        else if(data.comment) {
            username = data.comment.user.username;
        }
        
        if (type === 'likes') {
            const postUrl = `/posts/${data.like.post.postid}`;
            const usernameUrl = `/profile/${data.like.user.username}`;
            notificationContent = `
                <div class="flex-1">
                    <div class="text-sm font-semibold text-gray-800">
                        <a href="${usernameUrl}" class="text-sky-600 hover:underline">${username}</a> liked your post
                    </div>
                    <div class="text-sm text-gray-500">
                        <a href="${postUrl}" class="text-sky-600 hover:underline">"${limitText(message)}"</a>
                    </div>
                </div>
                <div class="text-xs text-gray-400">
                    ${formattedDate}
                </div>
            `;
        } else if (type === 'comments') {
            const postUrl = `/posts/${data.comment.postid}`;
            const usernameUrl = `/profile/${data.comment.user.username}`;
            let commentMessage = limitText(message);

            if (data.comment && data.comment.parent_comment) { //reply to a comment
                const postUrl = `/posts/${data.parent_comment.postid}`;
                notificationContent = `
                    <div class="flex-1">
                        <div class="text-sm font-semibold text-gray-800">
                            <a href="${usernameUrl}" class="text-sky-600 hover:underline">${username}</a> replied to a comment:
                            <span class="italic text-gray-600">"${commentMessage}"</span>
                        </div>
                        <div class="text-sm text-gray-500">
                            <a href="${postUrl}" class="text-sky-600 hover:underline">
                                On comment: "${limitText(data.comment.parent_comment.message)}"
                            </a>
                        </div>
                    </div>
                    <div class="text-xs text-gray-400">
                        ${formattedDate}
                    </div>
                `;
            } else {  // Normal comment
                notificationContent = `
                    <div class="flex-1">
                        <div class="text-sm font-semibold text-gray-800">
                            <a href="${usernameUrl}" class="text-sky-600 hover:underline">${username}</a> commented:
                            <span class="italic text-gray-600">"${commentMessage}"</span>
                        </div>
                        <div class="text-sm text-gray-500">
                            <a href="${postUrl}" class="text-sky-600 hover:underline">
                                On post: "${limitText(data.comment.post.message)}"
                            </a>
                        </div>
                    </div>
                    <div class="text-xs text-gray-400">
                        ${formattedDate}
                    </div>
                `;
            }
        } else if (type === 'follows') {
            const usernameUrl = `/profile/${data.follow?.follower.username}`;
            notificationContent = `
                <div class="flex-1">
                    <div class="text-sm font-semibold text-gray-800">
                        <a href="${usernameUrl}" class="text-sky-600 hover:underline">${username}</a>
                        ${data.follow.state === 'pending' ? 'requested to follow you' : 'followed you'}
                    </div>
                </div>
                <div class="text-xs text-gray-400">
                    ${formattedDate}
                </div>
            `;
        } else {
            console.error("Unknown notification type:", type);
        }
    } else {
        console.error("Missing or malformed data for notification");
    }

    notificationElement.innerHTML = notificationContent;
    return notificationElement;
}

function limitText(text, limit = 50) {
    if (text.length > limit) {
        return text.substring(0, limit) + '...';
    }
    return text;
}

//Notification button tab functions (used when one of the buttons in the tab is clicked) -----------------------------------------------


//Initializes the event listener for the clicks in the tab buttons
function initializeNotificationTabs() {
    const tabs = ['all-notifications', 'comments', 'likes', 'follows'];

    tabs.forEach(tab => {
        const tabElement = document.getElementById(`${tab}-tab`);
        if (tabElement) {
            tabElement.addEventListener('click', () => showTab(tab));
        }
    });
}


//Shows the section that was selected by the user when he clicked the button in the tab. Calls the load function to load the notifications from the DB
let notificationsTab = 'all-notifications';
function showTab(tab) {
    notificationsTab = tab;
    currentPage = 1;
    const notificationsContainer = document.querySelector(`#${notificationsTab}-content`);

    if(notificationsContainer === null) {
        //not in notifications page
        return;
    }

    while(notificationsContainer?.firstChild) {
        notificationsContainer?.firstChild.remove();
    }
    toggleVisibility(tab + '-content', '.notifications-section');
    toggleTabHighlight(tab + '-tab', '.tab-btn');
    loadNotifications();
}

//Toggles the visibility of the sections (shoe the one that the user selected)
function toggleVisibility(activeId, groupSelector) {
    const sections = document.querySelectorAll(groupSelector);
    sections.forEach(section => {
        section.classList.add('hidden');
    });

    const activeElement = document.getElementById(activeId);
    if (activeElement) {
        activeElement.classList.remove('hidden');
    }
}

//Changes the tab highlight depending on the section that is being displayed
function toggleTabHighlight(activeTabId, groupSelector) {
    const tabs = document.querySelectorAll(groupSelector);
    tabs.forEach(tab => {
        tab.classList.remove('text-sky-600');
        tab.classList.add('text-gray-600');
    });

    const activeTab = document.getElementById(activeTabId);
    if (activeTab) {
        activeTab.classList.add('text-sky-600');
        activeTab.classList.remove('text-gray-600');
    }
}


//Notification Loading and insertion (new notifications retrieved from the DB) ------------------------------------------

//Loads the first set of notifications from the db and inserts them into the appropriated section
function loadNotifications() {
    const notificationsPage = document.querySelector("#notifications-content");
    insertLoadingCircle(notificationsPage);
    sendAjaxRequest('post', '/api/notifications?page=' + currentPage + '&category=' + notificationsTab, null, function() {
        insertMoreNotifications(this.responseText);
        loading = false;
    });
}

//inserts the notification loaded from the db into the appropriated section
function insertMoreNotifications(response) {
    removeLoadingCircle();

    let notifications = JSON.parse(response);

    maxPage = notifications.last_page;

    const notificationsContainer = document.querySelector(`#${notificationsTab}-content`);
    if (notificationsContainer) {
        if (notifications.data && notifications.data.length > 0) {
            notifications.data.forEach(notification => {
                let type = null;
                let message = null;
                if(notification.followid !== null) {
                    type = 'follows';
                    message = "Follow";
                }
                else if (notification.likeid !== null) {
                    type = 'likes';
                    message = notification.like?.post?.message;
                    if(message === undefined) {
                        message = notification.like?.comment.message;
                    }
                }
                else if (notification.commentid !== null) {
                    type = 'comments';
                    message = notification.comment.message;
                }
                const notificationElement = createNotificationElement(type, message, notification.date, notification);
                notificationsContainer.appendChild(notificationElement);
            });


        } else {
            const warning = document.createElement('p');
            warning.innerHTML = 'No more notifications found.';
            notificationsContainer.appendChild(warning);
        }
    }
}


addEventListeners();
