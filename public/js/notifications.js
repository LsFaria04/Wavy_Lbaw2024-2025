function addEventListeners() {
    document.addEventListener('DOMContentLoaded', () => {
        fadeAlert();
        switchGroupTab();
        initializeNotificationTabs();
        showTab('all-notifications'); //default tab
    });

    window.addEventListener("scroll", infiniteScroll);
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
