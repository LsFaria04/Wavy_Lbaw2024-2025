function addEventListeners() {
  document.addEventListener('DOMContentLoaded', fadeAlert);
  document.addEventListener('DOMContentLoaded', switchGroupTab);
  window.addEventListener("scroll", infiniteScroll);

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


  addEventListenerEditUserAdmin();
  eventListernerFormsAdmin();
  
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
        currentPage += 1;
        insertLoadingCircle(timeline);
        loading = true;
        sendAjaxRequest('get', '/api/posts?page=' + currentPage, null, insertMoreTimeline);
        loading = false;
      }
  
      //actions to take place in the search page
      const searchPage = document.querySelector("#search-results");
      if((searchPage !== null) && (maxPage > currentPage || (maxPage == -1)) && (!loading) ){
          currentPage +=1;
          loading = true;
          insertLoadingCircle(searchPage);
          const query = document.querySelector('input[name="q"]').value;
          sendAjaxRequest('get', '/search?page=' + currentPage + "&" + 'q=' + query + "&" + "category=" + searchCategory, null, insertMoreSearchResults);
          loading = false;
      }

      const groupListPage = document.querySelector("#group-results");
      if((groupListPage !== null) && (maxPage > currentPage || (maxPage == -1)) && (!loading) ){
        currentPage +=1;
        loading = true;
        insertLoadingCircle(groupListPage);
        const query = document.querySelector('input[name="q"]').value;
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
  
        currentPage +=1;
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
        if (!isPublic && !isadmin) {
            // Skip loading more for private groups
            return;
        }

        currentPage += 1;
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
    }
  }

  //inserts a loading circle when an ajax request starts (infinite scroll) 
function insertLoadingCircle(element){
  if(document.querySelector("#loading_circle") !== null){
    //already exists a loading circle
    return;
  }

  let loadingCircle = document.createElement("div");

  loadingCircle.classList.add("ml-auto", "mr-auto", "inline-block", "h-8", "w-8", "animate-spin", "rounded-full", "border-4", "border-solid", "border-current", "border-e-transparent", "align-[-0.125em]", "text-primary", "motion-reduce:animate-[spin_1.5s_linear_infinite]");
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


addEventListeners();