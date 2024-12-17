 
 function addEventListeners() {
  document.addEventListener('DOMContentLoaded', fadeAlert);

  let cancelButton = document.getElementById('cancelButtonAdmin');

  if(cancelButton !== null){
    cancelButton.addEventListener('click', () => {
    const deleteMenu = document.getElementById('deleteMenuAdmin');
    deleteMenu.classList.toggle('hidden');
    deleteMenu.classList.toggle('flex');
  });
}
  let confirmButton = document.getElementById('confirmButtonAdmin');
  if(confirmButton !== null){
    confirmButton.addEventListener('click', () => {
      insertLoadingCircle(confirmButton);
      
      //resize the loading circle
      document.querySelector('#loading_circle').classList.remove('h-8');
      document.querySelector('#loading_circle').classList.remove('w-8');
      document.querySelector('#loading_circle').classList.add('h-4');
      document.querySelector('#loading_circle').classList.add('w-4');

      if(window.categoryDelete == 'topics'){
        sendAjaxRequest('post', '/api/topics/delete/' + window.elementToDelete, null, handleTopicDelete);
      }
      if(window.categoryDelete == 'reports'){
        sendAjaxRequest('post', '/api/reports/delete/' + window.elementToDelete, null, handleReportDelete);
      }

    });
  }
  createTopicListener();
  createUserListener()

  document.addEventListener('DOMContentLoaded', function() {
    handlePagination('posts-container');
    handlePagination('users-container');
  });

  document.addEventListener('DOMContentLoaded', handleDeleteFormSubmission);


  setupCreateUserMenu();
  setupCreateTopicMenu()

  addEventListenerEditUserAdmin();
  eventListernerFormsAdmin();
  
}

function createTopicListener(){
  let createTopic = document.getElementById('createTopicForm');

  createTopic?.addEventListener('submit', (form) => {
    form.preventDefault();
    let topicname = document.getElementById('create-Topic').value;

    let confirmButton = document.getElementById('submitCreateTopicBtn');
    insertLoadingCircle(confirmButton);
        
    //resize the loading circle
    document.querySelector('#loading_circle').classList.remove('h-8');
    document.querySelector('#loading_circle').classList.remove('w-8');
    document.querySelector('#loading_circle').classList.add('h-4');
    document.querySelector('#loading_circle').classList.add('w-4');

    sendAjaxRequest('post', '/api/topics/add', {"topicname": topicname}, handleCreateTopic);
  });
}

function createUserListener(){
  let createUser = document.getElementById('createUserForm');

  createUser?.addEventListener('submit', (form) => {
    form.preventDefault();
    let username = document.getElementById('create-username').value;
    let email = document.getElementById('create-email').value;
    let password = document.getElementById('create-password').value;
    let passwordConf = document.getElementById('create-password_confirmation').value;

    let confirmButton = document.getElementById('submitCreateUserBtn');
    insertLoadingCircle(confirmButton);
        
    //resize the loading circle
    document.querySelector('#loading_circle').classList.remove('h-8');
    document.querySelector('#loading_circle').classList.remove('w-8');
    document.querySelector('#loading_circle').classList.add('h-4');
    document.querySelector('#loading_circle').classList.add('w-4');

    sendAjaxRequest('post', '/api/admin/users/create', {"username": username, "email": email, "password" : password, "password_confirmation" : passwordConf}, handleCreateUser);
  });
}

function handleCreateTopic(){
  let createTopicMenu = document.getElementById('createTopicMenu');
  createTopicMenu.classList.toggle('hidden');
  createTopicMenu.classList.toggle('flex');

  removeLoadingCircle();
  const response = JSON.parse(this.responseText);

  const messageContainer = document.getElementById("messageContainer");
  if(response.response === '200'){
    const section = document.getElementById('topics');  
    const sectionContentTable = section.querySelector('table');
    createAlert(messageContainer, response.message,false);
    topic = createAdminTopic(response.topicname, response.topicid);
    sectionContentTable.appendChild(topic);
  }
  else{
    createAlert(messageContainer, response.message,true);
  }

}

