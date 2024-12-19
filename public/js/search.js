const { comment } = require("postcss");

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
      insertUpdateForm(post, posts.data[i].postid, posts.data[i].message, posts.data[i].media, posts.data[i].topics);
    }

    let editForm = post.querySelector('.edit-post-form form');
    if (editForm !== null) {
      addEventListenerToForm(editForm);
    }
    element.appendChild(post);
  }
}

function insertMoreCommentsToPost(element, comments) {
  if (comments.data && comments.data.length > 0){
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
      let editForm = comment.querySelector('.edit-comment-form form');
      if (editForm !== null) {
        addEventListenerToCommentForm(editForm);
      }
      
      element.appendChild(comment);
    }
  }
}

function insertMoreSubCommentsToComment(subcomments) {
  let html = '';
  
  if (subcomments && subcomments.length > 0) {
    for (let i = 0; i < subcomments.length; i++) {
      // Check if the user is deleted and modify accordingly
      if (subcomments[i].user.state === 'deleted') {
        subcomments[i].user.username = 'Deleted User';
      }
      console.log(subcomments[i]);

      let comment = createComment(subcomments[i]);

      // Add options to the comment (admin/user options)
      if (userId == subcomments[i].user.userid || isadmin) {
        comment = createCommentOptions(comment, subcomments[i].commentid, false);
      } else {
        comment = createCommentOptions(comment, subcomments[i].commentid, true);
      }

      // Check if media exists before passing it to insertCommentMedia
      if (subcomments[i].media && subcomments[i].media.length > 0) {
        comment = insertCommentMedia(comment, subcomments[i].media);
      }

      // If the user is authorized, insert the update comment form
      if (userId == subcomments[i].user.userid || isadmin) {
        insertUpdateCommentForm(comment, subcomments[i].commentid, subcomments[i].message, subcomments[i].media);
      }

      // Add event listeners for the edit form
      let editForm = comment.querySelector('.edit-comment-form form');
      if (editForm !== null) {
        addEventListenerToCommentForm(editForm);
      }

      html += comment.outerHTML; // Append the generated comment HTML
    }
  }
  return html; // Return the HTML string of all the subcomments
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