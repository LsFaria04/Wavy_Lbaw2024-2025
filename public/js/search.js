function addEventListeners() {
  document.addEventListener('DOMContentLoaded', switchProfileTab);
  document.addEventListener('DOMContentLoaded', switchGroupTab);
  window.addEventListener("scroll", infiniteScroll);
}

//gets the search category to be used by the other functions
let searchCategory = null;
  if(document.querySelector('input[name="category"]') !== null){
  searchCategory = document.querySelector('input[name="category"]').value;
}



//Search menus toggles -----------------------------------------------------------------------------------------------------



//Used to change the search category when a user clicks in a search tab option
function changeCategory(category) {
  currentPage = 1;

  searchCategory = category;
  document.querySelector('input[name="category"]').value = category;

  const buttons = document.querySelectorAll('.category-btn');
  buttons.forEach(button => {
      if (button.dataset.category === category) {
          button.classList.add('text-sky-900', 'border-sky-900');
      } else {
          button.classList.remove('text-sky-900', 'border-sky-900');
      }
  });
    filtersLoaded = false;
    filters = {};
    currentPage = 1;
    query = document.querySelector('input[name="q"]').value;
    loadSearchContent(category, query, null);
  }

  // shows/hides the toggle filters menu
  let filtersLoaded = false
  function toggleFilters(){
    const filtersMenu = document.getElementById('filterMenu');
    if(filtersMenu.classList.contains('hidden') && !filtersLoaded){
      buildFilterOptions();
      filtersLoaded = true;
    }
    filtersMenu.classList.toggle('hidden');
    filtersMenu.classList.toggle('flex');
    currentFilterPage = 0;
  }



  //filters related functions ---------------------------------------------------------------------------------------------



  //builds the filter options menu with the options related to the corresponding category
  function buildFilterOptions(){
    const filtersOptions = document.getElementById('filterOptions');

    //remove previous filter options
    while(filtersOptions.firstChild !== null){
      filtersOptions.firstChild.remove();
    }

    //build the filters according to the category of the search
    switch(searchCategory){
      case 'posts':
        buildPostFilterOptions();
        break;
      
      case 'users':
        buildUserFilterOptions();
        break;

      case 'groups':
        buildGroupFilterOptions();
        break;

    }

  }

  //builds the filter options menu for the posts
  function buildPostFilterOptions(){
    const filtersOptions = document.getElementById('filterOptions');

    const topicContainer = document.createElement('div');
    topicContainer.setAttribute('id', 'topicsFilterContainer')
    topicContainer.innerHTML = `
    <div class="border-b-2"><p class = "font-medium">Topic</p></div>
    <ul class = "optionsList h-60 overflow-y-scroll border-b-2">
    </ul>
    `

    filtersOptions.appendChild(topicContainer);

    loadTopicsForFilters();
  }

  //builds the filter options menu for the groups
  function buildGroupFilterOptions(){
    const filtersOptions = document.getElementById('filterOptions');
    const visibilityContainer = document.createElement('div');
    visibilityContainer.setAttribute('id', 'visibilityFilterContainer')
    visibilityContainer.innerHTML = `
    <div class="border-b-2"><p class = "font-medium">Visibility</p></div>
    <ul class = "optionsList">
      <li class = "my-1 mx-4 flex flex-row justify-between">
        <p>Public</p>
        <input type="checkbox" id = "publicCheck">
      </li>
      <li class = "my-1 mx-4 flex flex-row justify-between">
        <p>Private</p>
        <input type="checkbox" id = "privateCheck">
      </li>
    </ul>
    `
    filtersOptions.appendChild(visibilityContainer);

  }

  //builds the filter options menu for the users
  function buildUserFilterOptions(){
    const filtersOptions = document.getElementById('filterOptions');
    const visibilityContainer = document.createElement('div');
    visibilityContainer.setAttribute('id', 'visibilityFilterContainer')
    visibilityContainer.innerHTML = `
    <div class="border-b-2"><p class = "font-medium">Visibility</p></div>
    <ul class = "optionsList">
      <li class = "my-1 mx-4 flex flex-row justify-between">
        <p>Public</p>
        <input type="checkbox" id = "publicCheck">
      </li>
      <li class = "my-1 mx-4 flex flex-row justify-between">
        <p>Private</p>
        <input type="checkbox" id = "privateCheck">
      </li>
    </ul>
    `
    filtersOptions.appendChild(visibilityContainer);
  }

  //send an ajax request with the the filters that the user selected
  let filters = {}
  function applyFilters(){
    currentFilterPage = 0;
    currentPage = 1;
    const query = document.querySelector('input[name="q"]').value;
    switch(searchCategory){
      case 'posts':
        //get the check topics
        let checkedTopics = document.querySelectorAll("#topicsFilterContainer input:checked");
        let checkedList = [];
        for(let i = 0; i < checkedTopics.length; i++){
          checkedList.push(checkedTopics[i].getAttribute('id'));
        }
        filters = {'topics' : checkedList};
        loadSearchContent(searchCategory, query, filters);
        break;
      
      case 'groups':
        let publicCheckBox = document.querySelectorAll('#publicCheck:checked');
        let privateCheckBox = document.querySelectorAll('#privateCheck:checked');

        if(privateCheckBox.length > 0 && publicCheckBox.length > 0){
          //no need to add a filter because we want all
        }
        else if(privateCheckBox.length > 0){
          filters['visibilityPublic'] = false;
        }
        else if(publicCheckBox.length > 0){
          filters['visibilityPublic'] = true;
        }
        else{
          //no need to add a filter because we want all
        }
        loadSearchContent(searchCategory, query, filters);
        
        break;
      case 'users':
        let publicCheckBoxUser = document.querySelectorAll('#publicCheck:checked');
        let privateCheckBoxUser = document.querySelectorAll('#privateCheck:checked');
        if(privateCheckBoxUser.length > 0 && publicCheckBoxUser.length > 0){
          //no need to add a filter because we want all
        }
        else if(privateCheckBoxUser.length > 0){
          filters['visibilityPublic'] = false;
        }
        else if(publicCheckBoxUser.length > 0){
          filters['visibilityPublic'] = true;
        }
        else{
          //no need to add a filter because we want all
        }
        loadSearchContent(searchCategory, query, filters);
        break;
    }
    toggleFilters();
  }



