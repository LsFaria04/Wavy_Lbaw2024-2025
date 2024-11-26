function addEventListeners() {
  document.addEventListener('DOMContentLoaded', fadeAlert);
  document.addEventListener('DOMContentLoaded', switchProfileTab);
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

  setupCreateUserMenu();
  //listeners related to the posts
  addEventListenerToPostForms();
  syncFilesWithInputEventListener()

  addEventListenerEditUserAdmin();
  eventListernerFormsAdmin();
  
}

function encodeForAjax(data) {
if (data == null) return null;
return Object.keys(data).map(function(k){
  return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
}).join('&');
}

function sendAjaxRequest(method, url, data, handler) {
let request = new XMLHttpRequest();

request.open(method, url, true);
request.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
request.addEventListener('load', handler);
request.send(encodeForAjax(data));
}


//stores the authentication state
let isAuthenticated = false; 
let userId = -1;
let isadmin = false;
sendAjaxRequest('post', '/api/auth-check', null, authInfo);
function authInfo(){
  const response = JSON.parse(this.responseText);
  isAuthenticated = response.authenticated;
  if(isAuthenticated){
    sendAjaxRequest('post', '/api/auth-id', null, authId);
  }
}
function authId(){
const response = JSON.parse(this.responseText);
userId = response.id;
isadmin = response.isadmin;
}

//gets the csrf token to insert in new forms
function getCsrfToken(){
return document.querySelector('meta[name="csrf-token"]').content;
}

//store user profile info (only accessed if we enter a profile)
let isPublic = false;
let username = "";
if(document.querySelector("#profile-tab-content") !== null){
  username = document.getElementById('profile-username').innerHTML;
  sendAjaxRequest('get', '/api/' + username, null, handleProfileInfo);
}
function handleProfileInfo(){
  const response = JSON.parse(this.responseText);
  isPublic = response.visibilitypublic;
}

//creates the a post container with the message, username and date
function createPost(postInfo){
let post = document.createElement('div');
post.classList.add("post", "mb-4", "p-4", "bg-white", "rounded-md", "shadow");

post.innerHTML = `
  <div class="post-header mb-2 flex justify-between items-center">
      <div>
          <h3 class="font-bold">
            <a href="${ postInfo.user.state === 'deleted' ? '#' : '../profile/' + postInfo.user.username }" 
                class="text-black hover:text-sky-900">
                ${ postInfo.user.state === 'deleted' ? 'Deleted User' : postInfo.user.username }
            </a>
          </h3>
          <span class="text-gray-500 text-sm">${ postInfo.createddate }</span>
      </div>
  </div>
   <div class="post-body mb-2" id=post-content-${postInfo.postid}>
      <p>${ postInfo.message }</p>
    </div>
`;

return post

}

