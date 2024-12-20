function addEventListeners() {
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
        handleNotification('likes', data.message);
        triggerPopupNotification(data.message);
    });

    // Handle "comment" notifications
    channel.bind('notification-postcomment', function(data) {
        console.log(`New comment notification: ${data.message}`);
        handleNotification('comments', data.message);
        triggerPopupNotification(data.message);
    });

    // Handle "follow" notifications
    channel.bind('notification-follow', function (data) {
        console.log(`New follow notification: ${data.message}`);
        const type = data.type === 'follow-request' ? 'follow-requests' : 'follows';
        handleNotification(type, data.message);
        triggerPopupNotification(data.message);
    });
 
    console.log(`Subscribed to channel: public-user.${userId}`);
}

function handleNotification(type, message) {
    // Update the "all-notifications" tab
    const allNotificationsContainer = document.getElementById('all-notifications-content');
    if (allNotificationsContainer) {
        const notificationElement = createNotificationElement(message);
        allNotificationsContainer.prepend(notificationElement);
    }

    // Update the specific tab for the notification type
    const specificNotificationsContainer = document.getElementById(`${type}-content`);
    if (specificNotificationsContainer) {
        const notificationElement = createNotificationElement(message);
        specificNotificationsContainer.prepend(notificationElement);
    }
}

function createNotificationElement(message) {
    const notificationElement = document.createElement('div');
    notificationElement.classList.add('flex', 'items-center', 'p-4', 'mb-4', 'bg-gray-50', 'rounded-lg', 'shadow-sm', 'space-y-4');

    notificationElement.innerHTML = `
        <div class="flex-1">
            <div class="text-sm font-semibold text-gray-800">
                ${message}
            </div>
        </div>
        <div class="text-xs text-gray-400">
            ${new Date().toLocaleString()}
        </div>
    `;

    return notificationElement;
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

function insertMoreNotifications() {
    removeLoadingCircle();

    const notificationsContainer = document.querySelector("#notifications-content");
    if (!notificationsContainer) return;

    try {
        let notifications = JSON.parse(this.responseText);

        console.log(notifications);

        maxPage = notifications.last_page || 0;

        if (notifications.data && notifications.data.length > 0) {
            notifications.data.forEach(notification => {
                const notificationElement = document.createElement("div");
                notificationElement.classList.add("notification-item");
                notificationElement.innerHTML = `
                    <div class="notification-content">
                        <p>${notification.message}</p>
                        <span class="notification-time">${notification.created_at}</span>
                    </div>`;
                notificationsContainer.appendChild(notificationElement);
            });
        }
    } catch (error) {
        console.error("Failed to load notifications:", error);
    }
}


addEventListeners();
