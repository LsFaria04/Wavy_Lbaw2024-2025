function addEventListeners() {
  document.addEventListener('DOMContentLoaded', fadeAlert);
  document.addEventListener('DOMContentLoaded', switchGroupTab);
  window.addEventListener("scroll", infiniteScroll);
  //window.addEventListener("scroll",loadMoreComments);
  syncButtonPostTopicsWithInputEventListener();
  syncButtonPostFilesWithInputEventListener();
  
}
//removes the loading circle from the page
  function removeLoadingCircle(){
    let loadingCircles = document.querySelectorAll("#loading_circle");
    loadingCircles.forEach((loadingCircle) => loadingCircle.remove());
  }
  
  let currentPage = 1;
  let maxPage = -1;
  let loading = false;
  //check if the we have reached the end of the page and takes the apropriate actions
  function infiniteScroll(){ 
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 1) {
      //action to take place in the home page
      const timeline = document.querySelector("#timeline");
      if((timeline !== null) && (maxPage > currentPage || (maxPage == -1) ) && (!loading) ){
        currentPage++;
        insertLoadingCircle(timeline);
        loading = true;
        sendAjaxRequest('get', '/api/posts?page=' + currentPage, null, insertMoreTimeline);
        loading = false;
      }

      
      const comments = document.querySelector("#comments");
      if((comments !== null) && (maxPage > currentPage || (maxPage == -1) ) && (!loading) ){
        currentPage++;
        insertLoadingCircle(comments);
        loading = true;
        var postId = document.querySelector('input[name="postidForJs"]').value;
        sendAjaxRequest('get', '/api/comments/post/' + postId + '?page=' + currentPage, null, insertMoreCommentsPost);
        loading = false;
      }
      
  
      //actions to take place in the search page
      const searchPage = document.querySelector("#searchPage #search-results");
      if((searchPage !== null) && (maxPage > currentPage || (maxPage == -1)) && (!loading) ){
          currentPage++;
          loading = true;
          insertLoadingCircle(searchPage);
          const query = document.querySelector('input[name="q"]').value;
          sendAjaxRequest('post', '/api/search/filtered?page=' + currentPage + "&" + 'q=' + query + "&" + "category=" + searchCategory, filters, insertMoreSearchResults);
          loading = false;
      }

      const groupListPage = document.querySelector("#group-results");
      if((groupListPage !== null) && (maxPage > currentPage || (maxPage == -1)) && (!loading) ){
        currentPage++;
        loading = true;
        insertLoadingCircle(groupListPage);
        const query = document.querySelector('input[name="q"]').value || '';
        sendAjaxRequest('get', '/groups?page=' + currentPage + "&" + 'q=' + query + "&" + "category=" + searchGroupCategory, null, insertMoreGroupSearchResults);
        loading = false;
      }
  
      //actions to take place in the profile page
      const profilePage = document.querySelector("#profile-tab-content");
      if((profilePage !== null) && (maxPage > currentPage || (maxPage == -1)) && (!loading)) {
  
        if(!isPublic && !isadmin){
          //doesn´t need to load more info because the account is private
          return;
        }
  
        currentPage++;
        loading = true;
        insertLoadingCircle(profilePage);
        switch(profileTab){
          case 'user-posts':
              sendAjaxRequest('get', '/api/posts/' + username + "?page=" + currentPage, null, insertMoreProfileContent);
              break;
            
          case 'user-comments':
            sendAjaxRequest('get', '/api/comments/' + username + "?page=" + currentPage, null, insertMoreProfileContent);
            break;
            
          case 'user-likes':
            sendAjaxRequest('get', '/api/likes/' + username + "?page=" + currentPage, null, insertMoreProfileContent);
            break;
        }
        loading = false;
      }

      // Handle group page
      const groupPage = document.querySelector("#group-tab-content");
      if (groupPage !== null && (maxPage > currentPage || maxPage === -1)) {

        currentPage++;
        insertLoadingCircle(groupPage);
        switch (groupTab) {
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
        loading = false;
      }

      // Handle notifications page
      const notificationsPage = document.querySelector("#notifications-content");
      if (notificationsPage !== null && (maxPage > currentPage || maxPage == -1) && !loading) {
        currentPage++;
        loading = true;
        insertLoadingCircle(notificationsPage);
        sendAjaxRequest('post', '/api/notifications?page=' + currentPage + '&category=' + notificationsTab, null, function() {
            insertMoreNotifications(this.responseText);
            loading = false;
          });
             
      }
    }
  }
  

  //inserts a loading circle when an ajax request starts (infinite scroll) 
function insertLoadingCircle(element){
  
  if(document.querySelector("#loading_circle") !== null){
    //already exists a loading circle
    return;
  }

  let loadingCircle = document.createElement("div");

  loadingCircle.classList.add("ml-auto", "mr-auto", "inline-block", "h-8", "w-8", "animate-spin", "rounded-full", "border-4", "border-solid", "border-current", "border-e-transparent", "align-[-0.125em]", "text-primary", "motion-reduce:animate-[spin_1.5s_linear_infinite]", "z-50");
  loadingCircle.setAttribute('role', "status");
  loadingCircle.setAttribute('id', "loading_circle");
  loadingCircle.innerHTML = `
              <spanclass="!absolute !-m-px !h-px !w-px !overflow-hidden !whitespace-nowrap !border-0 !p-0 ![clip:rect(0,0,0,0)]">
              </span>
  `
  element.appendChild(loadingCircle);
}


