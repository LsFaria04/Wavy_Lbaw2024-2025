function addEventListeners() {
  document.addEventListener('DOMContentLoaded', switchProfileTab);
  document.addEventListener('DOMContentLoaded', switchGroupTab);
  window.addEventListener("scroll", infiniteScroll);
  imageCropper();

  document.addEventListener('DOMContentLoaded', toggleFollow);

}

function toggleHidden(element) {
  element.classList.toggle('hidden');
  element.classList.toggle('flex');
}


function handleProfileInfo(){
  const response = JSON.parse(this.responseText);
  isPublic = response.visibilitypublic;
}

//store user profile info (only accessed if we enter a profile)
let isPublic = false;
let username = "";
if(document.querySelector("#profile-tab-content") !== null){
  username = document.getElementById('profile-username').innerHTML;
  sendAjaxRequest('get', '/api/' + username, null, handleProfileInfo);
}

//toggles the edit menu when user clicks the edit button
function toggleEditMenu() {
  const menu = document.getElementById('edit-profile-menu');
  toggleHidden(menu);
  html.classList.toggle('overflow-hidden');
}

//Delete Account
function toggleDropdown() {
  const dropdownMenu = document.getElementById('dropdownMenu');
  dropdownMenu.classList.toggle('hidden');
}

//toggles the confirmation menu so that it can appear on screen
function toggleConfirmationModal() {
  const confirmationMenu = document.getElementById('confirmationModal');
  toggleHidden(confirmationMenu);
  const dropdownMenu = document.getElementById('dropdownMenu');
  dropdownMenu.classList.toggle('hidden');
  if (isadmin) {
    togglePasswordForm();
  }
}

//used to close modal menu
function closeModal() {
  const confirmationMenu = document.getElementById('confirmationModal');
  toggleHidden(confirmationMenu);
}

//opens the my topics menu in the profile
function toggleMyTopics(){
  const myTopicsMenu = document.getElementById('myTopics');

  if(myTopicsMenu.classList.contains('hidden')){
    myTopicPage = 0;
    loadMoreTopics(true);

  }
  else{

    let topics = document.querySelectorAll('#myTopicsList .topicList li,#myTopicsList p ');
    topics.forEach(function (e) {e.remove()}); 
    
  }

  toggleHidden(myTopicsMenu);
}