function handleCreateUser(){
  let createUserMenu = document.getElementById('createUserMenu');
  createUserMenu.classList.toggle('hidden');
  createUserMenu.classList.toggle('flex');

  removeLoadingCircle();
  const response = JSON.parse(this.responseText);

  const messageContainer = document.getElementById("messageContainer");
  if(response.response === '200'){
    createAlert(messageContainer, response.message,false);
  }
  else{
    createAlert(messageContainer, response.message,true);
  }
}

function handleReportDelete(){
  let deleteMenu = document.getElementById('deleteMenuAdmin');
  deleteMenu.classList.toggle('hidden');
  deleteMenu.classList.toggle('flex');
  removeLoadingCircle();
  const response = JSON.parse(this.responseText);
    
    const messageContainer = document.getElementById("messageContainer");
    if(response.response === '200'){
      createAlert(messageContainer, response.message,false);
      document.getElementById('Report-' + window.elementToDelete).remove();
    }
    else{
      createAlert(messageContainer, response.message,true);
    }

}

function handleTopicDelete(){
  let deleteMenu = document.getElementById('deleteMenuAdmin');
  deleteMenu.classList.toggle('hidden');
  deleteMenu.classList.toggle('flex');
  removeLoadingCircle();
  const response = JSON.parse(this.responseText);
    
  const messageContainer = document.getElementById("messageContainer");
  if(response.response === '200'){
    createAlert(messageContainer, response.message,false);
    document.getElementById('Topic-' + window.elementToDelete).remove()
  }
  else{
    createAlert(messageContainer, response.message,true);
  }


}
 
 
 //used to switch from section in the admin page(only for the final product)
  function showSectionAdmin(sectionId) {
    
      document.querySelectorAll('.tab-section').forEach((el) => {
          el.classList.add('hidden');
          el.classList.remove('flex');
      });

      document.getElementById(sectionId).classList.remove('hidden');
      document.getElementById(sectionId).classList.add('flex');

      const section = document.getElementById(sectionId);  
      const sectionContentTable = section.querySelector('table');
      if(sectionContentTable == null){
        return;
      }
      while(sectionContentTable.firstChild){
        sectionContentTable.firstChild.remove();
      }

      maxAdminPage = -1;
      currentAdminPage = 0;
      loadMoreAdminContent(sectionId);
      
    }

  
  function showDeleteAdminMenu(elementId, category){
    let deleteMenu = document.getElementById('deleteMenuAdmin');
    deleteMenu.querySelector('h2').innerHTML = `Delete ${category == 'topic' ? 'Topic' : 'Report'}`
    deleteMenu.querySelector('p').innerHTML = `Are you sure you want to delete this ${category == 'topics' ? 'topic' : 'report'}? This action cannot be undone.`
    deleteMenu.classList.toggle('hidden');
    deleteMenu.classList.toggle('flex');
    window.categoryDelete = category;
    window.elementToDelete = elementId;
  }


  function insertShowMoreAdmin(sectionId){
    const section = document.getElementById(sectionId);  
    let showMore = document.createElement('button');
    showMore.classList.add("flex", "w-full", "justify-center", "items-center");
    showMore.setAttribute('onclick', `loadMoreAdminContent('${sectionId}')`);
    showMore.setAttribute('id', 'showMore');
    showMore.innerHTML = `
                <svg class="-rotate-90 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <p>Show More</p>
    `;
    section.appendChild(showMore);
  }

  function removeShowMoreAdmin(){
    document.getElementById('showMore')?.remove();
  }

  

  function insertMoreReports(){
    removeLoadingCircle();
    const loadingWrapper = document.getElementById('loadingWrapper');
    loadingWrapper.remove();

    let reports =  JSON.parse(this.responseText);
    maxAdminPage = reports.last_page;
    const section = document.getElementById('reports');  
    const sectionContentTable = section.querySelector('table');

    if(document.querySelector('th') === null){
      let header = document.createElement('tr');
      header.classList.add("shadow", "font-medium");
      header.innerHTML = `
      <th class = "w-1/4 text-start px-4 py-2" >Content</th>
      <th class = "w-1/4 text-start px-4 py-2" >Reason</th>
      <th class = "w-1/4 text-start px-4 py-2" >Reported By</th>
      <th></th>
      `;
      sectionContentTable.appendChild(header);
    }

    

    for(let i = 0; i < reports.data.length; i++){
      let row = document.createElement('tr');
      row.setAttribute('id', 'Report-' + reports.data[i].reportid);
      row.classList.add("shadow", "font-medium");
      row.innerHTML = `
        <td class="w-1/3 px-4 py-2 text-gray-700">
          <a href = '/posts/${reports.data[i].postid}'>
            ${reports.data[i].commentid === null ? `Post ID${reports.data[i].postid}` : `Comment ID${reports.data[i].commentid}`}
          </a>
        </td>
        <td class="w-1/3 px-4 py-2 text-gray-700 truncate ...">${reports.data[i].reason}</td>
        <td class="w-1/3 px-4 py-2 text-gray-700">${reports.data[i].user.username}</td>
         <td class="px-4 py-2 self-end">
        <form action="../reports/delete/${reports.data[i].reportid}" method="POST" id="deleteForm-${reports.data[i].reportid}">
          <input type="hidden" name="_token" value= ${getCsrfToken()} />
          <button type="button" onclick="showDeleteAdminMenu(${reports.data[i].reportid}, 'reports')" class="text-red-500 hover:text-red-700 ml-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
          </button>
        </form>
      </td>

      `
      sectionContentTable.appendChild(row);
    }

    if(currentAdminPage < maxAdminPage){
      insertShowMoreAdmin('reports'); 
    }

  }

  function createAdminTopic(topicname, topicid){
    let row = document.createElement('tr');
      row.setAttribute('id', 'Topic-' + topicid);
      row.classList.add("flex", "w-full", "shadow", "font-medium");
      row.innerHTML = `
      <td class="grow px-4 py-2 text-gray-700">${topicname}</td>
      <td class="px-4 py-2 self-end">
        <form action="../topics/delete/${topicid}" method="POST" id="deleteForm-${topicid}">
          <input type="hidden" name="_token" value= ${getCsrfToken()} />
          <button type="button" onclick="showDeleteAdminMenu(${topicid}, 'topics')" class="text-red-500 hover:text-red-700 ml-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
          </button>
        </form>
      </td>
      `

      return row

  }

  function insertMoreAdminTopics(){
    removeLoadingCircle();
    const loadingWrapper = document.getElementById('loadingWrapper');
    loadingWrapper.remove();

    const section = document.getElementById('topics');  
    const sectionContentTable = section.querySelector('table');
    
    

    let topics =  JSON.parse(this.responseText);
    maxAdminPage = topics.last_page;

    for(let i = 0; i < topics.data.length; i++){

      //ignore the default topic 
      if(topics.data[i].topicid == 1){
        continue;
      }

      let row = createAdminTopic(topics.data[i].topicname, topics.data[i].topicid);
      sectionContentTable.appendChild(row);
    }

    if(currentAdminPage < maxAdminPage){
      insertShowMoreAdmin('topics');
    }
  }
  
  let maxAdminPage = -1;
  let currentAdminPage = 0;
  function loadMoreAdminContent(sectionId){

      if((maxAdminPage < currentPage) && (maxAdminPage != -1)){
        return;
      }

      removeShowMoreAdmin();

      const section = document.getElementById(sectionId);  
      const sectionContentTable = section.querySelector('table');
      const loadingWrapper = document.createElement('div');
      loadingWrapper.setAttribute('id', 'loadingWrapper')
      loadingWrapper.classList.add("flex", "justify-center", "items-center", "h-32","w-full");
      insertLoadingCircle(loadingWrapper);
      sectionContentTable.appendChild(loadingWrapper);
      currentAdminPage++;

      switch(sectionId){
        case 'topics':
          if(isQuery){
            sendAjaxRequest('get', '/api/topics/search/all?page=' + currentAdminPage + "&q=" + searchQuery, null, insertMoreAdminTopics);
          }
          else{
            sendAjaxRequest('get', '/api/topics/all?page=' + currentAdminPage, null, insertMoreAdminTopics);
          }
          break;
        case 'reports':
          sendAjaxRequest('get', '/api/reports/all?page=' + currentAdminPage, null, insertMoreReports);
          break;
      }
    }

  //Admin Edit User
  document.querySelectorAll('.edit-user-button').forEach(button => {
    button.addEventListener('click', function() {
        const userId = this.getAttribute('data-user-id');

        sendAjaxRequest('get',`/admin/users/${userId}/edit`, null, adminEditUser);
    })
  });

  function searchAdmin(event, sectionId){
    event.preventDefault();

    currentAdminPage = 0;
    isQuery = true;
    searchQuery = document.querySelector(`#${sectionId}AdminSearch`).value;
  
     //cancel the search if there is not a query
     if(searchQuery == ""){
      isQuery = false;
    }
  
    //remove the existing topics from the list that is being displayed to the user 
    const section = document.getElementById(sectionId);  
    const sectionContentTable = section.querySelector('table');
    if(sectionContentTable == null){
      return;
    }
    while(sectionContentTable.firstChild){
      sectionContentTable.firstChild.remove();
    }
  
    loadMoreAdminContent(sectionId);
  }

  //loads the edit user form when receive a positive response from the server
