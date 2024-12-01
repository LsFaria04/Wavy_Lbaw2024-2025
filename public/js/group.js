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
// Creates a new group container with all the needed info
function createGroup(groupInfo) {
    let group = document.createElement('div');
    group.classList.add("group", "mb-4", "p-4", "bg-white", "rounded-md", "shadow-md");
  
    group.innerHTML = `
      <div class="group-header mb-2">
        <h3 class="font-bold">
          <a href="/group/${groupInfo.groupid}" class="text-black hover:text-sky-900">
            ${groupInfo.groupname}
          </a>
        </h3>
      </div>
      <div class="group-body mb-2">
        <p>${groupInfo.description}</p>
      </div>
    `;
  
    return group;
  }

  //inserts more groups into and element
function insertMoreGroups(element, groups){
    for(let i = 0; i < groups.data.length; i++){
      let group = createGroup(groups.data[i]);
      element.appendChild(group);
    
    }
    }
  addEventListeners();