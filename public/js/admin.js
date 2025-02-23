function addEventListeners() {
  cancelButtonListener();
  confirmButtonListener();
  cancelBanListener();
  confirmBanListener();
  

  createTopicListener();
  createUserListener()

  setupCreateUserMenu();
  setupCreateTopicMenu();

  currentAdminPage = 1;
}

//Admin menu setups ---------------------------------------------------------------------

//Admin Create User
function setupCreateUserMenu() {
  const createUserBtn = document.getElementById("createUserBtn");

  if(createUserBtn === null) {
    return;
  }
  const createUserMenu = document.getElementById("createUserMenu");
  const cancelCreateUserBtn = document.getElementById("cancelCreateUserBtn");


  if (createUserBtn && createUserMenu && cancelCreateUserBtn) {
    createUserBtn.addEventListener("click", () => {
      createUserMenu.classList.toggle("hidden");
      createUserMenu.classList.add("flex");
      
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

  if(createTopicBtn === null) {
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

//admin Listeners ----------------------------------------------------------------------------

//Adds a listener to the submission of the create topic form. Performs a ajax request instead of the default request
function createTopicListener() {
  let createTopic = document.getElementById('createTopicForm');

  createTopic?.addEventListener('submit', (form) => {
    form.preventDefault();
    let topicname = document.getElementById('create-Topic').value;

    let confirmButton = document.getElementById('submitCreateTopicBtn');
    confirmButton.disable = true;

    insertLoadingCircle(confirmButton);
        
    //resize the loading circle
    document.querySelector('#loading_circle').classList.remove('h-8');
    document.querySelector('#loading_circle').classList.remove('w-8');
    document.querySelector('#loading_circle').classList.add('h-4');
    document.querySelector('#loading_circle').classList.add('w-4');

    sendAjaxRequest('post', '/api/topics/add', {"topicname": topicname}, handleCreateTopic);
  });
}

//Adds a listener to the submission of the create user form. Performs a ajax request instead of the default request
function createUserListener() {
  let createUser = document.getElementById('createUserForm');

  createUser?.addEventListener('submit', (form) => {
    form.preventDefault();
    let username = document.getElementById('create-username').value;
    let email = document.getElementById('create-email').value;
    let password = document.getElementById('create-password').value;
    let passwordConf = document.getElementById('create-password_confirmation').value;

    let confirmButton = document.getElementById('submitCreateUserBtn');
    confirmButton.disable = true;

    insertLoadingCircle(confirmButton);
        
    //resize the loading circle
    document.querySelector('#loading_circle').classList.remove('h-8');
    document.querySelector('#loading_circle').classList.remove('w-8');
    document.querySelector('#loading_circle').classList.add('h-4');
    document.querySelector('#loading_circle').classList.add('w-4');

    sendAjaxRequest('post', '/api/admin/users/create', {"username": username, "email": email, "password" : password, "password_confirmation" : passwordConf}, handleCreateUser);
  });
}

//adds a listener to the confirmation button. Performs a ajax request for deletion depending on the category selected
function confirmButtonListener() {
  let confirmButton = document.getElementById('confirmButtonAdmin');
  if(confirmButton !== null) {
    confirmButton.addEventListener('click', () => {
      insertLoadingCircle(confirmButton);

      confirmButton.disable = true;
      
      //resize the loading circle
      document.querySelector('#loading_circle').classList.remove('h-8');
      document.querySelector('#loading_circle').classList.remove('w-8');
      document.querySelector('#loading_circle').classList.add('h-4');
      document.querySelector('#loading_circle').classList.add('w-4');

      if(window.categoryDelete == 'topics') {
        sendAjaxRequest('post', '/api/topics/delete/' + window.elementToDelete, null, handleTopicDelete);
      }
      if(window.categoryDelete == 'reports') {
        sendAjaxRequest('post', '/api/reports/delete/' + window.elementToDelete, null, handleReportDelete);
      }
      if(window.categoryDelete == 'users') {
        sendAjaxRequest('post', '/api/profile/' + window.elementToDelete + '/delete', null, handleUserDelete);
      }

    });
  }
}

//adds a listener to the cancel button. Hides the menu and cancels the delete action
function cancelButtonListener() {
  let cancelButton = document.getElementById('cancelButtonAdmin');
  if(cancelButton !== null) {
    cancelButton.addEventListener('click', () => {
    const deleteMenu = document.getElementById('deleteMenuAdmin');
    deleteMenu.classList.toggle('hidden');
    deleteMenu.classList.toggle('flex');
    });
  }
}

function confirmBanListener() {
  let confirmButton = document.getElementById('confirmBanButtonAdmin');
  if(confirmButton !== null) {
    confirmButton.addEventListener('click', () => {
      confirmButton.disable = true;
      insertLoadingCircle(confirmButton);
      
      //resize the loading circle
      document.querySelector('#loading_circle').classList.remove('h-8');
      document.querySelector('#loading_circle').classList.remove('w-8');
      document.querySelector('#loading_circle').classList.add('h-4');
      document.querySelector('#loading_circle').classList.add('w-4');

      sendAjaxRequest('post','/api/admin/users/ban/' + window.userBan, null, handleBan);

    });
  }
}

function cancelBanListener() {
  let cancelButton = document.getElementById('cancelBanButtonAdmin');
  if(cancelButton !== null) {
    cancelButton.addEventListener('click', () => {
    const banMenu = document.getElementById('banMenuAdmin');
    banMenu.classList.toggle('hidden');
    banMenu.classList.toggle('flex');
    });
  }
}



//Admin ajax response handlers ---------------------------------------------------------------------------------

function handleBan() {
  let banMenu = document.getElementById('banMenuAdmin');
  banMenu.classList.toggle('hidden');
  banMenu.classList.toggle('flex');
  removeLoadingCircle();
  const response = JSON.parse(this.responseText);

  let confirmButton = document.getElementById('confirmBanButtonAdmin');
  confirmButton.disable = false;
    
    const messageContainer = document.getElementById("messageContainer");
    if(response.response === '200') {
      createAlert(messageContainer, response.message,false);
      const row = document.querySelector('#User-' + window.userBan + ' .userState');
      if(userState !== null) {
        //ban was made from the profile page
        document.getElementById('profileBan').innerHTML=`${userState === "suspended" ? "Ban account" : "Unban account"}`
        if(userState === "suspended") {
          userState = "active";
        }
        else {
          userState = "suspended";
        }
      }
      else if(row.innerHTML === "suspended") {
        row.innerHTML = "active";
        document.querySelector('#User-' + window.userBan + ' .banButton').innerHTML = 'Ban';
      }
      else {
        row.innerHTML = "suspended";
        document.querySelector('#User-' + window.userBan + ' .banButton').innerHTML = 'Unban';
      }
    }
    else {
      createAlert(messageContainer, response.message,true);
    }

}
//handles the responses from the requests related to the topic creation. Displays a message with the request result
function handleCreateTopic() {
  let createTopicMenu = document.getElementById('createTopicMenu');
  createTopicMenu.classList.toggle('hidden');
  createTopicMenu.classList.toggle('flex');

  removeLoadingCircle();
  const response = JSON.parse(this.responseText);

  let confirmButton = document.getElementById('submitCreateTopicBtn');
  confirmButton.disable = false;

  const messageContainer = document.getElementById("messageContainer");
  if(response.response === '200') {
    const section = document.getElementById('topics');  
    const sectionContentTable = section.querySelector('table');
    createAlert(messageContainer, response.message,false);
    if(sectionContentTable.childElementCount < 9) {
      topic = createAdminTopic(response.topicname, response.topicid);
      sectionContentTable.appendChild(topic);
    }
  }
  else {
    createAlert(messageContainer, response.message,true);
  }

}

//handles the responses from the requests related to the user creation. Displays a message with the request result
function handleCreateUser() {
  let createUserMenu = document.getElementById('createUserMenu');
  createUserMenu.classList.toggle('hidden');
  createUserMenu.classList.toggle('flex');

  removeLoadingCircle();
  const response = JSON.parse(this.responseText);

  let confirmButton = document.getElementById('submitCreateUserBtn');
  confirmButton.disable = false;

  const messageContainer = document.getElementById("messageContainer");
  if(response.response === '200') {
    createAlert(messageContainer, response.message,false);
  }
  else {
    createAlert(messageContainer, response.message,true);
  }
}

//handles the responses from the requests related to the reports deletion. Displays a message with the request result
function handleReportDelete() {
  let deleteMenu = document.getElementById('deleteMenuAdmin');
  deleteMenu.classList.toggle('hidden');
  deleteMenu.classList.toggle('flex');
  removeLoadingCircle();
  const response = JSON.parse(this.responseText);

  let confirmButton = document.getElementById('confirmButtonAdmin');
  confirmButton.disable = false;
    
    const messageContainer = document.getElementById("messageContainer");
    if(response.response === '200') {
      createAlert(messageContainer, response.message,false);
      document.getElementById('Report-' + window.elementToDelete).remove();
    }
    else {
      createAlert(messageContainer, response.message,true);
    }

}

function handleUserDelete() {
  let deleteMenu = document.getElementById('deleteMenuAdmin');
  deleteMenu.classList.toggle('hidden');
  deleteMenu.classList.toggle('flex');
  removeLoadingCircle();
  const response = JSON.parse(this.responseText);

  let confirmButton = document.getElementById('confirmButtonAdmin');
  confirmButton.disable = false;

  const messageContainer = document.getElementById("messageContainer");
    if(response.response === '200') {
      createAlert(messageContainer, response.message,false);
      document.getElementById('User-' + window.elementToDelete).remove();
    }
    else {
      createAlert(messageContainer, response.message,true);
    }

}

//handles the responses from the requests related to the topics deletion. Displays a message with the request result
function handleTopicDelete() {
  let deleteMenu = document.getElementById('deleteMenuAdmin');
  deleteMenu.classList.toggle('hidden');
  deleteMenu.classList.toggle('flex');
  removeLoadingCircle();
  const response = JSON.parse(this.responseText);

  let confirmButton = document.getElementById('confirmButtonAdmin');
  confirmButton.disable = false;
    
  const messageContainer = document.getElementById("messageContainer");
  if(response.response === '200') {
    createAlert(messageContainer, response.message,false);
    document.getElementById('Topic-' + window.elementToDelete).remove()
  }
  else {
    createAlert(messageContainer, response.message,true);
  }


}

//Admin menu toggles ---------------------------------------------------------------------------------------------
 
function toggleReasonDetails(reportid) {
  const reasonDetailsText = document.querySelector("#reasonDetails textarea ");
  const reasonDetails = document.querySelector("#reasonDetails");
  if(reportid !== null) {
  const report =  document.getElementById("Report-" + reportid);
  const reason = report.querySelector(".reportReason").innerHTML;

  
  reasonDetailsText.innerHTML = reason;
  }
 reasonDetails.classList.toggle('hidden');
 reasonDetails.classList.toggle('flex');
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
      if(sectionContentTable == null) {
        return;
      }
      while(sectionContentTable.firstChild) {
        sectionContentTable.firstChild.remove();
      }

      maxAdminPage = -1;
      currentAdminPage = 0;
      searchQuery = "";
      isQuery = false;
      loadMoreAdminContent(sectionId);
      
    }

  
  function showDeleteAdminMenu(elementId, category) {
    let deleteMenu = document.getElementById('deleteMenuAdmin');
    deleteMenu.querySelector('h2').innerHTML = `Delete ${category === 'topics' ? 'Topic' : category === 'reports' ? 'Reports' : 'User'}`;
    deleteMenu.querySelector('p').innerHTML = `Are you sure you want to delete this ${category == 'topics' ? 'topic' : category == 'reports' ? 'report' : 'user'}? This action cannot be undone.`;
    deleteMenu.classList.toggle('hidden');
    deleteMenu.classList.toggle('flex');
    window.categoryDelete = category;
    window.elementToDelete = elementId;
  }

  let userState = null;
  function showBanAdminMenu(elementId, state) {
    let banMenu = document.getElementById('banMenuAdmin');
    
    const row = document.querySelector('#User-' + elementId + ' .userState');
      if(row === null) {
        //row is null when the admin is banning from the user profile
        if(userState === null) {
          userState = state;
        }
        if(userState === "suspended") {
          
          document.querySelector('#banMenuAdmin h2').innerHTML = "Unban User";
          document.querySelector('#banMenuAdmin p').innerHTML= "Are you sure you want to unban this user?"
          document.getElementById('confirmBanButtonAdmin').innerHTML = "Unban";
        }
        else {
          document.querySelector('#banMenuAdmin h2').innerHTML = "Ban User";
          document.querySelector('#banMenuAdmin p').innerHTML= "Are you sure you want to ban this user?"
          document.getElementById('confirmBanButtonAdmin').innerHTML = "Ban";
        }
      }
      else if(row.innerHTML === "suspended") {
        document.querySelector('#banMenuAdmin h2').innerHTML = "Unban User";
        document.querySelector('#banMenuAdmin p').innerHTML= "Are you sure you want to unban this user?"
        document.getElementById('confirmBanButtonAdmin').innerHTML = "Unban";
      }
      else {
        document.querySelector('#banMenuAdmin h2').innerHTML = "Ban User";
        document.querySelector('#banMenuAdmin p').innerHTML= "Are you sure you want to ban this user?"
        document.getElementById('confirmBanButtonAdmin').innerHTML = "Ban";
      }
    
    
    banMenu.classList.toggle('hidden');
    banMenu.classList.toggle('flex');
    window.userBan = elementId;
  }


//Admin content insertion and creation ----------------------------------------------------------------------------------------

  //inserts the show more button into the end of a table on the admins page. Only used when there is more content to retrieve from the DB 
  function insertShowMoreAdmin(sectionId) {
    const section = document.getElementById(sectionId);  
    let showMore = document.createElement('button');
    showMore.classList.add("flex", "w-full", "justify-center", "items-center");
    showMore.setAttribute('onclick', `loadMoreAdminContent('${sectionId}')`);
    showMore.setAttribute('id', 'showMore');
    showMore.innerHTML = `
                <svg class="-rotate-90 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span>Show More</span>
    `;
    section.appendChild(showMore);
  }

  //remove the shoe more button
  function removeShowMoreAdmin() {
    document.getElementById('showMore')?.remove();
  }

  
  //inserts more reposts into the reports table in the admin page
  function insertMoreReports() {
    removeLoadingCircle();
    const loadingWrapper = document.getElementById('loadingWrapper');
    loadingWrapper.remove();

    let reports =  JSON.parse(this.responseText);

    if(reports.response) {
      let messageDiv = document.getElementById('messageContainer');
      createAlert(messageDiv, response.message, true);
      return;
    }
    maxAdminPage = reports.last_page;
    const section = document.getElementById('reports');  
    const sectionContentTable = section.querySelector('table');

    if(document.querySelector('#reports th') === null) {
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

    for(let i = 0; i < reports.data.length; i++) {
      let row = document.createElement('tr');
      row.setAttribute('id', 'Report-' + reports.data[i].reportid);
      row.classList.add("shadow", "font-medium");
      row.innerHTML = `
        <td class="w-1/3 px-4 py-2 text-gray-700 max-w-20 sm:max-w-40 truncate ...">
          <a href = '/posts/${reports.data[i].postid}'>
            ${reports.data[i].commentid === null ? `Post ID${reports.data[i].postid}` : `Comment ID${reports.data[i].commentid}`}
          </a>
        </td>
        <td  class="w-1/3 px-4 py-2 text-gray-700 "><button class = "reportReason max-w-20 sm:max-w-40 truncate ..." onclick = "toggleReasonDetails(${reports.data[i].reportid})">${reports.data[i].reason}</button></td>
        <td class="w-1/3 px-4 py-2 text-gray-700 "><a class = "max-w-20 sm:max-w-40 truncate ..." href = '/profile/${reports.data[i].user.username}'>${reports.data[i].user.username === null ? "Deleted User" : reports.data[i].user.username}</a></td>
         <td class="px-4 py-2 self-end">
        <form class = "flex items-center" action="../reports/delete/${reports.data[i].reportid}" method="POST" id="deleteForm-${reports.data[i].reportid}">
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

    if(currentAdminPage < maxAdminPage) {
      insertShowMoreAdmin('reports'); 
    }

  }

  //Creates a row with the topic information to be inserted into the topics table in the admin page
  function createAdminTopic(topicname, topicid) {
    let row = document.createElement('tr');
      row.setAttribute('id', 'Topic-' + topicid);
      row.classList.add("flex", "w-full", "shadow", "font-medium");
      row.innerHTML = `
      <td class="grow px-4 py-2 text-gray-700">${topicname}</td>
      <td class="px-4 py-2 self-end">
        <form class = "flex items-center" action="../topics/delete/${topicid}" method="POST" id="deleteForm-${topicid}">
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


  //inserts more topics into the topics table in the admins page
  function insertMoreAdminTopics() {
    removeLoadingCircle();
    const loadingWrapper = document.getElementById('loadingWrapper');
    loadingWrapper.remove();

    const section = document.getElementById('topics');  
    const sectionContentTable = section.querySelector('table');
    
    

    let topics =  JSON.parse(this.responseText);
    maxAdminPage = topics.last_page;

    if(reports.response) {
      let messageDiv = document.getElementById('messageContainer');
      createAlert(messageDiv, response.message, true);
      return;
    }

    for(let i = 0; i < topics.data.length; i++) {

      //ignore the default topic 
      if(topics.data[i].topicid == 1) {
        continue;
      }

      let row = createAdminTopic(topics.data[i].topicname, topics.data[i].topicid);
      sectionContentTable.appendChild(row);
    }

    if(currentAdminPage < maxAdminPage) {
      insertShowMoreAdmin('topics');
    }
  }

  function insertMoreAdminUsers() {
    removeLoadingCircle();
    const loadingWrapper = document.getElementById('loadingWrapper');
    loadingWrapper.remove();

    let users =  JSON.parse(this.responseText);

    if(reports.response) {
      let messageDiv = document.getElementById('messageContainer');
      createAlert(messageDiv, response.message, true);
      return;
    }
    maxAdminPage = users.last_page;
    const section = document.getElementById('users');  
    const sectionContentTable = section.querySelector('table');

    if(document.querySelector('#users th') === null) {
      let header = document.createElement('tr');
      header.classList.add("shadow", "font-medium");
      header.innerHTML = `
      <th class = "w-1/2 text-start px-4 py-2" >Username</th>
      <th class = "w-1/2 text-start px-4 py-2" >State</th>
      <th></th>
      `;
      sectionContentTable.appendChild(header);
    }

    for(let i = 0; i < users.data.length; i++) {
      let row = document.createElement('tr');
      row.setAttribute('id', 'User-' + users.data[i].userid);
      row.classList.add("shadow", "font-medium");
      row.innerHTML = `
        <td class="w-1/2 px-4 py-2 text-gray-700">
          <a class = "max-w-20 sm:max-w-40 truncate ..." href = '/profile/${users.data[i].username}'>
            ${users.data[i].username}
          </a>
        </td>
        <td  class="userState w-1/2 max-w-40 px-4 py-2 text-gray-700">${users.data[i].state}</td>
        <td class="flex flex-row px-4 py-2 self-end justify-between items-center">
        <button onclick = "showBanAdminMenu(${users.data[i].userid})" class="banButton text-center w-16 px-1 py-1 bg-slate-100 hover:bg-slate-200 rounded-2xl focus:outline-none">${users.data[i].state === "active" ? "Ban" : "Unban"}</button>
        <form class = "flex items-center" action="../profile/${users.data[i].userid}/delete" method="POST" id="deleteForm-${users.data[i].userid}">  
          <input type="hidden" name="_token" value= ${getCsrfToken()} />
          <button type="button" onclick="showDeleteAdminMenu(${users.data[i].userid}, 'users')" class="text-red-500 hover:text-red-700 ml-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
          </button>
        </form>
      </td>

      `
      sectionContentTable.appendChild(row);
    }

    if(currentAdminPage < maxAdminPage) {
      insertShowMoreAdmin('users'); 
    }

    

  }
  
  //Loads more content and calls calls the apropriated insert function to insert the loaded content
  let maxAdminPage = -1;
  let currentAdminPage = 0;
  function loadMoreAdminContent(sectionId) {

      if((maxAdminPage < currentPage) && (maxAdminPage != -1)) {
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

      switch(sectionId) {
        case 'topics':
          if(isQuery) {
            sendAjaxRequest('get', '/api/topics/search/all?page=' + currentAdminPage + "&q=" + searchQuery, null, insertMoreAdminTopics);
          }
          else {
            sendAjaxRequest('get', '/api/topics/all?page=' + currentAdminPage, null, insertMoreAdminTopics);
          }
          break;
        case 'reports':
          if(isQuery) {
            sendAjaxRequest('get', '/api/reports/search/all?page=' + currentAdminPage +"&q=" + searchQuery, null, insertMoreReports);
          }
          else {
            sendAjaxRequest('get', '/api/reports/all?page=' + currentAdminPage, null, insertMoreReports);
          }
          break;
        case 'users':
          if(isQuery) {
            sendAjaxRequest('get', '/api/admin/users/search/all?page=' + currentAdminPage +"&q=" + searchQuery, null, insertMoreAdminUsers);
          }
          else {
            sendAjaxRequest('get', '/api/admin/users/all?page=' + currentAdminPage, null, insertMoreAdminUsers);
          }
      }
    }


  //Triggered when a search input is submited. Loads more content that corresponds to the  search query
  function searchAdmin(event, sectionId) {
    event.preventDefault();

    currentAdminPage = 0;
    isQuery = true;
    searchQuery = document.querySelector(`#${sectionId}AdminSearch`).value;
  
     //cancel the search if there is not a query
     if(searchQuery == "") {
      isQuery = false;
    }
  
    //remove the existing topics from the list that is being displayed to the user 
    const section = document.getElementById(sectionId);  
    const sectionContentTable = section.querySelector('table');
    if(sectionContentTable == null) {
      return;
    }
    while(sectionContentTable.firstChild) {
      sectionContentTable.firstChild.remove();
    }
  
    loadMoreAdminContent(sectionId);
  }



addEventListeners();