function adminEditUser(){
  let data = JSON.parse(this.responseText);
  if(data.success){
    document.getElementById('editUserId').value = data.user.userid;
    document.getElementById('editUsername').value = data.user.username;
    document.getElementById('editEmail').value = data.user.email;
    document.getElementById('editState').value = data.user.state;
    document.getElementById('editVisibility').value = data.user.visibilitypublic;
    document.getElementById('editAdmin').value = data.user.isadmin;

    document.getElementById('editUserModal').classList.remove('hidden');
  }
  else{
    alert('Error loading user data');
  }
}


//event listeners for the forms in the admin page
function eventListernerFormsAdmin(){
  if(document.getElementById('editUserForm') === null){
    return;
  }
  document.getElementById('editUserForm').addEventListener('submit', function(event) {
    event.preventDefault();

  const formData = new FormData(this);

  let dataToSend = {};
  for (let [name, value] of formData) {
      dataToSend[name] = value;
  }

    sendAjaxRequest('post',`/admin/users/${document.getElementById('editUserId').value}`, dataToSend, updateDataEditProfile );
  });
}

//updates the data in admin pages when a profile is edit (Only for the final product)
function updateDataEditProfile(){
  let data = JSON.parse(this.responseText);
    if (data.success) {
      const row = document.querySelector(`tr[data-user-id="${data.user.userid}"]`);
      row.querySelector('.username').textContent = data.user.username;
      row.querySelector('.email').textContent = data.user.email;
      row.querySelector('.state').textContent = data.user.state;
      row.querySelector('.visibility').textContent = data.user.visibilitypublic === 1 ? 'Public' : 'Private';
      row.querySelector('.admin').textContent = data.user.isadmin ? 'Admin' : 'User';

      document.getElementById('editUserModal').classList.add('hidden');
    } else {
      alert('Error saving user data');
    }

}