//Search content loading and content insertion functions --------------------------------------------------------------------------------------



//loads the first content of a search when selecting another category
function loadSearchContent(category, query, filters){
  const searchResults = document.querySelector("#search-results");
  
  while (searchResults.firstChild) {
    searchResults.removeChild(searchResults.firstChild);
  }
  
  insertLoadingCircle(searchPage);
  sendAjaxRequest('post', '/api/search/filtered?page=' + currentPage + "&" + 'q=' + query + "&" + "category=" + category, filters, insertMoreSearchResults);
}
  
function insertMoreSearchResults(){
    removeLoadingCircle();
    const searchResults = document.querySelector("#search-results");

    if(document.getElementById('filter') === null){
      const filterButton = document.createElement('button');
      filterButton.innerHTML = `
          Filter
          <svg id="Layer_1" height="20" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1">
              <path d="m14.5 24a1.488 1.488 0 0 1 -.771-.214l-5-3a1.5 1.5 0 0 1 -.729-1.286v-5.165l-5.966-7.3a4.2 4.2 0 0 1 -1.034-2.782 4.258 4.258 0 0 1 4.253-4.253h13.494a4.254 4.254 0 0 1 3.179 7.079l-5.926 7.303v8.118a1.5 1.5 0 0 1 -1.5 1.5zm-3.5-5.35 2 1.2v-6a1.5 1.5 0 0 1 .335-.946l6.305-7.767a1.309 1.309 0 0 0 .36-.884 1.255 1.255 0 0 0 -1.253-1.253h-13.494a1.254 1.254 0 0 0 -.937 2.086l6.346 7.765a1.5 1.5 0 0 1 .338.949z"/>
          </svg> 
      `;
      filterButton.setAttribute('onclick', 'toggleFilters()');
      filterButton.setAttribute("class","flex flex-row gap-2 self-start p-4");
      filterButton.setAttribute("id","filter");
      searchResults.appendChild(filterButton);
    }
    

  
    let results = JSON.parse(this.responseText);
  
    switch(searchCategory){
  
        case 'posts':
          if(results[0] === undefined) break;
          maxPage = results[0].last_page; 
          insertMorePosts(searchResults,results[0]);
          break;
  
        case 'users':
          if(results[1] === undefined) break;
          maxPage = results[1].last_page;
          insertMoreUsers(searchResults,results[1]);
          break;
  
        case 'groups':
          if(results[2] === undefined) break;
          maxPage = results[2].last_page;
          insertMoreGroups(searchResults,results[2]);
          break;
  
      default:
        return;
  }
  
  if(searchResults.childElementCount == 1){
      searchResults.innerHTML = `
         <button id="filter" class = "flex flex-row gap-2 self-start p-4" onclick = "toggleFilters()" >
                    Filter
                    <svg id="Layer_1" height="20" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1">
                        <path d="m14.5 24a1.488 1.488 0 0 1 -.771-.214l-5-3a1.5 1.5 0 0 1 -.729-1.286v-5.165l-5.966-7.3a4.2 4.2 0 0 1 -1.034-2.782 4.258 4.258 0 0 1 4.253-4.253h13.494a4.254 4.254 0 0 1 3.179 7.079l-5.926 7.303v8.118a1.5 1.5 0 0 1 -1.5 1.5zm-3.5-5.35 2 1.2v-6a1.5 1.5 0 0 1 .335-.946l6.305-7.767a1.309 1.309 0 0 0 .36-.884 1.255 1.255 0 0 0 -1.253-1.253h-13.494a1.254 1.254 0 0 0 -.937 2.086l6.346 7.765a1.5 1.5 0 0 1 .338.949z"/>
                    </svg> 
          </button>
        <div class="flex justify-center items-center h-32">
            <p class="text-gray-600 text-center">No results matched your search.</p>
        </div>
      `;       
  }
}

