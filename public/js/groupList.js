function addEventListeners() {
    document.addEventListener('DOMContentLoaded', fadeAlert);
    window.addEventListener("scroll", infiniteScroll);
    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('accept-invite')) {
            const invitationId = e.target.dataset.id;
            const groupId = e.target.closest('.invitation').getAttribute('data-group-id');
    
            if (!groupId || !invitationId) {
                console.error('Group ID or Invitation ID is missing.');
                return;
            }
    
            sendAjaxRequest('post', `/groups/${groupId}/invitations/${invitationId}/accept`, {}, function () {
                if (this.status === 200) {
                    const response = JSON.parse(this.responseText);
    
                    const invitationElement = e.target.closest('.invitation');
                    if (invitationElement) invitationElement.remove();
                    
                    loadSearchGroupContent('manage-invitations','');
                    alert(response.message);
                } else {
                    console.error('Failed to accept invitation:', this.responseText);
                }
            });
        }
    
        if (e.target && e.target.classList.contains('reject-invite')) {
            const invitationId = e.target.dataset.id;
            const groupId = e.target.closest('.invitation').getAttribute('data-group-id');
    
            if (!groupId || !invitationId) {
                console.error('Group ID or Invitation ID is missing.');
                return;
            }
    
            sendAjaxRequest('post', `/groups/${groupId}/invitations/${invitationId}/reject`, {}, function () {
                if (this.status === 200) {
                    const response = JSON.parse(this.responseText);
    
                    const invitationElement = e.target.closest('.invitation');
                    if (invitationElement) invitationElement.remove();
                    
                    loadSearchGroupContent('manage-invitations','');
                    alert(response.message);
                } else {
                    console.error('Failed to reject invitation:', this.responseText);
                }
            });
        }
    });    
}

let searchGroupCategory = null;
if(document.querySelector('input[name="category"]') !== null) {
    searchGroupCategory = document.querySelector('input[name="category"]').value;
}

function changeGroupCategory(category) {
    currentPage = 1; 
    searchGroupCategory = category;
    document.querySelector('input[name="category"]').value = category;

    const buttons = document.querySelectorAll('.category-btn');
    buttons.forEach(button => {
        if (button.dataset.category === category) {
            button.classList.add('text-sky-900', 'border-sky-900');
        } else {
            button.classList.remove('text-sky-900', 'border-sky-900');
        }
    });

    query = document.querySelector('input[name="q"]').value || '';
    loadSearchGroupContent(category, query);
}

// Loads the search content based on the selected category
function loadSearchGroupContent(category, query) {
    const groupResults = document.querySelector("#group-results");

    while (groupResults.firstChild) {
        groupResults.removeChild(groupResults.firstChild);
    }

    insertLoadingCircle(groupResults);
    sendAjaxRequest('get', '/groups?page=' + currentPage + "&q=" + query + "&category=" + category, null, insertMoreGroupSearchResults);
}

function insertMoreGroupSearchResults() {
    removeLoadingCircle();  
    const groupResults = document.querySelector("#group-results");

    let results = JSON.parse(this.responseText); 

    switch (searchGroupCategory) {
        case 'your-groups':
            if(results[0] === undefined) {
                break;
            }
            maxPage = results[0].lastPage;
            insertMoreGroups(groupResults, results[0]);
            break;

        case 'manage-invitations':
            if(results[2] === undefined) {
                break;
            }
            maxPage = results[2].lastPage;
            insertMoreInvitationsGroupList(groupResults, results[2]);
            break;

        case 'search-groups':
            if (results[1] === undefined) {
                break;
            }
            maxPage = results[1].lastPage;
            insertMoreGroups(groupResults, results[1]);
            break;

        default:
            return;
    }

    if(groupResults.firstChild == null) {
        groupResults.innerHTML = `
          <div class="flex justify-center items-center h-32">
              <p class="text-gray-600 text-center">No groups found matching your search.</p>
          </div>
        `;       
    }
}

function createInvitationGroupList(invitationInfo) {
    let invitation = document.createElement('div');
    invitation.classList.add("invitation", "border-b", "border-gray-300", "p-4", "bg-white");

    // Ensure `groupId` is available
    invitation.setAttribute('data-group-id', invitationInfo.group.groupid);

    if (!invitationInfo.user) {
        console.error("User data is missing in invitationInfo:", invitationInfo);
        invitation.innerHTML = `<p>Error: User information is unavailable.</p>`;
        return invitation;
    }

    invitation.innerHTML = `
        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-bold">
                    <a href="../profile/${invitationInfo.group.groupname}" class="text-black hover:text-sky-900">
                        ${invitationInfo.group.groupname}
                    </a>
                </h3>
                <p class="text-sm text-gray-600">Sent ${invitationInfo.createddate || 'Date unavailable'}</p>
            </div>
            <div class="flex space-x-2">
                <button type="button" class="accept-invite bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-700" 
                        data-id="${invitationInfo.invitationid}">
                    Accept
                </button>
                <button type="button" class="reject-invite bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-700" 
                        data-id="${invitationInfo.invitationid}">
                    Reject
                </button>
            </div>
        </div>
    `;

    return invitation;
}

function insertMoreInvitationsGroupList(element, invitations) {
    for (let i = 0; i < invitations.data.length; i++) {
        let invitationElement = createInvitationGroupList(invitations.data[i]);
        element.appendChild(invitationElement);
    }
}

function toggleCreateGroupMenu() {
    const createGroupMenu = document.getElementById('create-group-menu');
    createGroupMenu.classList.toggle('hidden');
    createGroupMenu.classList.toggle('flex');
}

addEventListeners();