//event listener for the admmin edit user profile
function addEventListenerEditUserAdmin(){
  if(document.getElementById('closeModalBtn') === null){
    return;
  }
  document.getElementById('closeModalBtn').addEventListener('click', function() {
    document.getElementById('editUserModal').classList.add('hidden');
  });
}

// Admin Delete User
function deleteUser(userId) {
  if (confirm("Are you sure you want to delete this user?")) {
      fetch(`/admin/users/${userId}`, {
          method: 'DELETE',
          headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
      })
      .then(response => response.json())
      .then(data => {
          if (data.success) {
              alert(data.message); 
              document.getElementById(`user-${userId}`).remove();
              
              window.location.href = data.redirect_url; 
          } else {
              alert('Error deleting user');
          }
      })
      .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while deleting the user');
      });
  }
}


//Admin Delete Post
document.querySelectorAll('.delete-post-button').forEach(button => {
  button.addEventListener('click', function(event) {
      const postId = event.target.getAttribute('data-post-id');
      const postMessage = event.target.getAttribute('data-post-message');

      openDeleteMenu(postId, postMessage);
  });
});


//Admin Create User
function setupCreateUserMenu() {
  const createUserBtn = document.getElementById("createUserBtn");

  if(createUserBtn === null){
    return;
  }
  const createUserMenu = document.getElementById("createUserMenu");
  const cancelCreateUserBtn = document.getElementById("cancelCreateUserBtn");


  if (createUserBtn && createUserMenu && cancelCreateUserBtn) {
    createUserBtn.addEventListener("click", () => {
      createUserMenu.classList.toggle("hidden");
      createUserMenu.classList.toggle("flex");
      
    });

    document.addEventListener("click", (event) => {
      if (!createUserMenu.contains(event.target) && event.target !== createUserBtn) {
        createUserMenu.classList.add("hidden");
        createUserMenu.classList.toggle("flex");
      }
    });

    cancelCreateUserBtn.addEventListener("click", () => { 
      createUserMenu.classList.add("hidden");
      createUserMenu.classList.toggle("flex");
    });
  } else {
    console.error("One or more elements are missing. Check your HTML."); 
  }
}