//creates buttons for post options and inserts them into a post. Returns the updated post
function createPostOptions(post, id){
const postheader = post.querySelector('.post-header');
let options = document.createElement('div');
options.classList.add("flex", "items-center", "gap-2");
options.setAttribute('id', 'postOptions');

options.innerHTML = `
  <button type="button" onclick="toggleEditPost(${id})" class="text-gray-500 hover:text-black">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="black" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="0.6" d="M10.973 1.506a18.525 18.525 0 00-.497-.006A4.024 4.024 0 006.45 5.524c0 .43.095.865.199 1.205.054.18.116.356.192.527v.002a.75.75 0 01-.15.848l-4.937 4.911a.871.871 0 000 1.229.869.869 0 001.227 0L7.896 9.31a.75.75 0 01.847-.151c.17.079.35.139.529.193.34.103.774.198 1.204.198A4.024 4.024 0 0014.5 5.524c0-.177-.002-.338-.006-.483-.208.25-.438.517-.675.774-.32.345-.677.696-1.048.964-.354.257-.82.512-1.339.512-.396 0-.776-.156-1.059-.433L9.142 5.627a1.513 1.513 0 01-.432-1.06c0-.52.256-.985.514-1.34.27-.37.623-.727.97-1.046.258-.237.529-.466.78-.675zm-2.36 9.209l-4.57 4.59a2.37 2.37 0 01-3.35-3.348l.002-.001 4.591-4.568a6.887 6.887 0 01-.072-.223 5.77 5.77 0 01-.263-1.64A5.524 5.524 0 0110.476 0 12 12 0 0112 .076c.331.044.64.115.873.264a.92.92 0 01.374.45.843.843 0 01-.013.625.922.922 0 01-.241.332c-.26.257-.547.487-.829.72-.315.26-.647.535-.957.82a5.947 5.947 0 00-.771.824c-.197.27-.227.415-.227.457 0 .003 0 .006.003.008l1.211 1.211a.013.013 0 00.008.004c.043 0 .19-.032.46-.227.253-.183.532-.45.826-.767.284-.308.56-.638.82-.95.233-.28.463-.565.72-.823a.925.925 0 01.31-.235.841.841 0 01.628-.033.911.911 0 01.467.376c.15.233.22.543.262.87.047.356.075.847.075 1.522a5.524 5.524 0 01-5.524 5.525c-.631 0-1.221-.136-1.64-.263a6.969 6.969 0 01-.222-.071z"/>
      </svg>
  </button>
  <form action="../posts/delete/${id}" method="POST" id="deleteForm-${id}">
    <button type="button" onclick="openDeleteMenu(${id})" class="text-red-500 hover:text-red-700 ml-2">
        <input type="hidden" name="_token" value= ${getCsrfToken()} />
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
  </form>
`
postheader.appendChild(options);
return post;
}

//inserts the delete menu into a post container. Returns an updated post
function insertDeleteMenu(post){
const postheader = post.querySelector('.post-header');
let menu = document.createAttribute('div');
menu.setAttribute('id', 'deleteMenu');
menu.classList("fixed", "inset-0", "bg-black", "bg-opacity-50", "hidden", "flex", "items-center", "justify-center", "z-20");

menu.innerHTML = `
  <div class="bg-white rounded-xl shadow-lg p-6 max-w-sm w-full">
      <h2 class="text-xl font-semibold text-gray-900">Delete Post</h2>
      <p class="mt-4 text-sm text-gray-600">Are you sure you want to delete this post? This action cannot be undone.</p>
      <div class="mt-6 flex justify-end gap-3">
          <button id="cancelButton" class="px-4 py-2 text-white bg-gray-400 hover:bg-gray-600 rounded-2xl focus:outline-none">
              Cancel
          </button>
          <button id="confirmButton" class="px-4 py-2 text-white bg-red-600 hover:bg-red-700 rounded-2xl focus:outline-none">
              Delete
          </button>
      </div>
  </div>
`

postheader.appendChild(menu);

return post;
}

