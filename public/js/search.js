function addEventListeners() {
  document.addEventListener('DOMContentLoaded', fadeAlert);
  document.addEventListener('DOMContentLoaded', switchProfileTab);
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

  //listeners related to the posts

  addEventListenerEditUserAdmin();
  eventListernerFormsAdmin();
  
}

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

    if(userId == posts.data[i].user.userid || isadmin){
      post = createPostOptions(post, posts.data[i].postid); 
    }

    post = insertPostTopics(post, posts.data[i].topics);

    const likeButtonHtml = createLikeButton(posts.data[i].postid, posts.data[i].like_count, posts.data[i].liked_by_user);
    
    post.insertAdjacentHTML('beforeend', likeButtonHtml);

    post = insertPostMedia(post, posts.data[i].media);

    if(userId == posts.data[i].user.userid || isadmin){
      insertUpdateForm(post, posts.data[i].postid, posts.data[i].message, posts.data[i].media);
    }

    let editForm = post.querySelector('.edit-post-form form');
    if(editForm !== null){
      addEventListenerToForm(editForm);
    }
    element.appendChild(post);
  }

  function createLikeButton(postId, likeCount = 0, likedByUser = false) {
    return `
        <div class="post-likes flex items-center gap-2 mt-4">
            <button 
                type="button" 
                class="flex items-center text-gray-500 hover:text-red-600" 
                onclick="likePost(${postId}, event); event.stopPropagation();">
                
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

let searchCategory = null;
  if(document.querySelector('input[name="category"]') !== null){
  searchCategory = document.querySelector('input[name="category"]').value;
}

//loads the first content of a search when selecting another category
function loadSearchContent(category, query){
  const searchResults = document.querySelector("#search-results");
  
  while (searchResults.firstChild) {
    searchResults.removeChild(searchResults.firstChild);
  }
  
  insertLoadingCircle(searchPage);
  sendAjaxRequest('get', '/search?page=' + currentPage + "&" + 'q=' + query + "&" + "category=" + category, null, insertMoreSearchResults);
}
  
//inserts more results in the search body
function insertMoreSearchResults(){
    removeLoadingCircle();//remove the circle because we already have the data
    const searchResults = document.querySelector("#search-results");
  
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
  
  if(searchResults.firstChild == null){
      searchResults.innerHTML = `
        <div class="flex justify-center items-center h-32">
            <p class="text-gray-600 text-center">No results matched your search.</p>
        </div>
      `;       
  }
}

// Creates a new group container with all the needed info
function createGroup(groupInfo) {
  let group = document.createElement('div');
  group.classList.add("group", "mb-4", "p-4", "bg-white", "rounded-md", "shadow-md");

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

//inserts more groups into and element
function insertMoreGroups(element, groups){
  for(let i = 0; i < groups.data.length; i++){
    let group = createGroup(groups.data[i]);
    element.appendChild(group);
  
  }
}

//inserts more posts into an element
function insertMorePosts(element, posts){
  for(let i = 0; i < posts.data.length; i++){
    
    if (posts.data[i].user.state === 'deleted') {
      posts.data[i].user.username = 'Deleted User';
    }
    
    let post = createPost(posts.data[i]);

    if(userId == posts.data[i].user.userid || isadmin){
      post = createPostOptions(post, posts.data[i].postid, false); 
    }
    else{
      post = createPostOptions(post, posts.data[i].postid, true); 
    }

    post = insertPostTopics(post, posts.data[i].topics);

    const likeButtonHtml = createLikeButton(posts.data[i].postid, posts.data[i].like_count, posts.data[i].liked_by_user);
    
    post.insertAdjacentHTML('beforeend', likeButtonHtml);

    post = insertPostMedia(post, posts.data[i].media);

    if(userId == posts.data[i].user.userid || isadmin){
      insertUpdateForm(post, posts.data[i].postid, posts.data[i].message, posts.data[i].media);
    }

    let editForm = post.querySelector('.edit-post-form form');
    if(editForm !== null){
      addEventListenerToForm(editForm);
    }
    console.log("here");
    element.appendChild(post);
  }
}

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
    
//inserts more users into an element
function insertMoreUsers(element, users){
  for(let i = 0; i < users.data.length; i++){
    let user = createUser(users.data[i]);
    element.appendChild(user);
  }
}
  
addEventListeners();