//Inserts a show more button for the filters menu
function insertShowMoreFilter(){
  const section = document.getElementById('topicsFilterContainer');  
  let showMore = document.createElement('button');
  showMore.classList.add("flex", "w-full", "justify-center", "items-center");
  showMore.setAttribute('onclick', `loadTopicsForFilters()`);
  showMore.setAttribute('id', 'showMore');
  showMore.innerHTML = `
              <svg class="-rotate-90 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
              <p>Show More</p>
  `;
  section.appendChild(showMore);
}


//loads more topics to insert into the filter menu
let currentFilterPage = 0;
let maxFilterPage = -1;
function loadTopicsForFilters(){
  currentFilterPage++;
  let topicContainerList = document.querySelector('#topicsFilterContainer');
  insertLoadingCircle(topicContainerList);
  sendAjaxRequest('get', '/api/topics/all?page=' + currentFilterPage, null, insertMoreTopicsFilters);
  
}

//inserts more topics into the filter menu
function insertMoreTopicsFilters(){
  let topicContainerList = document.querySelector('#topicsFilterContainer > .optionsList');
  removeShowMoreAdmin();
  removeLoadingCircle();
  let topics =  JSON.parse(this.responseText);
  maxFilterPage = topics.last_page;
  currentFilterPage = topics.current_page

  for(let i = 0; i < topics.data.length; i++){
    let row = document.createElement('li');
    row.setAttribute('class', "my-1 mx-4 flex flex-row justify-between");
    row.innerHTML = `
      <label for= ${topics.data[i].topicid} >${topics.data[i].topicname}</label>
      <input type = "checkbox" id= ${topics.data[i].topicid}>
    `;
    topicContainerList.appendChild(row);
    
  }
  if(currentFilterPage < maxFilterPage){
    insertShowMoreFilter();
  }
}



function insertMoreGroups(element, groups){
  for(let i = 0; i < groups.data.length; i++){
    let group = createGroup(groups.data[i]);
    element.appendChild(group);
  
  }
}

//inserts more users into an element
function insertMoreUsers(element, users){
  for(let i = 0; i < users.data.length; i++){
    let user = createUser(users.data[i]);
    element.appendChild(user);
  }
}

//inserts more users into an element
function insertMoreUsers(element, users){
  for(let i = 0; i < users.data.length; i++){
    let user = createUser(users.data[i]);
    element.appendChild(user);
  }
}

function insertMorePosts(element, posts) {
  for (let i = 0; i < posts.data.length; i++) {
    if (posts.data[i].user.state === 'deleted') {
      posts.data[i].user.username = 'Deleted User';
    }

    let post = createPost(posts.data[i]);

    if (userId == posts.data[i].user.userid || isadmin) {
      post = createPostOptions(post, posts.data[i].postid, false);
    } else {
      post = createPostOptions(post, posts.data[i].postid, true);
    }

    post = insertPostTopics(post, posts.data[i].topics);

    post = insertPostMedia(post, posts.data[i].media);

    if (userId == posts.data[i].user.userid || isadmin) {
      insertUpdateForm(post, posts.data[i].postid, posts.data[i].message, posts.data[i].media);
    }

    let editForm = post.querySelector('.edit-post-form form');
    if (editForm !== null) {
      addEventListenerToForm(editForm);
    }
    element.appendChild(post);
  }
}



//Search content creation functions ---------------------------------------------------------------------------



