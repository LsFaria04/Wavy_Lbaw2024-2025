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

        case 'group-requests':
          sendAjaxRequest('get', `/api/groups/${groupId}/requests?page=${currentPage}`, null, insertMoreGroupContent);
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

          case 'group-requests':
            maxPage = results.last_page;
            insertMoreRequests(groupContent, results);
            break;

        default:
            return;
    }

    if(groupContent.firstChild == null){
      groupContent.innerHTML = `
        <div class="flex justify-center items-center h-32">
                <p class="text-gray-600 text-center">No ${groupTab == 'group-posts' ? 'posts' : (groupTab == 'group-members' ? 'members' : (groupTab == 'group-invitations' ? 'invitations' : 'join requests'))} found for this group.</p>
        </div>
        `;       
    }
  }

  // Creates an invitation container with all the necessary info
  function createInvitation(invitationInfo) {
    let invitation = document.createElement('div');
    invitation.classList.add("invitation", "mb-4", "p-4", "bg-white", "rounded-md", "shadow-md");

    if (!invitationInfo.user) {
        console.error("User data is missing in invitationInfo:", invitationInfo);
        invitation.innerHTML = `<p>Error: User information is unavailable.</p>`;
        return invitation;
    }

    invitation.innerHTML = `
        <div class="invitation-header mb-2">
            <h3 class="font-bold">
                <a href="../profile/${invitationInfo.user.username}" class="text-black hover:text-sky-900">
                    ${invitationInfo.user.username}
                </a>
            </h3>
        </div>
        <div class="invitation-body mb-2">
            <p>Invitation sent: ${invitationInfo.date || 'Date unavailable'}</p>
            <p>Status: ${invitationInfo.state}</p>
        </div>
        <button class="cancel-btn bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-700" data-id="${invitationInfo.groupid}">
            Cancel
        </button>
    `;

    return invitation;
  }

  // Inserts more invitations into an element
  function insertMoreInvitations(element, invitations) {
    for (let i = 0; i < invitations.data.length; i++) {
        let invitationElement = createInvitation(invitations.data[i]);
        element.appendChild(invitationElement);
    }
  }

  // Creates an join request container with all the necessary info
  function createRequest(requestInfo) {
    let request = document.createElement('div');
    request.classList.add("request", "mb-4", "p-4", "bg-white", "rounded-md", "shadow-md");

    if (!requestInfo.user) {
        console.error("User data is missing in requestInfo:", requestInfo);
        request.innerHTML = `<p>Error: User information is unavailable.</p>`;
        return requestInfo;
    }

    request.innerHTML = `
        <div class="request-header mb-2">
            <h3 class="font-bold">
                <a href="../profile/${requestInfo.user.username}" class="text-black hover:text-sky-900">
                    ${requestInfo.user.username}
                </a>
            </h3>
        </div>
        <div class="request-body mb-2">
            <p>Join request received: ${requestInfo.date || 'Date unavailable'}</p>
            <p>Status: ${requestInfo.state}</p>
        </div>
        <div class="request-actions">
            <button class="accept-btn bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-700" data-id="${requestInfo.groupid}">
                Accept
            </button>
            <button class="reject-btn bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-700" data-id="${requestInfo.groupid}">
                Reject
            </button>
        </div>
    `;

    return request;
  }

  // Inserts more join requests into an element
  function insertMoreRequests(element, requests) {
    for (let i = 0; i < requests.data.length; i++) {
        let requestElement = createRequest(requests.data[i]);
        element.appendChild(requestElement);
    }
  }

  addEventListeners();