//inserts the media (images, audio and video) of a post into a post container. Returns the updated post container
function insertPostMedia(post, mediaArray){
const postbody = post.querySelector('.post-body');
let mediaContainer = document.createElement('div');
mediaContainer.classList.add("post-media", "mt-4", "grid", "grid-cols-2", "gap-4");

for(let i = 0; i < mediaArray.length; i++){
  let media = mediaArray[i];
  let fileExtension = media.path.split('.').pop();

  let newMedia = document.createElement('img');

    
    if(['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)){
      newMedia.setAttribute('alt', 'Image');
      newMedia.setAttribute('src', "/storage/" + media.path);
      newMedia.classList.add("max-w-full", "max-h-96", "object-cover", "rounded-md", "mb-2", "mx-auto");
    }

    else if(['mp4', 'avi', 'mov'].includes(fileExtension)){
      newMedia = document.createElement('video');
      newMedia.setAttribute("controls", "");
      let source = document.createElement('source');
  
      source.setAttribute('src', "/storage/" + media.path);
      source.setAttribute('type', "video/" + fileExtension);

    newMedia.appendChild(source);
    newMedia.classList.add("max-w-full", "max-h-96", "object-cover", "rounded-md", "mb-2", "mx-auto");
  }

    else if(['mp3', 'wav', 'ogg'].includes(fileExtension)){
      newMedia = document.createElement('audio');
      let source = document.createElement('source');
    
      source.setAttribute('src', "/storage/" + media.path);
      source.setAttribute('type', "audio/" + fileExtension);

    newMedia.appendChild(source);
    newMedia.classList.add("w-full","mb-2");
  }

  else{
    newMedia = document.createElement('p');
    newMedia.classList.add("text-gray-500");
    newMedia.innerHTML = 'Unsupported media type';
  }

  mediaContainer.appendChild(newMedia);
}

postbody.appendChild(mediaContainer);

return post;
}

//inserts the update post form into a post container. Return the updated post container.
function insertUpdateForm(post, id, message, media){
let formContainer = document.createElement('div');
formContainer.classList.add("edit-post-form", "hidden", "mt-4", "bg-white", "rounded-xl", "shadow-md", "p-4");
formContainer.setAttribute('id',"edit-post-" + id);

formContainer.innerHTML = `
  <form action="/posts/update/${id}" method="POST" enctype="multipart/form-data"  class="flex flex-col gap-4" data-post-id = "${id}">
      <input type="hidden" name="_token" value= ${getCsrfToken()} />
      <div class="mb-4">
          <label for="message" class="block text-sm font-medium text-gray-700">Edit Message</label>
          <textarea id="message" name="message" rows="2" class="mt-1 block w-full p-4 border rounded-xl focus:ring-2 focus:ring-sky-700 shadow-sm outline-none" placeholder="Edit your message">${ message}</textarea>
      </div>

      <div class="mb-4">
           <label class="block text-sm font-medium text-gray-700">Edit Media</label>

            <label for="image-${id }" class="cursor-pointer flex items-center gap-2 text-gray-500 hover:text-black mt-2">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-7 h-7">
                    <path d="M19.8278 11.2437L12.7074 18.3641C10.7548 20.3167 7.58896 20.3167 5.63634 18.3641C3.68372 16.4114 3.68372 13.2456 5.63634 11.293L12.4717 4.45763C13.7735 3.15589 15.884 3.15589 17.1858 4.45763C18.4875 5.75938 18.4875 7.86993 17.1858 9.17168L10.3614 15.9961C9.71048 16.647 8.6552 16.647 8.00433 15.9961C7.35345 15.3452 7.35345 14.2899 8.00433 13.6391L14.2258 7.41762" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
                <span>Attach new file</span>
            </label>
        
            <div id="fileDisplay-${id}" class="flex-col items-center gap-2 text-gray-500 hover:text-black mt-2 ${ media.length == 0 ? 'hidden' : '' }">
            </div>
            <input type="file" name="media[]" id="image-${ id }" class="hidden" onchange="updateFileNameEdit('${id }')" multiple>
            <input type="hidden" name="remove_media" id="removeMedia-${id }" value="[]">
      </div>

      <button type="submit" class="px-4 py-2 w-20 bg-sky-700 text-white font-semibold rounded-3xl hover:bg-sky-800">Update</button>
  </form>
`;


const fileDisplay = formContainer.querySelector('#fileDisplay-' + id);
for(let i = 0; i < media.length; i++){
  let mediaRemove = document.createElement('div');
  mediaRemove.classList.add("flex", "items-center", "gap-2");
  mediaRemove.setAttribute('id', 'file-' + media[i].mediaid);

  mediaRemove.innerHTML = `
    <span class="text-sm text-gray-500">${media[i].path.split('/')[1]}</span>
    <button type="button" onclick="removeFileEdit('${id}', '${ media[i].mediaid }')" class="text-sm text-red-500 hover:text-red-700">Remove</button>
  `;

 
  fileDisplay.appendChild(mediaRemove);
}

const newFilesSection = document.createElement('div');
newFilesSection.classList.add("flex-col", "gap-2");
newFilesSection.setAttribute('id', 'newFiles-' + id);
fileDisplay.appendChild(newFilesSection);

post.appendChild(formContainer);

return post
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

//creates a new group container with all the needed info
function createGroup(groupInfo){
let group = document.createElement('div');
group.classList.add("group", "mb-4", "p-4", "bg-white", "rounded-md", "shadow-md");

group.innerHTML= `
  <div class="group mb-4 p-4 bg-white rounded-md shadow-md">
      <div class="group-header mb-2">
          <h3 class="font-bold">${groupInfo.groupname }}</h3>
      </div>
      <div class="group-body mb-2">
          <p>${groupInfo.description }</p>
      </div>
  </div>
`;
}

//creates a new comment container with all the needed info
function createComment(commentInfo){
  const comment = document.createElement('div');
  comment.classList.add("mb-4", "p-4", "bg-white", "rounded-md", "shadow");

  comment.innerHTML = `
    <div class="flex justify-between items-center">
      <h3 class="font-bold text-gray-800">${ commentInfo.user.username}</h3>
    </div>
    <span class="text-sm text-gray-500">${ commentInfo.createddate}</span>
    <p class="mt-2 text-gray-700">${ commentInfo.message }</p>
  `;

  const reply = document.createElement('p');
  reply.classList.add("text-sm", "hover:text-sky-900");

  if(commentInfo.parent_comment !== null){

    reply.innerHTML = `
      <strong>Replying to:</strong>
      ${ commentInfo.parent_comment.user.username }
    `;
  }
  else{

    reply.innerHTML = `
      <strong>Replying to:</strong>
      ${ commentInfo.post.user.username }`;
  }

  const commentInfoContainer = comment.querySelector("div");
  commentInfoContainer.appendChild(reply);

  return comment;
}

//inserts more users into an element
function insertMoreUsers(element, users){
  for(let i = 0; i < users.data.length; i++){
    let user = createUser(users.data[i]);
    element.appendChild(user);
    }
}

//inserts more groups into and element
function insertMoreGroups(element, groups){
for(let i = 0; i < groups.data.length; i++){
  let group = createUser(groups.data[i]);
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
      post = createPostOptions(post, posts.data[i].postid); 
    }

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
}

//inserts more comments into an element
function insertMoreComments(element, comments){
  for(let i = 0; i < comments.data.length; i++){
    let comment = createComment(comments.data[i]);
    element.appendChild(comment);

  }

}

//inserts more liked contents into an element
function insertMoreLikedContent(element, likes){
  
  for(let i = 0; i < likes.data.length; i++){
    if(likes.data[i].post !== null){
      let post = createPost(likes.data[i].post);
      element.append(post);
    }
    else{
      let comment = createComment(likes.data[i].comment);
      element.append(comment);
    }
  }
}

//inserts more posts into the timeline
function insertMoreTimeline(){
removeLoadingCircle(); //remove the circle because we already have the data
const timeline = document.querySelector("#timeline");
let posts = JSON.parse(this.responseText);


maxPage = posts.last_page; //used to stop send requests when maximum page is reached

insertMorePosts(timeline,posts);

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

//inserts more content into the profile page
function insertMoreProfileContent(){
  removeLoadingCircle();//remove the circle because we already have the data
  const profileContent = document.querySelector("#profile-tab-content");

  let results = JSON.parse(this.responseText);

  if(!isPublic && !isadmin){
    profileContent.innerHTML = `
    <div class="flex justify-center items-center h-32">
            <p class="text-gray-600 text-center">Account is private.</p>
    </div>
    `;
    return;   
  }

  switch(profileTab){

    case 'user-posts':
      maxPage = results.last_page; 
      insertMorePosts(profileContent,results);
      break;

    case 'user-comments':
      maxPage = results.last_page;
      insertMoreComments(profileContent,results);
      break;

    case 'user-likes':
      maxPage = results.last_page;
      insertMoreLikedContent(profileContent,results);
      break;

    default:
      return;
  }

  if(profileContent.firstChild == null){
    profileContent.innerHTML = `
      <div class="flex justify-center items-center h-32">
              <p class="text-gray-600 text-center">No ${profileTab == 'user-posts' ? 'posts' : (profileTab == 'user-comments' ? 'comments' : 'liked content')} found for this user.</p>
      </div>
      `;       
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
  }
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

//loads the first content of a search when selecting another category
function loadProfileContent(category){
  const profileContent = document.querySelector("#profile-tab-content");
  if(profileContent == null){
    return;
  }

  while (profileContent.firstChild) {
    profileContent.removeChild(profileContent.firstChild);
  }

  insertLoadingCircle(profileContent);

  switch(category){
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
}
  
//fades the alert messages after a certain period of time
function fadeAlert(){
const alertBoxes = document.querySelectorAll('.alert');
  alertBoxes.forEach(alertBox => {
      setTimeout(() => {
          alertBox.remove()
      }, 3000); 
    });// Time before fade-out
}

const html = document.documentElement;

//toggles the edit menu when user clicks the edit button
function toggleEditMenu() {
const menu = document.getElementById('edit-profile-menu');
menu.classList.toggle('hidden');
menu.classList.toggle('flex');
html.classList.toggle('overflow-hidden');
}

const buttons = document.querySelectorAll('.tab-btn');
const sections = document.querySelectorAll('.tab-content');
let profileTab = "user-posts";

function switchProfileTab() {
  buttons.forEach(button => {
    button.addEventListener('click', () => {
      currentPage = 1;
      profileTab = button.dataset.tab;

      // Toggle active button
      buttons.forEach(btn => {
        btn.classList.remove('text-sky-900', 'border-sky-900');
      });
      button.classList.add('text-sky-900', 'border-sky-900');
      loadProfileContent(profileTab);
      
      });
    });
  }

const navigationMenu = document.getElementById('navigation-menu');
const menuText = document.querySelectorAll("#navigation-menu span");
const menuOptions = document.querySelectorAll("#navigation-menu li");
const menuHeader = document.querySelector("#navigation-menu header");
const menuArrow = document.querySelector("#navigation-menu header button > svg");

//Allows the expantion of the menu
function navigationMenuOperation(){
if(navigationMenu.classList.contains("lg:w-60")){
  navigationMenu.classList.remove("lg:w-60");
  navigationMenu.classList.add("lg:w-14");
}
else{
  navigationMenu.classList.add("lg:w-60");
  navigationMenu.classList.remove("lg:w-14");
}
menuText.forEach(function(element){
  element.classList.toggle("hidden");
})
menuOptions.forEach(function(option){
  option.classList.toggle("gap-3");
})
menuArrow.classList.toggle("rotate-180");
}

const searchMenu = document.getElementById('search-menu');
const searchBar = document.getElementById('search-bar');
const searchIcon = document.getElementById('search-icon');
const searchMenuArrow = document.querySelector("#search-menu header button > svg");

let searchCategory = null;
if(document.querySelector('input[name="category"]') !== null){
  searchCategory = document.querySelector('input[name="category"]').value;
}

//allows the operation of the search menu
function searchMenuOperation(){
  if(searchMenu.classList.contains("lg:w-60")){
    searchMenu.classList.remove("lg:w-60");
    searchMenu.classList.add("lg:w-14");
    searchBar.classList.add("hidden");
    searchIcon.classList.remove("hidden");
  }
  else{
    searchMenu.classList.add("lg:w-60");
    searchMenu.classList.remove("lg:w-14");
    searchBar.classList.remove("hidden");
    searchIcon.classList.add("hidden");
  }
  searchMenuArrow.classList.toggle("rotate-180");
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
  
  //Create Post Helper
  let selectedFiles = [];

function updateFileList() {
    const fileInput = document.getElementById('image');
    const fileDisplay = document.getElementById('fileDisplay');

    // Append new files to the list (preserve existing files)
    Array.from(fileInput.files).forEach(file => {
      if (file.size > 2097152){
        alert('File too big');
      }
      else{
        selectedFiles.push(file);
      }
    });

    // Check if there are more than 4 files
    if (selectedFiles.length > 4) {
      alert('You can only select up to 4 files.');
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
          <button type="button" onclick="removeSpecificFile(${index})" class="text-sm text-red-500 hover:text-red-700">Remove</button>
      `;
      fileDisplay.appendChild(li);
    });

    fileDisplay.classList.remove('hidden');

    // Reset the file input to allow adding more files
    fileInput.value = '';
  }

  //Create Post Helper
  function removeSpecificFile(index) {
    const fileDisplay = document.getElementById('fileDisplay');

    // Remove file from the list
    selectedFiles.splice(index, 1);

    // Clear previous display and update with new list
    fileDisplay.innerHTML = '';
    selectedFiles.forEach((file, i) => {
        const li = document.createElement('li');
        li.classList.add('flex', 'items-center', 'gap-2');

        li.innerHTML = `
            <span class="text-sm text-gray-500">${file.name}</span>
            <button type="button" onclick="removeSpecificFile(${i})" class="text-sm text-red-500 hover:text-red-700">Remove</button>
        `;
        fileDisplay.appendChild(li);
    });

    // Hide the display if no files remain
    if (selectedFiles.length === 0) {
        fileDisplay.classList.add('hidden');
    }
  }

  function syncFilesWithInputEventListener(){
    // Synchronize selectedFiles with the file input before form submission
    document.querySelector('form').addEventListener('submit', function (e) {
      
      if (selectedFiles.length > 4) {
        e.preventDefault(); // Prevent the form from submitting
        alert('You can only submit up to 4 files.');
        return; 
      }

      const fileInput = document.getElementById('image');
      const dataTransfer = new DataTransfer();

      // Append all files from selectedFiles to the new DataTransfer object
      selectedFiles.forEach(file => {
          dataTransfer.items.add(file);
      });

      // Update the file input's files property
      fileInput.files = dataTransfer.files;
    });
  }


  //used to switch from section in the admin page(only for the final product)
  function showSectionAdmin(sectionId) {
    /*
      document.querySelectorAll('.tab-section').forEach((el) => {
          el.classList.add('hidden');
      });

      document.getElementById(sectionId).classList.remove('hidden');
      */
    }

// Object to store the original values and files for each post
const originalFormData = {};

// Toggle the edit form visibility
function toggleEditPost(postid) {
    const editForm = document.getElementById(`edit-post-${postid}`);
    const postContent = document.getElementById(`post-content-${postid}`);
    const editFormFields = editForm.querySelectorAll('input, textarea, select'); // Editable fields

    // File-related elements
    const fileInput = document.getElementById(`image-${postid}`);
    const fileDisplay = document.getElementById(`fileDisplay-${postid}`);
    const newFilesContainer = document.getElementById(`newFiles-${postid}`);
    const removeMediaInput = document.getElementById(`removeMedia-${postid}`);

    // Toggle visibility
    editForm.classList.toggle('hidden');
    postContent.classList.toggle('hidden');

    // If showing the edit form, save original values
    if (!editForm.classList.contains('hidden')) {
        if (!originalFormData[postid]) {
            // Store the original values in the object
            originalFormData[postid] = {
                formValues: {},
                mediaFiles: fileDisplay.innerHTML, // Store current files displayed
                removeMedia: removeMediaInput.value, // Store any files marked for removal
            };
            editFormFields.forEach(field => {
                originalFormData[postid].formValues[field.name] = field.value;
            });
        }
    } else {
        // If hiding the edit form without saving, restore original values
        if (originalFormData[postid]) {
            // Restore original form values
            editFormFields.forEach(field => {
                field.value = originalFormData[postid].formValues[field.name];
            });

            // Restore original files
            fileDisplay.innerHTML = originalFormData[postid].mediaFiles;
            removeMediaInput.value = originalFormData[postid].removeMedia;
            
            // Clear new files added during the edit session
            if (newFilesContainer) {
                newFilesContainer.innerHTML = "";
            }

            // Reset the file input
            fileInput.value = "";
        }

        // Reset file input and media display if switching from edit form to post content
        selectedFilesEdit = []; // Reset media selection when returning to the post content view
    }
}



// Open delete confirmation menu
function openDeleteMenu(postid) {
    const deleteMenu = document.getElementById('deleteMenu');
    deleteMenu.classList.remove('hidden');
    document.documentElement.classList.add('overflow-hidden'); // Ensure the whole page is locked
    window.selectedPostId = postid;
}

let selectedFilesEdit = [];  // New files selected by user (not yet submitted)

// Edit Post Helpers
function updateFileNameEdit(postId) {
  const fileInput = document.getElementById(`image-${postId}`);
  const fileDisplay = document.getElementById(`fileDisplay-${postId}`);
  const newFileDisplay = document.getElementById(`newFiles-${postId}`)

  // Append new files to the list (preserve existing files)

  Array.from(fileInput.files).forEach(file => {
    console.log(file.size);
    if (file.size > 2097152){
      alert('File too big');
    }
    else{
      selectedFilesEdit.push(file);
    }
  });

  
  const lenStoreMedia = fileDisplay.querySelectorAll('div').length - 1;
  console.log(lenStoreMedia);
  // Check if there are more than 4 files
  console.log(lenStoreMedia + selectedFiles.length);
  if (lenStoreMedia + selectedFilesEdit.length > 4) {
      alert('You can only select up to 4 files.');
      // Remove the newly added files from the selectedFiles array
      selectedFilesEdit.splice(-fileInput.files.length);
      return; 
  }

  // Clear previous file list
  newFileDisplay.innerHTML = '';

  // Show updated list of file names
  console.log(selectedFilesEdit);
  selectedFilesEdit.forEach((file, index) => {
    if(newFileDisplay.classList.contains('hidden')){
      newFileDisplay.classList.toggle('hidden');
    }
      const li = document.createElement('li');
      li.classList.add('flex', 'items-center', 'gap-2');

      li.innerHTML = `
          <span class="text-sm text-gray-500">${file.name}</span>
          <button type="button" onclick="removeSpecificFileEdit(${postId}, ${index})" class="text-sm text-red-500 hover:text-red-700">Remove</button>
      `;
      newFileDisplay.appendChild(li);
  });

  fileDisplay.classList.remove('hidden');
  fileInput.value = '';
}

function removeFileEdit(postId, mediaId) {
  const fileElement = document.getElementById(`file-${mediaId}`);
  const removeMediaInput = document.getElementById(`removeMedia-${postId}`);
  let removedFiles = JSON.parse(removeMediaInput.value);

  // Add the removed mediaId to the array for server processing
  removedFiles.push(mediaId);
  removeMediaInput.value = JSON.stringify(removedFiles);

  // Remove the media element from the display
  fileElement.remove();
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


//Admin Delete Post
document.querySelectorAll('.delete-post-button').forEach(button => {
  button.addEventListener('click', function(event) {
      const postId = event.target.getAttribute('data-post-id');
      const postMessage = event.target.getAttribute('data-post-message');

      openDeleteMenu(postId, postMessage);
  });
});

//closes the users posts delete menu
function closeDeleteMenu() {
  const modal = document.getElementById('deleteMenu');
  modal.classList.add('hidden');
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

//removes the file from the options in the edit post menu
function removeFileEdit(postId, mediaId) {
  const fileElement = document.getElementById(`file-${mediaId}`);
  const removeMediaInput = document.getElementById(`removeMedia-${postId}`);
  let removedFiles = JSON.parse(removeMediaInput.value);

  // Add the removed mediaId to the array for server processing
  removedFiles.push(mediaId);
  removeMediaInput.value = JSON.stringify(removedFiles);

  // Remove the media element from the display
  fileElement.remove();
}

function removeSpecificFileEdit(postId, index) {
  const fileDisplay = document.getElementById(`newFiles-${postId}`);

  // Remove file from the list
  selectedFilesEdit.splice(index, 1);

  // Clear previous display and update with new list
  fileDisplay.innerHTML = '';
  selectedFilesEdit.forEach((file, i) => {
      const li = document.createElement('li');
      li.classList.add('flex', 'items-center', 'gap-2');

      li.innerHTML = `
          <span class="text-sm text-gray-500">${file.name}</span>
          <button type="button" onclick="removeSpecificFileEdit(${postId}, ${i})" class="text-sm text-red-500 hover:text-red-700">Remove</button>
      `;
      fileDisplay.appendChild(li);
  });

  // Hide the display if no files remain
  if (selectedFilesEdit.length === 0) {
      fileDisplay.classList.add('hidden');
  }
}

// Synchronize selectedFilesEdit with the file input before form submission
function addEventListenerToPostForms(){

  if(document.querySelectorAll('.edit-post-form form').length === 0){
    //did not found post edit forms
    return;
  }

  document.querySelectorAll('.edit-post-form form').forEach(function (form) {
    addEventListenerToForm(form);
  });
}

//adds a event listener to a post form
function addEventListenerToForm(form){
  form.addEventListener('submit', function (e) {
    const postId = form.dataset.postId;
    const fileInput = document.getElementById(`image-${postId}`);
    
    // Check if there are more than 4 files, prevent submission
    if (selectedFilesEdit.length > 4) {
        e.preventDefault();
        alert('You can only submit up to 4 files.');
        return;
    }

    const dataTransfer = new DataTransfer();

    // Append all files from selectedFilesEdit to the new DataTransfer object
    selectedFilesEdit.forEach(file => {
        dataTransfer.items.add(file);
    });

    // Update the file input's files property
    fileInput.files = dataTransfer.files;

    // Make sure the form submits only the valid files
    // Here you can do additional checks if necessary (e.g., clearing out old files)
  });
}

//Admin Edit User
document.querySelectorAll('.edit-user-button').forEach(button => {
  button.addEventListener('click', function() {
      const userId = this.getAttribute('data-user-id');

      sendAjaxRequest('get',`/admin/users/${userId}/edit`, null, adminEditUser);
  })
});

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

//Delete Account
function toggleDropdown() {
  const dropdownMenu = document.getElementById('dropdownMenu');
  dropdownMenu.classList.toggle('hidden');
}

//toggles the confirmation menu so that it can appear on screen
function toggleConfirmationModal() {
  const confirmationMenu = document.getElementById('confirmationModal');
  confirmationMenu.classList.toggle('hidden');
  confirmationMenu.classList.toggle('flex');
  console.log("here");
  const dropdownMenu = document.getElementById('dropdownMenu');
  dropdownMenu.classList.toggle('hidden');
  if (isadmin) {
    togglePasswordForm();
  }
}

//used to close modal menu
function closeModal() {
  const confirmationMenu = document.getElementById('confirmationModal');
  confirmationMenu.classList.toggle('hidden');
  confirmationMenu.classList.toggle('flex');
}

//handles the profile delete confirmation with requests via ajax
function confirmDeleteProfile() {  
  if (isadmin) {
    document.getElementById('deleteProfileForm').submit();
  }
  else {
    const password = document.getElementById('password').value;

    if (!password) {
      document.getElementById('passwordError').classList.remove('hidden');
      document.getElementById('passwordError').innerText = 'Password is required.';
      return;
    }

    document.getElementById('deleteProfileForm').submit();
  }
    
}

//toggles the password form when it is needed in the delete user menu
function togglePasswordForm() {
  const passwordForm = document.getElementById('passwordForm');
  if(passwordForm.classList.contains('hidden')){
    return;
  }
  passwordForm.classList.toggle('hidden');
}

addEventListeners();