//opens the submenu add topic in the profile page
function toggleAddTopics(){
  //show add topics menu
  const addTopicsMenu = document.getElementById('addTopics');

  if(addTopicsMenu.classList.contains('hidden')){
    addTopicPage = 0;
    loadMoreTopics(false);

  }
  else{
    let topics = document.querySelectorAll('#topicsList .topicList li, #topicsList p');
    topics.forEach( function (e) {e.remove()});
  }

  toggleHidden(addTopicsMenu);

  //hide or show the my topics menu
  const myTopicsMenu  = document.getElementById('myTopics');
  toggleHidden(myTopicsMenu);
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

//inserts more content into the profile page
function insertMoreProfileContent(){
  removeLoadingCircle();//remove the circle because we already have the data
  const profileContent = document.querySelector("#profile-tab-content");

  let results = JSON.parse(this.responseText);

  if((!isPublic && !isadmin) && (username != currentUsername)){
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

//loads more topics from the database and calls the insert more topics
let isMyTopics = true;
let myTopicPage = 0;
let myTopicPageMax = -1;
let addTopicPage = 0;
let addTopicPageMax = -1;
let searchQuery = "";
let isQuery = false;
function loadMoreTopics(isMy){
  isMyTopics = isMy;

  let topicsList = null;
  if(isMyTopics){
    topicsList = document.querySelector("#myTopicsList > ul");
  }
  else{
    topicsList = document.querySelector("#topicsList > ul");
  }
  insertLoadingCircle(topicsList);

  
  if(isQuery){
    if(isMyTopics){
      myTopicPage++;
      sendAjaxRequest('get', '/api/topics/search/' + userId +'?page=' + myTopicPage + '&q=' + searchQuery, null,insertMoreTopics);
    }
    else{
      addTopicPage++;
      sendAjaxRequest('get', '/api/topics/search/canAdd/' + userId + '?page=' + addTopicPage + '&q=' + searchQuery,null, insertMoreTopics);
    }   
  }
  else{
    if(isMyTopics){
      myTopicPage++;
      sendAjaxRequest('get', '/api/topics/' + userId +'?page=' + myTopicPage, null,insertMoreTopics);
    }
    else{
      addTopicPage++;
      sendAjaxRequest('get', '/api/topics/canAdd/' + userId + '?page=' + addTopicPage,null, insertMoreTopics);
    }
    
  }


}

//insert topics in the topics list in the topics menu (my topics and add topics)
function insertMoreTopics(){
    removeLoadingCircle();
    
    let topics = JSON.parse(this.responseText);

    //received a response from the server that needs to be displayed (error messages)
    if(topics.response !== undefined){
      alert(topics.message);
      return;
    }

    let topicsList = null;
    if(isMyTopics){
      myTopicPageMax = topics.last_page;
      topicsList = document.querySelector("#myTopicsList > ul");
      
      //already loaded everything from the db. Hide the button
      if(myTopicPageMax == myTopicPage){
        if(!document.querySelector('#myTopicsList > button').classList.contains('hidden')){
          document.querySelector('#myTopicsList > button').classList.toggle('hidden');
        }
      }
    }
    else{
      addTopicPageMax = topics.last_page;
      topicsList = document.querySelector("#topicsList > ul");

      //already loaded everything from the db. Hide the button
      if(addTopicPageMax == addTopicPage){
        if(!document.querySelector('#topicsList > button').classList.toggle('hidden')){
          document.querySelector('#topicsList > button').classList.toggle('hidden');
        }
      }
    }

    //iterate throw the topics and add them into the list
    for(let i = 0; i < topics.data.length; i++){
      //do not show the general topic because it is the default
      if(topics.data[i].topicid === 1){
        continue;
      }
      let topic = createTopic(topics.data[i], isMyTopics, false,null);
      topicsList.appendChild(topic, isMyTopics);
    }

    //show the more topics button again if we found more topics. We also display a warning if no topics were found
    if(topics.data.length > 0){

      //show the button to load more topics if it is not on the screen
      if(isMyTopics && (myTopicPageMax > myTopicPage)){
        if(document.querySelector('#myTopicsList > button').classList.contains('hidden')){
          document.querySelector('#myTopicsList > button').classList.toggle('hidden');
        }
      }
      else if (!isMyTopics && (addTopicPageMax > addTopicPage)){
        if(document.querySelector('#topicsList > button').classList.contains('hidden')){
          document.querySelector('#topicsList > button').classList.toggle('hidden');
        }
      }
        
    }
    else{
      //there are no topics in the list and we could not found new ones with the ajax request so a warning is displayed
      if(topicsList.querySelector('p') == null && topicsList.querySelector('li') == null){
        let warning = document.createElement('p');
        warning.innerHTML='No topics found';
        topicsList.appendChild(warning);
      }
      
      //hide the button if it isn't hidden
      if(isMyTopics){
        if(!document.querySelector('#myTopicsList > button').classList.contains('hidden')){ 
        document.querySelector('#myTopicsList > button').classList.toggle('hidden');
        }
      }
      else{
        if(!document.querySelector('#topicsList > button').classList.contains('hidden')){
        document.querySelector('#topicsList > button').classList.toggle('hidden');
        }
      }
    }

}

//creates a new topic with a layout that depends on the page where the topic is going to be inserted
function createTopic(topicInfo, isMyTopics, isFromPosts, postid){
  let topic = document.createElement('li');
  topic.classList.add("w-full","flex","justify-between", "p-2", "my-2", "shadow");
  topic.setAttribute('id',`topic-${topicInfo.topicid}`)

  if(isMyTopics){
    topic.innerHTML = `
      <p id = "topic-${topicInfo.topicid}" class="text-gray-800 font-semibold">${topicInfo.topicname}</p>
      <button onclick=removeTopicFromUser(${topicInfo.topicid}) class="text-red-500 hover:text-red-700 ml-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
      </button>
    `;
  }
  else{
    topic.innerHTML = `
      <p id = "topic-${topicInfo.topicid}" class="text-gray-800 font-semibold">${topicInfo.topicname}</p>
      <button ${isFromPosts ? `onclick = "addTopicToPost(${topicInfo.topicid}, '${topicInfo.topicname}', ${postid})"` : `onclick=addTopicToUser(${topicInfo.topicid})`} class="text-green-500 hover:text-green-700 ml-2">
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve" width="20" height="20">
              <g>
                  <path d="M480,224H288V32c0-17.673-14.327-32-32-32s-32,14.327-32,32v192H32c-17.673,0-32,14.327-32,32s14.327,32,32,32h192v192   c0,17.673,14.327,32,32,32s32-14.327,32-32V288h192c17.673,0,32-14.327,32-32S497.673,224,480,224z" fill= "currentColor"/>
              </g>
          </svg>
      </button>
    `;
  }


  return topic;
}

  //loads the first content of a search when selecting another category
  function loadProfileContent(category){
    const profileContent = document.querySelector("#profile-tab-content");
    if(!profileContent) return;

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


const html = document.documentElement;

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


//search for all the topics
function searchTopics(event){
  event.preventDefault();
  addTopicPage = 0;
  isQuery = true;
  searchQuery = document.querySelector('#topicsSearch').value;
  
  //cancel the search if there is not a query
  if(searchQuery == ""){
    isQuery = false;
  }

  //remove the existing topics from the list that is being displayed to the user 
  let topics = document.querySelectorAll("#topicsList > ul li, #topicsList > ul p");
  topics.forEach( function (topic){
    topic.remove();
  })

  loadMoreTopics(false);
}

//search for topics that are associated to a user
function searchMyTopics(event){
  event.preventDefault();
  myTopicPage = 0;
  isQuery = true;
  searchQuery = document.querySelector('#myTopicsSearch ').value;
  
  //cancel the search if there is not a query
  if(searchQuery == ""){
    isQuery = false;
  }

  //remove the existing topics from the list that is being displayed to the user 
  let topics = document.querySelectorAll("#myTopicsList > ul li, #myTopicsList > ul p");
  topics.forEach( function (topic){
    topic.remove();
  })

  loadMoreTopics(true);
}

//adds more topics to a user using an ajax request and removing from the DOM in the add topic page and adding the topic in the DOM in my topics page
function addTopicToUser(topicId){
  sendAjaxRequest('put', '/api/topics/add/' + topicId + '/' + userId, null, function(){
    let response = JSON.parse(this.responseText);

    if(response.response == '200'){
      //remove element from the add topics page
      let topic = document.getElementById(`topic-${topicId}`);
      let topicName = topic.querySelector('p').innerHTML;
      topic.remove();

      //remove the warning if it is there
      let warning = document.querySelector("#myTopicsList .topicList > p");
      if(warning != null){
        warning.remove();
      }

      //add topic to the my topics page
      let newTopic = createTopic({'topicname' : topicName, 'topicid' : topicId }, true, false, null);
      let topicList = document.querySelector("#myTopicsList > ul");
      topicList.insertBefore(newTopic, topicList.firstChild);

      //insert the warning there are no more topics in the topics list
      let topicsList = document.querySelector("#topicsList > ul")
      if(topicsList.firstChild == null){
        let warning = document.createElement('p');
        warning.innerHTML='No topics found';
        topicsList.appendChild(warning);

        //hide button only if needed
        if(!topicsList.nextElementSibling.classList.contains('hidden')){
          topicsList.nextElementSibling.classList.toggle('hidden');
        }
      }
    }
    else{
      alert(response.message);
    }
  });
}

//removes a topic from the user using an ajax request and removing the topic from the DOM.
function removeTopicFromUser(topicId){
  sendAjaxRequest('delete', '/api/topics/remove/' + topicId + '/' + userId, null, function(){
    let response = JSON.parse(this.responseText);

    if(response.response == '200'){
      //remove element from the my topics page
      let topic = document.getElementById(`topic-${topicId}`);
      topic.remove();

      //insert the warning there are no more topics in the topics list
      let topicsList = document.querySelector("#myTopicsList > ul")
      if(topicsList.firstChild == null){
        let warning = document.createElement('p');
        warning.innerHTML='No topics found';
        topicsList.appendChild(warning);

        //hide button only if needed
        if(!topicsList.nextElementSibling.classList.contains('hidden')){
          topicsList.nextElementSibling.classList.toggle('hidden');
        }
      }
    }
    else{
      alert(response.message);
    }
  });
}

function toggleFollow() {
  const followButton = document.getElementById('follow-btn');

  followButton?.addEventListener('click', function(e) {
      e.preventDefault();  // Prevents the default behavior of the button (if it's in a form)

      let userId = followButton.getAttribute('data-userid');
      const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

      const followStatus = followButton.getAttribute('data-follow-status');  // Possible values: "following", "Pending", "not-following", "admin"
      const isPrivate = followButton.getAttribute('data-is-private') === 'true';  

      console.log("Follow Status:", followStatus);  // Debugging output
      console.log("Is Private:", isPrivate);        // Debugging output

      // if the user is trying to follow an admin, prevent it
      if (followStatus === 'admin') {
          alert('You cannot follow an admin!');
          return;
      }

      // Handle private profiles (only allow "Follow" or "Request Follow" based on the state)
      if (isPrivate && followStatus === 'not-following') {
        console.log("1")
        sendFollowRequest(userId, csrfToken, followButton);

      } else if (followStatus === 'following') {
        console.log("2")
          unfollowUser(userId, csrfToken, followButton);

      } else if (followStatus === 'Pending') {
        console.log("3")
          cancelPendingRequest(userId, csrfToken, followButton);

      } else {
        console.log("4")
          followUser(userId, csrfToken, followButton);
      }
  });
}

function sendFollowRequest(userId, csrfToken, followButton) {
  console.log("5")

  // Send the follow request (pending state)
  fetch('/profile/' + userId + '/follow', {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify({
          user_id: userId,
          state: 'Pending'  
      })
  })
  .then(response => {
      if (!response.ok) {
          throw new Error('Something went wrong with the follow request.');
      }
      return response.json();
  })
  .then(data => {
      if (data.success) {
          followButton.textContent = 'Pending Request';
          followButton.classList.remove('bg-sky-700', 'hover:bg-sky-900');
          followButton.classList.add('bg-yellow-500', 'hover:bg-yellow-700');
          followButton.setAttribute('data-follow-status', 'Pending');
      }
  })
  .catch(error => {
      console.error('Something went wrong with the follow request.', error);
  });
}

function followUser(userId, csrfToken, followButton) {
  console.log("6")

  // Send the follow request (accepted state)
  fetch('/profile/' + userId + '/follow', {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify({
          user_id: userId,
          state: 'Accepted' 
      })
  })
  .then(response => {
      if (!response.ok) {
          throw new Error('Something went wrong with the follow request.');
      }
      return response.json();
  })
  .then(data => {
      if (data.success) {
          followButton.textContent = 'Unfollow';  
          followButton.classList.remove('bg-sky-700', 'hover:bg-sky-900');
          followButton.classList.add('bg-red-500', 'hover:bg-red-700');
          followButton.setAttribute('data-follow-status', 'following');
      }
  })
  .catch(error => {
      console.error('Something went wrong with the follow request.', error);
  });
}

function unfollowUser(userId, csrfToken, followButton) {
  console.log("7");

  // Send the unfollow request (remove follow relationship)
  fetch('/profile/' + userId + '/unfollow', {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify({
          user_id: userId
      })
  })
  .then(response => {
      if (!response.ok) {
          throw new Error('Something went wrong with the unfollow request.');
      }
      return response.json();
  })
  .then(data => {
    if (data.success) {
      const isPrivate = followButton.getAttribute('data-is-private') === 'true';

      if (isPrivate) {
          // if private, show "Request to Follow"
          followButton.textContent = 'Request to Follow';
          followButton.classList.remove('bg-red-500', 'hover:bg-red-700');
          followButton.classList.add('bg-sky-700', 'hover:bg-sky-900');
          followButton.setAttribute('data-follow-status', 'not-following');
      } else {
          // if public, show "Follow"
          followButton.textContent = 'Follow';
          followButton.classList.remove('bg-red-500', 'hover:bg-red-700');
          followButton.classList.add('bg-sky-700', 'hover:bg-sky-900');
          followButton.setAttribute('data-follow-status', 'not-following');
      }
  }
  })
  .catch(error => {
      console.error('Something went wrong with the unfollow request.', error);
  });
}

function cancelPendingRequest(userId, csrfToken, followButton) {
  console.log("cancelPendingRequest");

  fetch('/profile/' + userId + '/unfollow', {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify({ user_id: userId })
  })
  .then(response => response.json())
  .then(data => {
      if (data.success) {
          const isPrivate = followButton.getAttribute('data-is-private') === 'true';
          
          if (isPrivate) {
              console.log("11");
              followButton.textContent = 'Request to Follow';  
              followButton.classList.remove('bg-yellow-500', 'hover:bg-yellow-700');
              followButton.classList.add('bg-sky-700', 'hover:bg-sky-900');
              followButton.setAttribute('data-follow-status', 'not-following');
          } else {
              console.log("10");
              followButton.textContent = 'Follow';  
              followButton.classList.remove('bg-yellow-500', 'hover:bg-yellow-700');
              followButton.classList.add('bg-sky-700', 'hover:bg-sky-900');
              followButton.setAttribute('data-follow-status', 'not-following');
          }
      }
  })
  .catch(error => console.error('Error:', error));
}



//Functions related to the profile pictures (profile picture and banner) -----------------------------------------------------------------------


//Funtion that displays a preview of the images that are going to be uploaded
function imageCropper(){
  document.getElementById('profilePic').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const imageUrl = e.target.result;
            const img = document.getElementById('image');
            img.src = imageUrl;
            const croppModal = document.getElementById("croppModal");
            croppModal.classList.toggle('hidden');
            croppModal.classList.toggle('flex');
        };
        reader.readAsDataURL(file);
        
    }
  });
  document.getElementById('bannerPic').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const croppModal = document.getElementById("croppModal");
        croppModal.classList.toggle('hidden');
        croppModal.classList.toggle('flex');
        insertLoadingCircle(croppModal);
        const reader = new FileReader();
        reader.onload = function(e) {
          removeLoadingCircle();
            const imageUrl = e.target.result;
            const img = document.getElementById('image');
            img.src = imageUrl;
            
        };
        reader.readAsDataURL(file);
        
    }
  });
}