//inserts more posts into the timeline
function insertMoreTimeline(){
  removeLoadingCircle(); //remove the circle because we already have the data
  const timeline = document.querySelector("#timeline");
  let posts = JSON.parse(this.responseText);


  maxPage = posts.last_page; //used to stop send requests when maximum page is reached

  insertMorePosts(timeline,posts);

}

//inserts more comments into the post
function insertMoreCommentsPost(){
  removeLoadingCircle(); //remove the circle because we already have the data
  const comments = document.querySelector("#comments");
  let newComments = JSON.parse(this.responseText);

  maxPage = newComments.last_page; //used to stop send requests when maximum page is reached

  insertMoreCommentsToPost(comments,newComments);

}

document?.addEventListener("DOMContentLoaded", () => {
    const togglePostFormButton = document.getElementById("togglePostForm");
    const modalContainer = document.getElementById("modalContainer");
    const closeModalButton = document.getElementById("closeModal");

    // Show the modal
    togglePostFormButton?.addEventListener("click", () => {
        modalContainer.classList.remove("hidden");
    });

    // Hide the modal
    closeModalButton?.addEventListener("click", () => {
        modalContainer.classList.add("hidden");
    });

    // Optional: Hide modal when clicking outside of it
    modalContainer?.addEventListener("click", (e) => {
        if (e.target === modalContainer) {
            modalContainer.classList.add("hidden");
        }
    });
});


function updateFileButtonList(){
  const fileInput = document.getElementById('imageButton');
  const fileDisplay = document.getElementById('buttonFileDisplay');

  // Append new files to the list (preserve existing files)
  Array.from(fileInput.files).forEach(file => {
    if (file.size > 1048576){
      const messageContainer = document.getElementById('messageContainer');
      createAlert(messageContainer, "File is too big (>2Mb)", true);
    }
    else{
      selectedFiles.push(file);
    }
  });

  // Check if there are more than 4 files
  if (selectedFiles.length > 4) {
    const messageContainer = document.getElementById('messageContainer');
    createAlert(messageContainer, 'You can only select up to 4 files', true);
    // Remove the newly added files from the selectedFiles array
    selectedFiles.splice(-fileInput.files.length);
    return; 
  }

  // Clear previous file list
  fileDisplay.innerHTML = '';

  // Show updated list of file names
  selectedFiles.forEach((file, index) => {
    const li = document.createElement('li');
    li.classList.add('flex', 'items-center', 'gap-2');

    li.innerHTML = `
        <span class="text-sm text-gray-500">${file.name}</span>
        <button type="button" onclick="removeSpecificFileButton(${index})" class="text-sm text-red-500 hover:text-red-700">Remove</button>
    `;
    fileDisplay.appendChild(li);
  });

  fileDisplay.classList.remove('hidden');

  // Reset the file input to allow adding more files
  fileInput.value = '';
}

function syncButtonPostTopicsWithInputEventListener(){
  document.querySelector('.addButtonPost form')?.addEventListener('submit', function (e) {
    //update the values before sending the form
    let topicInput = document.getElementById('topicInput-1');
    topicInput.value = selectedTopics;
  });
}

function syncButtonPostFilesWithInputEventListener(){
  // Synchronize selectedFiles with the file input before form submission
  document.querySelector('.addButtonPost form')?.addEventListener('submit', function (e) {
    
    if (selectedFiles.length > 4) {
      e.preventDefault(); // Prevent the form from submitting
      const messageContainer = document.getElementById('messageContainer');
      createAlert(messageContainer, "You can only submit up to 4 files", true);
      return; 
    }

    const fileInput = document.getElementById('imageButton');
    const dataTransfer = new DataTransfer();

    // Append all files from selectedFiles to the new DataTransfer object
    selectedFiles.forEach(file => {
        dataTransfer.items.add(file);
    });

    // Update the file input's files property
    fileInput.files = dataTransfer.files;
  });
}

function removeSpecificFileButton(index) {
  const fileDisplay = document.getElementById('buttonFileDisplay');

  // Remove file from the list
  selectedFiles.splice(index, 1);

  // Clear previous display and update with new list
  fileDisplay.innerHTML = '';
  selectedFiles.forEach((file, i) => {
      const li = document.createElement('li');
      li.classList.add('flex', 'items-center', 'gap-2');

      li.innerHTML = `
          <span class="text-sm text-gray-500 sm:w-12 text-ellipsis overflow-hidden ...">${file.name}</span>
          <button type="button" onclick="removeSpecificFileButton(${i})" class="text-sm text-red-500 hover:text-red-700">Remove</button>
      `;
      fileDisplay.appendChild(li);
  });

  // Hide the display if no files remain
  if (selectedFiles.length === 0) {
      fileDisplay.classList.add('hidden');
  }
}


addEventListeners();