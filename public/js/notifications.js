function addEventListeners() {
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('all-notifications-tab').addEventListener('click', function() {
            showTab('all-notifications');
        });

        document.getElementById('comments-tab').addEventListener('click', function() {
            showTab('comments');
        });

        document.getElementById('likes-tab').addEventListener('click', function() {
            showTab('likes');
        });

        document.getElementById('follows-tab').addEventListener('click', function() {
            showTab('follows');
        });

        showTab('all-notifications');
    });
}

function showTab(tab) {
    const sections = document.querySelectorAll('.notifications-section');
    sections.forEach(function(section) {
        section.classList.add('hidden');
    });

    const tabs = document.querySelectorAll('.tab-btn');
    tabs.forEach(function(tab) {
        tab.classList.remove('text-blue-600');
        tab.classList.add('text-gray-600');
    });

    document.getElementById(tab + '-content').classList.remove('hidden');

    document.getElementById(tab + '-tab').classList.add('text-blue-600');
    document.getElementById(tab + '-tab').classList.remove('text-gray-600');
}

addEventListeners();
