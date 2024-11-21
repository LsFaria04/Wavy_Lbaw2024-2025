function addEventListeners() {
    let itemCheckers = document.querySelectorAll('article.card li.item input[type=checkbox]');
    [].forEach.call(itemCheckers, function(checker) {
      checker.addEventListener('change', sendItemUpdateRequest);
    });
  
    let itemCreators = document.querySelectorAll('article.card form.new_item');
    [].forEach.call(itemCreators, function(creator) {
      creator.addEventListener('submit', sendCreateItemRequest);
    });
  
    let itemDeleters = document.querySelectorAll('article.card li a.delete');
    [].forEach.call(itemDeleters, function(deleter) {
      deleter.addEventListener('click', sendDeleteItemRequest);
    });
  
    let cardDeleters = document.querySelectorAll('article.card header a.delete');
    [].forEach.call(cardDeleters, function(deleter) {
      deleter.addEventListener('click', sendDeleteCardRequest);
    });
  
    let cardCreator = document.querySelector('article.card form.new_card');
    if (cardCreator != null)
      cardCreator.addEventListener('submit', sendCreateCardRequest);

    document.addEventListener('DOMContentLoaded', fadeAlert);
    document.addEventListener('DOMContentLoaded', switchProfileTab);
    document.addEventListener("scroll", infiniteScroll);
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
  request.addEventListener('load', handler);
  request.send(encodeForAjax(data));
}

function sendItemUpdateRequest() {
  let item = this.closest('li.item');
  let id = item.getAttribute('data-id');
  let checked = item.querySelector('input[type=checkbox]').checked;

  sendAjaxRequest('post', '/api/item/' + id, {done: checked}, itemUpdatedHandler);
}

function sendDeleteItemRequest() {
  let id = this.closest('li.item').getAttribute('data-id');

  sendAjaxRequest('delete', '/api/item/' + id, null, itemDeletedHandler);
}

function sendCreateItemRequest(event) {
  let id = this.closest('article').getAttribute('data-id');
  let description = this.querySelector('input[name=description]').value;

  if (description != '')
    sendAjaxRequest('put', '/api/cards/' + id, {description: description}, itemAddedHandler);

  event.preventDefault();
}

function sendDeleteCardRequest(event) {
  let id = this.closest('article').getAttribute('data-id');

  sendAjaxRequest('delete', '/api/cards/' + id, null, cardDeletedHandler);
}

function sendCreateCardRequest(event) {
  let name = this.querySelector('input[name=name]').value;

  if (name != '')
    sendAjaxRequest('put', '/api/cards/', {name: name}, cardAddedHandler);

  event.preventDefault();
}

function itemUpdatedHandler() {
  let item = JSON.parse(this.responseText);
  let element = document.querySelector('li.item[data-id="' + item.id + '"]');
  let input = element.querySelector('input[type=checkbox]');
  element.checked = item.done == "true";
}

function itemAddedHandler() {
  if (this.status != 200) window.location = '/';
  let item = JSON.parse(this.responseText);

  // Create the new item
  let new_item = createItem(item);

  // Insert the new item
  let card = document.querySelector('article.card[data-id="' + item.card_id + '"]');
  let form = card.querySelector('form.new_item');
  form.previousElementSibling.append(new_item);

  // Reset the new item form
  form.querySelector('[type=text]').value="";
}

function itemDeletedHandler() {
  if (this.status != 200) window.location = '/';
  let item = JSON.parse(this.responseText);
  let element = document.querySelector('li.item[data-id="' + item.id + '"]');
  element.remove();
}

function cardDeletedHandler() {
  if (this.status != 200) window.location = '/';
  let card = JSON.parse(this.responseText);
  let article = document.querySelector('article.card[data-id="'+ card.id + '"]');
  article.remove();
}

