function addEventListeners() {
    document.addEventListener('DOMContentLoaded', fadeAlert);
    document.addEventListener('DOMContentLoaded', switchGroupTab);
    window.addEventListener("scroll", infiniteScroll);
    
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('all-notifications-tab')) {
            document.getElementById('all-notifications-tab').addEventListener('click', function() {
                showTab('all-notifications');
            });
        }

        if (document.getElementById('comments-tab')) {
            document.getElementById('comments-tab').addEventListener('click', function() {
                showTab('comments');
            });
        }

        if (document.getElementById('likes-tab')) {
            document.getElementById('likes-tab').addEventListener('click', function() {
                showTab('likes');
            });
        }

        if (document.getElementById('follows-tab')) {
            document.getElementById('follows-tab').addEventListener('click', function() {
                showTab('follows');
            });
        }

        showTab('all-notifications');
    });
}

function showTab(tab) {
    const contentElement = document.getElementById(tab + '-content');
    if (contentElement) {
        const sections = document.querySelectorAll('.notifications-section');
        sections.forEach(function(section) {
            section.classList.add('hidden');
        });
        contentElement.classList.remove('hidden');
    }

    const tabElement = document.getElementById(tab + '-tab');
    if (tabElement) {
        const tabs = document.querySelectorAll('.tab-btn');
        tabs.forEach(function(tab) {
            tab.classList.remove('text-blue-600');
            tab.classList.add('text-gray-600');
        });
        tabElement.classList.add('text-blue-600');
        tabElement.classList.remove('text-gray-600');
    }
}


addEventListeners();
