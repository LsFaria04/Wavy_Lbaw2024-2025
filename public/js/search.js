function addEventListeners() {
  document.addEventListener('DOMContentLoaded', switchProfileTab);
  document.addEventListener('DOMContentLoaded', switchGroupTab);
  window.addEventListener("scroll", infiniteScroll);
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
  query = document.querySelector('input[name="q"]').value;
  loadSearchContent(category, query);
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
  
function insertMoreSearchResults(){
    removeLoadingCircle();
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

function insertMoreGroups(element, groups){
  for(let i = 0; i < groups.data.length; i++){
    let group = createGroup(groups.data[i]);
    element.appendChild(group);
  
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
    
function insertMoreUsers(element, users){
  for(let i = 0; i < users.data.length; i++){
    let user = createUser(users.data[i]);
    element.appendChild(user);
  }
}
  
addEventListeners();