function cardAddedHandler() {
  if (this.status != 200) window.location = '/';
  let card = JSON.parse(this.responseText);

  // Create the new card
  let new_card = createCard(card);

  // Reset the new card input
  let form = document.querySelector('article.card form.new_card');
  form.querySelector('[type=text]').value="";

  // Insert the new card
  let article = form.parentElement;
  let section = article.parentElement;
  section.insertBefore(new_card, article);

  // Focus on adding an item to the new card
  new_card.querySelector('[type=text]').focus();
}

function createCard(card) {
  let new_card = document.createElement('article');
  new_card.classList.add('card');
  new_card.setAttribute('data-id', card.id);
  new_card.innerHTML = `

  <header>
    <h2><a href="cards/${card.id}">${card.name}</a></h2>
    <a href="#" class="delete">&#10761;</a>
  </header>
  <ul></ul>
  <form class="new_item">
    <input name="description" type="text">
  </form>`;

  let creator = new_card.querySelector('form.new_item');
  creator.addEventListener('submit', sendCreateItemRequest);

  let deleter = new_card.querySelector('header a.delete');
  deleter.addEventListener('click', sendDeleteCardRequest);

  return new_card;
}



function createItem(item) {
  let new_item = document.createElement('li');
  new_item.classList.add('item');
  new_item.setAttribute('data-id', item.id);
  new_item.innerHTML = `
  <label>
    <input type="checkbox"> <span>${item.description}</span><a href="#" class="delete">&#10761;</a>
  </label>
  `;

  new_item.querySelector('input').addEventListener('change', sendItemUpdateRequest);
  new_item.querySelector('a.delete').addEventListener('click', sendDeleteItemRequest);

  return new_item;
}

//stores the authentication state
let isAuthenticated = false; 
let userId = -1;
sendAjaxRequest('post', '/auth-check', null, isAuth);
function isAuth(){
  const response = JSON.parse(this.responseText);
  isAuthenticated = response.authenticated;
  if(isAuthenticated){
    sendAjaxRequest('post', '/auth-id', null, authId);
  }
}
function authId(){
  const response = JSON.parse(this.responseText);
  userId = response.id;
}

//gets the csrf token to insert in new forms
function getCsrfToken(){
  return document.querySelector('meta[name="csrf-token"]').content;
}



//creates the a post container with the message, username and date
function createPost(postInfo){
  let post = document.createElement('div');
  post.classList.add("post", "mb-4", "p-4", "bg-white", "rounded-md", "shadow");

  post.innerHTML = `
    <div class="post-header mb-2 flex justify-between items-center">
        <div>
            <h3 class="font-bold">
                <a href="{{ route('profile', $post->user->username) }}" class="text-black hover:text-sky-900">
                    ${ postInfo.user.username }
                </a>
            </h3>
            <span class="text-gray-500 text-sm">${ postInfo.createddate }</span>
        </div>
    </div>
     <div class="post-body mb-2">
        <p>${ postInfo.message }</p>
      </div>
  `;


  return post

}

//creates a delete button and inserts it into a post. Returns the updated post
function createDeletePostButton(post, id){
  const postheader = post.querySelector('.post-header');
  let buttom = document.createElement('div');
  buttom.classList.add("flex", "items-center");

  buttom.innerHTML = `
    <form action="../posts/delete/${id}" method="POST" onsubmit="return confirmDelete()">
      <button type="submit" class="text-red-500 hover:text-red-700 ml-2">
          <input type="hidden" name="_token" value= ${getCsrfToken()} />
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
      </button>
    </form>
  `
  postheader.appendChild(buttom);
  return post;
}

