function addEventListeners() {
    document.addEventListener('DOMContentLoaded', fadeAlert);
    document.addEventListener('DOMContentLoaded', switchGroupTab);
  
    let cancelButton = document.getElementById('cancelButton');
  
    if(cancelButton !== null){
      cancelButton.addEventListener('click', () => {
        const deleteMenu = document.getElementById('deleteMenu');
        html.classList.toggle('overflow-hidden');
        deleteMenu.classList.add('hidden');
      });
    }
    let confirmButton = document.getElementById('confirmButton');
    if(confirmButton !== null){
      confirmButton.addEventListener('click', () => {
        const deleteForm = document.getElementById(`deleteForm-${window.selectedPostId}`);
        deleteForm.submit();
      });
    }
  
    document.addEventListener('DOMContentLoaded', function() {
      handlePagination('posts-container');
      handlePagination('users-container');
    });
  
    document.addEventListener('DOMContentLoaded', handleDeleteFormSubmission);
  
    setupCreateUserMenu();
    addEventListenerEditUserAdmin();
    eventListernerFormsAdmin();
  }

  const buttonsG = document.querySelectorAll('.tab-btn');
  let groupTab = "group-posts"; // Default tab
  let groupId = document.getElementById('groupPage').dataset.groupid;

  function switchGroupTab() {
    buttonsG.forEach(button => {
      button.addEventListener('click', () => {
        currentPage = 1;  // Reset page for new tab content
        groupTab = button.dataset.tab;

        // Toggle active button
        buttonsG.forEach(btn => {
          btn.classList.remove('text-sky-900', 'border-sky-900');
        });
        button.classList.add('text-sky-900', 'border-sky-900');
        
        loadGroupContent(groupTab);
      });
    });
  }

  function loadGroupContent(tab) {
    const groupContent = document.querySelector("#group-tab-content");
    if (!groupContent) return;

    // Clear the current content
    while (groupContent.firstChild) {
        groupContent.removeChild(groupContent.firstChild);
    }

    insertLoadingCircle(groupContent);

    // Send AJAX request based on the selected tab
    switch (tab) {
        case 'group-posts':
            sendAjaxRequest('get', `/api/groups/${groupId}/posts?page=${currentPage}`, null, insertMoreGroupContent);
            break;

        case 'group-members':
            sendAjaxRequest('get', `/api/groups/${groupId}/members?page=${currentPage}`, null, insertMoreGroupContent);
            break;

        case 'group-invitations':
            sendAjaxRequest('get', `/api/groups/${groupId}/invitations?page=${currentPage}`, null, insertMoreGroupContent);
            break;
    }
  }

  function insertMoreGroupContent() {
    removeLoadingCircle(); // Remove the loading indicator
    const groupContent = document.querySelector("#group-tab-content");

    let results = JSON.parse(this.responseText);

    // Populate content based on the current tab
    switch (groupTab) {
        case 'group-posts':
            maxPage = results.last_page;
            insertMorePosts(groupContent, results);
            break;

        case 'group-members':
            maxPage = results.last_page;
            insertMoreUsers(groupContent, results);
            break;

        case 'group-invitations':
            maxPage = results.last_page;
            insertMoreInvitations(groupContent, results);
            break;
        default:
            return;
    }

    if(groupContent.firstChild == null){
      groupContent.innerHTML = `
        <div class="flex justify-center items-center h-32">
                <p class="text-gray-600 text-center">No ${groupTab == 'group-posts' ? 'posts' : (groupTab == 'group-members' ? 'members' : 'invitations')} found for this group.</p>
        </div>
        `;       
    }
  }

  addEventListeners();