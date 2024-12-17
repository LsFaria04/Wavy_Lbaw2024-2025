function addEventListeners() {
  document.addEventListener('DOMContentLoaded', fadeAlert);
  document.addEventListener('DOMContentLoaded', switchGroupTab);

  document.addEventListener('click', function (e) {
        // Find the closest element with the 'cancel-btn' class
        const cancelButton = e.target.closest('.cancel-btn');

        if (cancelButton) {
            const invitationId = cancelButton.dataset.id;

            if (!groupId || !invitationId) {
                console.error('Group ID or Invitation ID is missing.');
                return;
            }

            sendAjaxRequest('delete', `/api/groups/${groupId}/invitations/${invitationId}`, {}, function () {
                if (this.status === 200) {
                    const response = JSON.parse(this.responseText);
                    console.log(response.message);

                    const invitationElement = cancelButton.closest('.invitation');
                    if (invitationElement) invitationElement.remove();

                    loadGroupContent('group-invitations');
                    alert(response.message);
                } else {
                    console.error('Failed to cancel the invitation:', this.responseText);
                }
            });
        }
  });

  document.addEventListener('click', function (e) {
      if (e.target && e.target.classList.contains('accept-btn')) {
          const requestId = e.target.dataset.id;

          if (!groupId || !requestId) {
              console.error('Group ID or Request ID is missing.');
              return;
          }

          sendAjaxRequest('post', `/api/groups/${groupId}/requests/${requestId}/accept`, {}, function () {
              if (this.status === 200) {
                  const response = JSON.parse(this.responseText);
                  console.log(response.message);

                  const requestElement = e.target.closest('.request');
                  if (requestElement) requestElement.remove();

                  loadGroupContent('group-requests');
                  alert(response.message);
              } else {
                  console.error('Failed to accept request:', this.responseText);
              }
          });
      }
  });

  document.addEventListener('click', function (e) {
      if (e.target && e.target.classList.contains('reject-btn')) {
          const requestId = e.target.dataset.id;

          if (!groupId || !requestId) {
              console.error('Group ID or Request ID is missing.');
              return;
          }

          sendAjaxRequest('post', `/api/groups/${groupId}/requests/${requestId}/reject`, {}, function () {
              if (this.status === 200) {
                  const response = JSON.parse(this.responseText);
                  console.log(response.message);

                  const requestElement = e.target.closest('.request');
                  if (requestElement) requestElement.remove();

                  loadGroupContent('group-requests');
                  alert(response.message);
              } else {
                  console.error('Failed to reject request:', this.responseText);
              }
          });
      }
  });

  document.addEventListener('click', function (e) {
      if (e.target && e.target.id === 'ask-to-join-btn') {
          console.log('Ask to Join button clicked.');

          // Send join request
          sendAjaxRequest('post', `/api/groups/${groupId}/requests`, null, function () {
              if (this.status === 200) {
                  const response = JSON.parse(this.responseText);
                  alert(response.message || 'Join request sent successfully!');
                  e.target.disabled = true; // Disable button after sending request
              } else {
                  console.error('Failed to send join request:', this.responseText);
              }
          });
      }
  });

  // Invite modal functionality
    let selectedUserId = null;
    document.addEventListener('click', function (e) {
        const inviteModal = document.getElementById('invite-modal');
        const searchResults = document.getElementById('search-results');
        const userSearchInput = document.getElementById('user-search');
        const sendInviteButton = document.getElementById('send-invite');

        // Open modal
        if (e.target && e.target.id === 'invite-users-btn') {
            inviteModal.classList.remove('hidden');
            inviteModal.classList.add('flex');
        }

        // Close modal
        if (e.target.closest('#close-invite-modal')) {
            inviteModal.classList.add('hidden');
            inviteModal.classList.remove('flex');
            searchResults.innerHTML = '';
            userSearchInput.value = '';
            sendInviteButton.disabled = true;
            selectedUserId = null;
        }

        // Select a user from search results
        if (e.target && e.target.closest('.search-result')) {
            const result = e.target.closest('.search-result');
            selectedUserId = result.dataset.id;
            console.log('User ID Found:', selectedUserId);
            sendInviteButton.disabled = false;
        }

        // Send invitation
        if (e.target && e.target.id === 'send-invite') {
            if (!selectedUserId) return;

            console.log('Sending invite to User ID:', selectedUserId);

            sendAjaxRequest('post', `/api/groups/${groupId}/invitations`, { userid: selectedUserId }, function () {
                if (this.status === 200) {
                    const response = JSON.parse(this.responseText);
                    alert(response.message || 'Invitation sent successfully!');
                    inviteModal.classList.add('hidden');
                    searchResults.innerHTML = '';
                    userSearchInput.value = '';
                    sendInviteButton.disabled = true;
                    loadGroupContent('group-invitations');
                } else {
                    console.error('Failed to send invitation:', this.responseText);
                }
            });
        }
    });

  document.addEventListener('input', function (e) {
      if (e.target && e.target.id === 'user-search') {
          const query = e.target.value.trim();
          const searchResults = document.getElementById('search-results');

          if (query.length < 3) {
              searchResults.innerHTML = '<p class="text-gray-500">Please type at least 3 characters.</p>';
              return;
          }

          sendAjaxRequest('get', `/api/search?q=${encodeURIComponent(query)}&category=users`, null, function () {
              if (this.status === 200) {
                  const response = JSON.parse(this.responseText);
                  const users = response[1];

                  if (users.data.length === 0) {
                      searchResults.innerHTML = '<p class="text-gray-500">No users found.</p>';
                  } else {
                      searchResults.innerHTML = users.data.map(user => `
                          <div class="search-result p-2 hover:bg-gray-100 flex items-center cursor-pointer" data-id="${user.userid}">
                              <img src="" alt="mock" class="h-8 w-8 rounded-full mr-2">
                              <span>${user.username}</span>
                          </div>
                      `).join('');
                  }
              }
          });
      }
    });

    document.getElementById('cancelExitButton')?.addEventListener('click', () => {
        const exitMenu = document.getElementById('exitGroupMenu');
        exitMenu.classList.add('hidden');
        exitMenu.classList.remove('flex');
    });

    document.getElementById('cancelDeleteButton')?.addEventListener('click', () => {
        const deleteMenu = document.getElementById('deleteGroupMenu');
        deleteMenu.classList.add('hidden');
        deleteMenu.classList.remove('flex');
    });
}