//inserts the media (images, audio and video) of a post into a post container. Returns the updated post container
function insertPostMedia(post, mediaArray){
  const postbody = post.querySelector('.post-body');
  let mediaContainer = document.createElement('div');
  mediaContainer.classList.add("post-media", "mt-4");

  for(let i = 0; i < mediaArray.length; i++){
    let media = mediaArray[i];
    let fileExtension = media.path.split('.').pop();

    let newMedia = document.createElement('img');

    
    if(['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)){
      newMedia.setAttribute('alt', 'Image');
      newMedia.setAttribute('src', "storage/" + media.path);
      newMedia.classList.add("w-full", "h-auto", "rounded-md", "mb-2");
    }

    else if(['mp4', 'avi', 'mov'].includes(fileExtension)){
      newMedia = document.createElement('video');
      newMedia.setAttribute("controls", "");
      let source = document.createElement('source');
  
      source.setAttribute('src', "storage/" + media.path);
      source.setAttribute('type', "video/" + fileExtension);

      newMedia.appendChild(source);
      newMedia.classList.add("w-full", "h-auto", "rounded-md", "mb-2");
    }

    else if(['mp3', 'wav', 'ogg'].includes(fileExtension)){
      newMedia = document.createElement('audio');
      let source = document.createElement('source');
    
      source.setAttribute('src', "storage/" + media.path);
      source.setAttribute('type', "audio/" + fileExtension);

      newMedia.appendChild(source);
      newMedia.classList.add("w-full","mb-2");
    }

    else{
      newMedia = document.createElement('p');
      newMedia.classList.add("text-gray-500");
      newMedia.innerHTML = 'Unsupported media type';
    }

    postbody.appendChild(newMedia);
  }

  return post;
}

//Inserts the update post button into a post container. Return the updated post container
function insertUpdateButton(post, id){
  const postbody = post.querySelector('.post-body');
  let button = document.createElement('button');
  button.classList.add("px-4", "py-2", "bg-gray-500", "text-white", "rounded-3xl", "hover:bg-gray-600", "mt-4");
  button.setAttribute('onclick', `toggleEditPost(${id})`);
  button.innerHTML = 'Edit Post';
  postbody.appendChild(button);
}

//inserts the update post form into a post container. Return the updated post container.
function insertUpdateForm(post, id, message){
  const postbody = post.querySelector('.post-body');
  let formContainer = document.createElement('div');
  formContainer.classList.add("edit-post-form", "hidden", "mt-4");
  formContainer.setAttribute('id',"edit-post-" + id);

  formContainer.innerHTML = `
    <form action="../posts/update/${id}" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="_token" value= ${getCsrfToken()} />
        <div class="mb-4">
            <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
            <textarea id="message" name="message" rows="3" class="mt-1 block w-full p-2 border rounded-md" placeholder="Edit your message">${ message}</textarea>
        </div>

        <div class="mb-4">
            <label for="image" class="block text-sm font-medium text-gray-700">Upload Image (optional)</label>
            <input type="file" name="image" id="image" accept="image/*" class="mt-1 block w-full p-2 border rounded-md">
        </div>

        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Update Post</button>
    </form>
  `

  postbody.appendChild(formContainer);

  return post
}

//inserts more posts into the timeline
function insertMorePosts(){
  removeLoadingCircle(); //remove the circle because we already have the data
  const timeline = document.querySelector("#timeline");
  let posts = JSON.parse(this.responseText);

  maxPage = posts.last_page; //used to stop send requests when maximum page is reached

  for(let i = 0; i < posts.data.length; i++){
      let post = createPost(posts.data[i]);

      if(userId == posts.data[i].user.userid){
        post = createDeletePostButton(post, posts.data[i].postid); 
      }

      post = insertPostMedia(post, posts.data[i].media);

      if(userId == posts.data[i].user.userid){
        insertUpdateButton(post, posts.data[i].postid);
        insertUpdateForm(post, posts.data[i].postid, posts.data[i].message);
      }
      timeline.appendChild(post);
  }

}

function insertLoadingCircle(){
  if(document.querySelector(".loading_circle") !== null){
    //already exists a loading circle
    return;
  }
  const timeline = document.querySelector("#timeline");
  let loadingCircle = document.createElement("div");

  loadingCircle.classList.add("loading_circle","ml-auto", "mr-auto", "inline-block", "h-8", "w-8", "animate-spin", "rounded-full", "border-4", "border-solid", "border-current", "border-e-transparent", "align-[-0.125em]", "text-primary", "motion-reduce:animate-[spin_1.5s_linear_infinite]");
  loadingCircle.setAttribute('role', "status");
  loadingCircle.innerHTML = `
              <spanclass="!absolute !-m-px !h-px !w-px !overflow-hidden !whitespace-nowrap !border-0 !p-0 ![clip:rect(0,0,0,0)]">
              </span>
  `

  timeline.appendChild(loadingCircle);
}

function removeLoadingCircle(){
  let loadingCircles = document.querySelectorAll(".loading_circle");
  loadingCircles.forEach((loadingCircle) => loadingCircle.remove());
}

let currentPage = 1;
let maxPage = -1;
//check if the we have reached the end of the page and takes the apropriate actions
function infiniteScroll(){ 
  if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 1) {
      
      //action to take place in the home page
      const timeline = document.querySelector("#timeline");
      if((timeline !== null) && (maxPage > currentPage || (maxPage == -1))  ){
        currentPage += 1;
        insertLoadingCircle();
        sendAjaxRequest('get', '/api/posts?page=' + currentPage, null, insertMorePosts);
      }
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

//toggles the edit menu when user clicks the edit button
function toggleEditMenu() {
  const menu = document.getElementById('edit-profile-menu');
  const html = document.documentElement;
  menu.classList.toggle('hidden');
  html.classList.toggle('overflow-hidden');
}
  
  addEventListeners();

const buttons = document.querySelectorAll('.tab-btn');
const sections = document.querySelectorAll('.tab-content');

function switchProfileTab() {
  buttons.forEach(button => {
    button.addEventListener('click', () => {
      const targetTab = button.dataset.tab;

      // Toggle active button
      buttons.forEach(btn => {
        btn.classList.remove('text-sky-900', 'border-sky-900');
      });
      button.classList.add('text-sky-900', 'border-sky-900');

        // Toggle visible content
        sections.forEach(section => {
          if (section.id === targetTab) {
            section.classList.remove('hidden');
          } else {
            section.classList.add('hidden');
          }
        });
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

  function changeCategory(category) {
      document.querySelector('input[name="category"]').value = category;

      const buttons = document.querySelectorAll('.category-btn');
      buttons.forEach(button => {
          if (button.dataset.category === category) {
              button.classList.add('text-sky-900', 'border-sky-900');
          } else {
              button.classList.remove('text-sky-900', 'border-sky-900');
          }
      });

      document.getElementById('search-form').submit();
  }

  function updateFileName() {
    const fileInput = document.getElementById('image');
    const fileNameDisplay = document.getElementById('fileName');
    const fileDisplay = document.getElementById('fileDisplay');
    const file = fileInput.files[0];

    if (file) {
        // Show the file name and remove button
        fileNameDisplay.textContent = file.name;
        fileDisplay.classList.remove('hidden');
    } else {
        // Hide the file display section
        fileDisplay.classList.add('hidden');
    }
  }

  function removeFile() {
    const fileInput = document.getElementById('image');
    const fileDisplay = document.getElementById('fileDisplay');

    // Reset the file input and hide the file display section
    fileInput.value = '';
    fileDisplay.classList.add('hidden');
  }


  function showSectionAdmin(sectionId) {
    document.querySelectorAll('.tab-section').forEach((el) => {
        el.classList.add('hidden');
    });

    document.getElementById(sectionId).classList.remove('hidden');
  }
  // Toggle the edit form visibility
    function toggleEditPost(postid) {
    const editForm = document.getElementById(`edit-post-${postid}`);
    editForm.classList.toggle('hidden'); 
  }
  // Confirm delete dialog
  function confirmDelete() {
      return confirm('Are you sure you want to delete this post? This action cannot be undone.');
  }