//Admin Create Topic
function setupCreateTopicMenu() {
  const createTopicBtn = document.getElementById("createTopicBtn");

  if(createTopicBtn === null){
    return;
  }
  const createTopicMenu = document.getElementById("createTopicMenu");
  const cancelCreateTopicBtn = document.getElementById("cancelCreateTopicBtn");

  if (createTopicBtn && createTopicMenu && cancelCreateTopicBtn) {
    createTopicBtn.addEventListener("click", () => {
      createTopicMenu.classList.toggle("hidden");
      createTopicMenu.classList.add("flex");
      
    });

    document.addEventListener("click", (event) => {
      if (!createTopicMenu.contains(event.target) && event.target !== createTopicBtn) {
        createTopicMenu.classList.add("hidden");
        createTopicMenu.classList.toggle("flex");
      }
    });

    cancelCreateTopicBtn.addEventListener("click", () => { 
      createTopicMenu.classList.add("hidden");
      createTopicMenu.classList.toggle("flex");
    });
  } else {
    console.error("One or more elements are missing. Check your HTML."); 
  }
}

//handles the delete forms in the admins (Maybe used for the final product)
function handleDeleteFormSubmission() {
  const deleteForm = document.getElementById('deleteForm');
  if (!deleteForm) return;

  deleteForm.addEventListener('submit', function(event) {
      event.preventDefault();

      const postIdInput = document.getElementById('postId');
      const postId = postIdInput.value;

      const formData = new FormData(deleteForm);
      formData.set('post_id', postId);

      fetch(deleteForm.action, {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest', 
            'Accept': 'application/json',         
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
          }
      })
      .then(response => response.json())
      .then(data => {
          if (data.success) {
              const postElement = document.getElementById(`post-${postId}`);
              postElement?.remove();
              closeDeleteMenu();
          } else {
              alert('Error deleting post!'  + data.message);
          }
      })
      .catch(error => {
          console.error('Error:', error);
          alert('Error deleting post!');
      });
  });
}

// Admin Page Pagination
function handlePagination(containerId) {
  const container = document.getElementById(containerId);

  if (!container) return;

  container.addEventListener('click', function (e) {
    if (e.target.classList.contains('pagination-link')) {
      e.preventDefault();

      const url = e.target.href;

      fetch(url, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
        }
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Error loading page.');
        }
        return response.text();
      })
      .then(html => {
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;

        const newContainer = tempDiv.querySelector(`#${containerId}`);
        const newPagination = tempDiv.querySelector('.pagination');

        container.innerHTML = newContainer ? newContainer.innerHTML : '';
        container.querySelector('.pagination').innerHTML = newPagination.innerHTML;

        const activeButton = newPagination.querySelector('.pagination-link.active');
      })
      .catch(error => {
        console.error('Erro:', error);
      });
    }
  });
}

addEventListeners();