function toggleEditGroupMenu() {
    const editMenu = document.getElementById('edit-group-menu');
    editMenu.classList.toggle('hidden');
    editMenu.classList.toggle('flex');
    html.classList.toggle('overflow-hidden');
}

function openDeleteGroupMenu() {
    const deleteMenu = document.getElementById('deleteGroupMenu');
    deleteMenu.classList.remove('hidden');
    deleteMenu.classList.add('flex');
}

function openExitGroupMenu() {
    const exitMenu = document.getElementById('exitGroupMenu');
    exitMenu.classList.remove('hidden');
    exitMenu.classList.add('flex');
}

function openRemoveMemberMenu(memberId, username) {
    const removeMenu = document.getElementById('removeMemberMenu');
    const removeMessage = document.getElementById('removeMemberMessage');
    const removeForm = document.getElementById('removeMemberForm');

    // Update the confirmation message and form action
    removeMessage.textContent = `Are you sure you want to remove ${username} from the group?`;
    removeForm.action = `/groups/${groupId}/remove/${memberId}`;

    // Show the dialog
    removeMenu.classList.remove('hidden');
    removeMenu.classList.add('flex');

    document.getElementById('cancelRemoveButton').addEventListener('click', () => {
        const removeMenu = document.getElementById('removeMemberMenu');
        removeMenu.classList.add('hidden');
        removeMenu.classList.remove('flex')
    });
}

const buttonsG = document.querySelectorAll('.tab-btn');
let groupTab = "group-posts"; // Default tab
let groupId = document.getElementById('groupPage')?.dataset.groupid;
let ownerid = document.getElementById('groupPage')?.dataset.ownerid; 

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

        // Show or hide the Add Post Section based on the active tab
        if (groupTab === "group-posts" && addPostSection) {
            addPostSection.classList.remove('hidden');
        } else if (addPostSection) {
            addPostSection.classList.add('hidden');
        }
        
        loadGroupContent(groupTab);
      });
    });
}

