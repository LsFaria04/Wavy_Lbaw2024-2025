function addEventListeners() {
  document.addEventListener('DOMContentLoaded', fadeAlert);
  document.addEventListener('DOMContentLoaded', switchGroupTab);

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
  addEventListenerToPostForms();
  syncPostFilesWithInputEventListener();
  syncPostTopicsWithInputEventListener();

  addEventListenerEditUserAdmin();
  eventListernerFormsAdmin();

  likePost();
  
}



//stores the authentication state
let isAuthenticated = false; 
let userId = -1;
let isadmin = false;
let currentUsername = "";
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
  currentUsername = response.username;
 
}

//Create Post Helper

let selectedFiles = [];

function updateFileList() {
  const fileInput = document.getElementById('image');
  const fileDisplay = document.getElementById('fileDisplay');

  // Append new files to the list (preserve existing files)
  Array.from(fileInput.files).forEach(file => {
    if (file.size > 1048576){
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
addEventListeners();
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


function syncPostFilesWithInputEventListener(){
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


//closes the users posts delete menu
function closeDeleteMenu() {
  const modal = document.getElementById('deleteMenu');
  modal.classList.add('hidden');
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
            <textarea name="message" rows="2" class="mt-1 block w-full p-4 border rounded-xl focus:ring-2 focus:ring-sky-700 shadow-sm outline-none" placeholder="Edit your message">${ message}</textarea>
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

//inserts the topics into the posts
function insertPostTopics(post, topics){
  const postheader = post.querySelector('.post-header');
  let postTopics = document.createElement('div');
  postTopics.setAttribute('id', 'postTopics');
  postTopics.classList.add('flex', 'flex-row');

  for(let i = 0; i < topics.length; i++){
    let topic = document.createElement('p');
    topic.classList.add("text-xs");
    topic.innerHTML = `
      ${topics[i].topicname}
    `
    postTopics.appendChild(topic);
  }
  post.insertBefore(postTopics, postheader.nextSibling);

  return post
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


function likePost(postId) {
  
  const likeCountElement = document.getElementById(`like-count-${postId}`);
  
  const heartEmpty = document.getElementById(`heart-empty-${postId}`);
  const heartFilled = document.getElementById(`heart-filled-${postId}`);

  if (heartFilled?.classList.contains('hidden')) {
      heartEmpty.classList.add('hidden');
      heartFilled.classList.remove('hidden');
      likeCountElement.textContent = parseInt(likeCountElement.textContent) + 1;
  } else {
      heartEmpty?.classList.remove('hidden');
      heartFilled?.classList.add('hidden');
      if(likeCountElement !== null){
        likeCountElement.textContent = parseInt(likeCountElement.textContent) - 1;
      }
  }
}

function toggleAddPostTopics(){
  if(document.getElementById("addTopics").classList.contains('hidden')){
    postTopicPage = 0;
    loadMorePostTopics();
  }
  else{
    //remove the topics when we hide the the menu
    let topics = document.querySelectorAll('#postTopicsList .topicList li,#postTopicsList .topicList p ');
    topics.forEach(function (e) {e.remove()});
  }
  document.getElementById("addTopics").classList.toggle('hidden');
  document.getElementById("addTopics").classList.toggle('flex');
  
}

//loads more post topics from the database and calls the insert more topics
let postTopicPage = 0;
let postTopicPageMax = -1;
let searchQueryPost = "";
let isQueryPost = false;
function loadMorePostTopics(){

  let topicsList = null;

  topicsList = document.querySelector("#postTopicsList > ul");
  

  insertLoadingCircle(topicsList);

  
  if(isQueryPost){
 
  }
  else{
    postTopicPage++;
    sendAjaxRequest('get', '/api/topics/all?page=' + postTopicPage,null,insertMorePostTopics);
  }


}

let selectedTopics = [];
function insertMorePostTopics(){
  removeLoadingCircle();
  let topics = JSON.parse(this.responseText);

  let topicsList = document.querySelector("#postTopicsList > ul");

  if(topics.response !== undefined){
    alert(topics.message);
    return;
  }

  //iterate throw the topics and add them into the list
  for(let i = 0; i < topics.data.length; i++){
    //only create and a dd a topic if it isn't already selected
    if(!selectedTopics.includes(topics.data[i].topicid)){
      let topic = createTopic(topics.data[i], false, true);
      topicsList.appendChild(topic);
    }
  }
}

function addTopicToPost(topicid, topicname){

  //maximum number of topics per post reached
  if(selectedTopics.length == 5){
    alert("Can only select up to 5 topics per post!");
    return;
  }
  if(!selectedTopics.includes(topicid)){
    selectedTopics.push(topicid);
  }

  //remove topic from the menu
  const topic = document.getElementById(`topic-${topicid}`);
  topic.remove();

  //add the topic to the topic display
  let topicDisplay = document.getElementById('topicDisplay');

  const li = document.createElement('li');
  li.classList.add('flex', 'items-center', 'gap-2');
  li.setAttribute('id', `postTopic-${topicid}`);

  li.innerHTML = `
      <span class="text-sm text-gray-500">${topicname}</span>
      <button type="button" onclick="removeSpecificTopic(${topicid})" class="text-sm text-red-500 hover:text-red-700">Remove</button>
  `;

  topicDisplay.appendChild(li);

  if(topicDisplay.classList.contains('hidden')){
    topicDisplay.classList.toggle('hidden');
  }

  
}

function removeSpecificTopic(topicid){
  //remove from the display topics so that the user knows that the topic was removed
  let topic = document.getElementById(`postTopic-${topicid}`);
  topic.remove();

  //remove from the selected array so that the the user can select it again
  const index = selectedTopics.findIndex(id => id === topicid);
  const deleted = selectedTopics.splice(index, 1);
  console.log(deleted);

}

function syncPostTopicsWithInputEventListener(){
  document.querySelector('.addPost form').addEventListener('submit', function (e) {
    //update the values before sending the form
    let topicInput = document.getElementById('topicInput');
    topicInput.value = selectedTopics;
    alert(topicInput.value);
  });
}

addEventListeners();