//Closes the preview menu
function closeImagePreview(){
  const croppModal = document.getElementById("croppModal");
  croppModal.classList.toggle('hidden');
  croppModal.classList.toggle('flex');
}


//update the file that appears in the edit profile when the user uploads a file
function updateFileProfile(isbanner) {
  let fileInput = null;
  let fileDisplay = null
  if(isbanner){
    fileInput = document.getElementById('bannerPic');
    fileDisplay = document.getElementById('bannerPicDisplay');
  }
  else{
    fileInput = document.getElementById('profilePic');
    fileDisplay = document.getElementById('profilePicDisplay');
  }


  // Clear previous file list
  fileDisplay.innerHTML = '';

  let file = fileInput.files[0];
  const li = document.createElement('li');
  li.classList.add('flex', 'items-center', 'gap-2');

  li.innerHTML = `
      <span class="text-sm text-gray-500">${file.name}</span>
      <button type="button" onclick="removeFileProfile(${isbanner})" class="text-sm text-red-500 hover:text-red-700">Remove</button>
  `;
  fileDisplay.appendChild(li);


  fileDisplay.classList.remove('hidden');
}

//removes the file that is being displayed
function removeFileProfile(isbanner){
  let fileInput = null;
  let fileDisplay = null
  if(isbanner){
    fileInput = document.getElementById('bannerPic');
    fileDisplay = document.getElementById('bannerPicDisplay');
  }
  else{
    fileInput = document.getElementById('profilePic');
    fileDisplay = document.getElementById('profilePicDisplay');
  }

  fileDisplay.innerHTML = '';
  fileInput.value = '';

}

addEventListeners();
  