// Creates a new group container with all the needed info
function createGroup(groupInfo) {
  let group = document.createElement('div');
  group.classList.add("group", "border-b", "border-gray-300", "p-4", "bg-white");

  group.innerHTML = `
    <div class="group-header mb-2">
      <h3 class="font-bold">
        <a href="/groups/${groupInfo.groupname}" class="text-black hover:text-sky-900">
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

//creates the like buttons to be inserted into the posts
function createLikeButton(postId, likeCount = 0, likedByUser = false) {
  return `
      <div class="post-likes flex items-center gap-2 mt-4">
          <button 
              type="button" 
              class="flex items-center text-gray-500 hover:text-red-600" 
              onclick="likePost(${postId}); event.stopPropagation();">
              
              <!-- No like -->
              <svg 
                  id="heart-empty-${postId}" 
                  viewBox="0 0 24 24" 
                  aria-hidden="true" 
                  class="h-5 w-5 ${likedByUser ? 'hidden' : 'fill-gray-500 hover:fill-red-600'}">
                  <g>
                      <path d="M16.697 5.5c-1.222-.06-2.679.51-3.89 2.16l-.805 1.09-.806-1.09C9.984 6.01 8.526 5.44 7.304 5.5c-1.243.07-2.349.78-2.91 1.91-.552 1.12-.633 2.78.479 4.82 1.074 1.97 3.257 4.27 7.129 6.61 3.87-2.34 6.052-4.64 7.126-6.61 1.111-2.04 1.03-3.7.477-4.82-.561-1.13-1.666-1.84-2.908-1.91zm4.187 7.69c-1.351 2.48-4.001 5.12-8.379 7.67l-.503.3-.504-.3c-4.379-2.55-7.029-5.19-8.382-7.67-1.36-2.5-1.41-4.86-.514-6.67.887-1.79 2.647-2.91 4.601-3.01 1.651-.09 3.368.56 4.798 2.01 1.429-1.45 3.146-2.1 4.796-2.01 1.954.1 3.714 1.22 4.601 3.01.896 1.81.846 4.17-.514 6.67z"></path>
                  </g>
              </svg>
              
              <!-- Yes like -->
              <svg 
                  id="heart-filled-${postId}" 
                  viewBox="0 0 24 24" 
                  aria-hidden="true" 
                  class="h-5 w-5 ${likedByUser ? 'fill-red-600' : 'hidden'}">
                  <g>
                      <path d="M20.884 13.19c-1.351 2.48-4.001 5.12-8.379 7.67l-.503.3-.504-.3c-4.379-2.55-7.029-5.19-8.382-7.67-1.36-2.5-1.41-4.86-.514-6.67.887-1.79 2.647-2.91 4.601-3.01 1.651-.09 3.368.56 4.798 2.01 1.429-1.45 3.146-2.1 4.796-2.01 1.954.1 3.714 1.22 4.601 3.01.896 1.81.846 4.17-.514 6.67z"></path>
                  </g>
              </svg>
              
              <span id="like-count-${postId}" class="ml-1">${likeCount}</span>
          </button>
      </div>
  `;
}

//creates a user container with all the necessary info
function insertMoreCommentsToPost(element, comments) {
  for (let i = 0; i < comments.data.length; i++) {
    if (comments.data[i].user.state === 'deleted') {
      comments.data[i].user.username = 'Deleted User';
    }

    let comment = createComment(comments.data[i]);

    if (userId == comments.data[i].user.userid || isadmin) {
      comment = createCommentOptions(comment, comments.data[i].commentid, false);
    } else {
      comment = createCommentOptions(comment, comments.data[i].commentid, true);
    }

    const likeButtonHtml = createLikeButton(comments.data[i].commentid, comments.data[i].like_count, comments.data[i].liked_by_user);
    const commentButtonHtml = createCommentButton(comments.data[i].commentid, comments.data[i].comment_count);

    const interactionContainer = document.createElement('div');
    interactionContainer.classList.add('comment-interactions', 'flex', 'items-center', 'gap-4', 'mt-4');
    interactionContainer.innerHTML = likeButtonHtml + commentButtonHtml;

    comment.appendChild(interactionContainer);

    comment = insertCommentMedia(comment, comments.data[i].media);

    if (userId == comments.data[i].user.userid || isadmin) {
      insertUpdateCommentForm(comment, comments.data[i].commentid, comments.data[i].message, comments.data[i].media);
    }

    let editForm = comment.querySelector('.edit-comment-form form');
    if (editForm !== null) {
      addEventListenerToCommentForm(editForm);
    }
    element.appendChild(comment);
  }
}

function createUser(userInfo){
  let user = document.createElement('div');
  user.classList.add("user", "mb-4", "p-4", "bg-white", "rounded-md", "shadow-md");
  
    user.innerHTML= `
      <div class="user-header mb-2">
              <h3 class="font-bold">
                  <a href="../profile/${userInfo.username}" class="text-black hover:text-sky-900">
                      ${userInfo.username}
                  </a>
              </h3>
          </div>
          <div class="user-body mb-2">
              <p>${userInfo.bio === null ? "" : userInfo.bio }</p>
          </div>
    `;
  
  return user;
}
     
addEventListeners();