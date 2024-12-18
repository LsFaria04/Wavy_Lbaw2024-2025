function addEventListeners() {
    document.addEventListener('DOMContentLoaded', () => {
        fadeAlert();
        initializeNotificationTabs();
        showTab('all-notifications'); //default tab

        initializePusher(receiverId);
    });

    window.addEventListener("scroll", infiniteScroll);
}

function initializePusher(receiverId) {
    const pusher = new Pusher('0b3c646b9e8aeb6f4458', {
        cluster: 'eu',
        encrypted: true
    });
    
    const channel = pusher.subscribe('private-user.' + receiverId);

    // Handle "like" notifications
    channel.bind('notification-postlike', function(data) {
        console.log(`New like notification: ${data.message}`);
        handleNotification('likes', data.message);
    });

    // Handle "comment" notifications
    channel.bind('notification-postcomment', function(data) {
        console.log(`New comment notification: ${data.message}`);
        handleNotification('comments', data.message);
    });

    // Handle "follow" notifications
    channel.bind('notification-follow', function (data) {
        console.log(`New follow notification: ${data.message}`);
        const type = data.type === 'follow-request' ? 'follow-requests' : 'follows';
        handleNotification(type, data.message);
    });
    
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
    notificationElement.classList.add('notification-item');
    notificationElement.innerHTML = `
        <div class="notification-content">
            <p>${message}</p>
            <span class="notification-time">${new Date().toLocaleString()}</span>
        </div>`;
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