function loadGroupContent(tab) {
    const addPost = document.getElementById('post-form');
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
            if(addPost) {
              addPost.classList.add('flex');
              addPost.classList.remove('hidden');
            }
            break;

        case 'group-members':
            sendAjaxRequest('get', `/api/groups/${groupId}/members?page=${currentPage}`, null, insertMoreGroupContent);
            if(addPost) {
              addPost.classList.add('hidden');
              addPost.classList.remove('flex');
            }
            break;

        case 'group-invitations':
            sendAjaxRequest('get', `/api/groups/${groupId}/invitations?page=${currentPage}`, null, insertMoreGroupContent);
            if(addPost) {
              addPost.classList.add('hidden');
              addPost.classList.remove('flex');
            }
            break;

        case 'group-requests':
          sendAjaxRequest('get', `/api/groups/${groupId}/requests?page=${currentPage}`, null, insertMoreGroupContent);
          if(addPost) {
            addPost.classList.add('hidden');
            addPost.classList.remove('flex');
          }
          break;
    }
}

function insertMoreGroupContent() {
    removeLoadingCircle(); 
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
            insertMoreMembers(groupContent, results);
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

function createInvitation(invitationInfo) {
    let invitation = document.createElement('div');
    invitation.classList.add("invitation", "border-b", "border-gray-300", "p-4", "bg-white");

    if (!invitationInfo.user) {
        console.error("User data is missing in invitationInfo:", invitationInfo);
        invitation.innerHTML = `<p>Error: User information is unavailable.</p>`;
        return invitation;
    }

    invitation.innerHTML = `
        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-bold">
                    <a href="../profile/${invitationInfo.user.username}" class="text-black hover:text-sky-900">
                        ${invitationInfo.user.username}
                    </a>
                </h3>
                <p class="text-sm text-gray-600">Sent ${invitationInfo.createddate || 'Date unavailable'}</p>
            </div>
            <button type="button" class="cancel-btn text-red-500 hover:text-red-700 ml-2" 
                    data-id="${invitationInfo.invitationid}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    `;

    return invitation;
}

function insertMoreInvitations(element, invitations) {
    for (let i = 0; i < invitations.data.length; i++) {
        let invitationElement = createInvitation(invitations.data[i]);
        element.appendChild(invitationElement);
    }
}

function createRequest(requestInfo) {
    let request = document.createElement('div');
    request.classList.add("request", "border-b", "border-gray-300", "p-4", "bg-white");

    if (!requestInfo.user) {
        console.error("User data is missing in requestInfo:", requestInfo);
        request.innerHTML = `<p>Error: User information is unavailable.</p>`;
        return request;
    }

    request.innerHTML = `
        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-bold">
                    <a href="../profile/${requestInfo.user.username}" class="text-black hover:text-sky-900">
                        ${requestInfo.user.username}
                    </a>
                </h3>
                <p class="text-sm text-gray-600">Request received: ${requestInfo.createddate || 'Date unavailable'}</p>
            </div>
            <div class="flex space-x-2">
                <button type="button" class="accept-btn bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-700" 
                        data-id="${requestInfo.requestid}">
                    Accept
                </button>
                <button type="button" class="reject-btn bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-700" 
                        data-id="${requestInfo.requestid}">
                    Reject
                </button>
            </div>
        </div>
    `;

    return request;
}

function insertMoreRequests(element, requests) {
    for (let i = 0; i < requests.data.length; i++) {
        let requestElement = createRequest(requests.data[i]);
        element.appendChild(requestElement);
    }
}

//creates a member container with all the necessary info
function createMember(memberInfo) {
    let member = document.createElement('div');
    member.classList.add("member", "border-b", "border-gray-300", "p-4", "bg-white");

    const canRemove = (parseInt(memberInfo.userid) !== parseInt(ownerid)) && ((userId === parseInt(ownerid)) || isadmin);

    // Member card structure
    member.innerHTML = `
        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-bold">
                    <a href="../profile/${memberInfo.username}" class="text-black hover:text-sky-900">
                        ${memberInfo.username}
                    </a>
                </h3>
                <p class="text-sm text-gray-600">${memberInfo.bio || ''}</p>
            </div>
            ${canRemove ? `
                <button type="button" onclick="openRemoveMemberMenu(${memberInfo.userid}, '${memberInfo.username}')" 
                        class="text-red-500 hover:text-red-700 ml-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            ` : ''}
        </div>
    `;

    return member;
}
    
//inserts more members into an element
function insertMoreMembers(element, members){
    for(let i = 0; i < members.data.length; i++){
      let member = createMember(members.data[i]);
      element.appendChild(member);
    }
}

addEventListeners();
