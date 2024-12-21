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
    notificationPopup.style.right = '20px';
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


function initializePusher(userId) {
    const pusher = new Pusher('0b3c646b9e8aeb6f4458', {
        cluster: 'eu',
        encrypted: true
    });

    const channel = pusher.subscribe('public-user.' + userId);

    // Handle "like" notifications
    channel.bind('notification-postlike', function(data) {
        console.log('Received like notification:', data);
        const timestamp = data.timestamp || new Date().toISOString();
        triggerPopupNotification(data.message);
    });

    // Handle "comment" notifications
    channel.bind('notification-postcomment', function(data) {
        console.log(`New comment notification: ${data.message}`);
        const timestamp = data.timestamp || new Date().toISOString();
        triggerPopupNotification(data.message);
    });

    // Handle "follow" notifications
    channel.bind('notification-follow', function (data) {
        console.log(`New follow notification: ${data.message}`);
        const timestamp = data.timestamp || new Date().toISOString();
        const type = data.type === 'follow-request' ? 'follow-requests' : 'follows';
        triggerPopupNotification(data.message);
    });
 
    console.log(`Subscribed to channel: public-user.${userId}`);
}


function createNotificationElement(type, message, timestamp, data) {
    if (!data) {
        console.error("No data received for notification:", message);
        return;  // Exit if data is undefined
    }

    const notificationElement = document.createElement('div');
    notificationElement.classList.add('flex', 'items-center', 'p-4', 'mb-4', 'bg-gray-50', 'rounded-lg', 'shadow-sm', 'space-y-4');

    const date = new Date(timestamp);
    const formattedDate = formatRelativeTime(date);

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
    if (data && data.message && data.user) {
        const username = data.user.username || 'Unknown user';
        const postUrl = `/posts/${data.post_id}`;
        const usernameUrl = `/profile/${data.user.username}`;

        if (type === 'likes') {
            notificationContent = `
                <div class="flex-1">
                    <div class="text-sm font-semibold text-gray-800">
                        <a href="${usernameUrl}" class="text-blue-600 hover:underline">${username}</a> liked your post
                    </div>
                    <div class="text-sm text-gray-500">
                        <a href="${postUrl}" class="text-blue-600 hover:underline">Your Post</a>
                    </div>
                </div>
                <div class="text-xs text-gray-400">
                    ${formattedDate}
                </div>
            `;
        } else if (type === 'comments') {
            notificationContent = `
                <div class="flex-1">
                    <div class="text-sm font-semibold text-gray-800">
                        <a href="${usernameUrl}" class="text-blue-600 hover:underline">${username}</a> commented on your post
                    </div>
                    <div class="text-sm text-gray-500">
                        <a href="${postUrl}" class="text-blue-600 hover:underline">Your Post</a>
                    </div>
                </div>
                <div class="text-xs text-gray-400">
                    ${formattedDate}
                </div>
            `;
        } else if (type === 'follows') {
            notificationContent = `
                <div class="flex-1">
                    <div class="text-sm font-semibold text-gray-800">
                        <a href="${usernameUrl}" class="text-blue-600 hover:underline">${username}</a> followed you
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

function formatRelativeTime(date) {
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);
    const rtf = new Intl.RelativeTimeFormat('en', { numeric: 'auto' });

    if (seconds < 60) {
        return rtf.format(-seconds, 'second');
    } else if (seconds < 3600) {
        return rtf.format(-Math.floor(seconds / 60), 'minute');
    } else if (seconds < 86400) {
        return rtf.format(-Math.floor(seconds / 3600), 'hour');
    } else if (seconds < 2592000) {
        return rtf.format(-Math.floor(seconds / 86400), 'day');
    } else if (seconds < 31536000) {
        return rtf.format(-Math.floor(seconds / 2592000), 'month');
    } else {
        return rtf.format(-Math.floor(seconds / 31536000), 'year');
    }
}


function initializeNotificationTabs() {
    const tabs = ['all-notifications', 'comments', 'likes', 'follows'];

    tabs.forEach(tab => {
        const tabElement = document.getElementById(`${tab}-tab`);
        if (tabElement) {
            tabElement.addEventListener('click', () => showTab(tab));
        }
    });
}

function showTab(tab) {
    toggleVisibility(tab + '-content', '.notifications-section');
    toggleTabHighlight(tab + '-tab', '.tab-btn');
}

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

function toggleTabHighlight(activeTabId, groupSelector) {
    const tabs = document.querySelectorAll(groupSelector);
    tabs.forEach(tab => {
        tab.classList.remove('text-blue-600');
        tab.classList.add('text-gray-600');
    });

    const activeTab = document.getElementById(activeTabId);
    if (activeTab) {
        activeTab.classList.add('text-blue-600');
        activeTab.classList.remove('text-gray-600');
    }
}

function insertMoreNotifications(response) {
    removeLoadingCircle();

    let notifications = JSON.parse(response);

    maxPage = notifications.last_page;

    const notificationsContainer = document.querySelector("#notifications-content");
    if (notificationsContainer) {
        if (notifications.data && notifications.data.length > 0) {
            notifications.data.forEach(notification => {
                const notificationElement = createNotificationElement(notification.type, notification.message, notification.created_at, notification);
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
