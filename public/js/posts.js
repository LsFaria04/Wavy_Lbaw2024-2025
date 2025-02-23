function addEventListeners() {
  //listeners related to the posts
  addEventListenerToPostForms();
  syncPostFilesWithInputEventListener();
  syncPostTopicsWithInputEventListener();
  deleteMenuButtons();
}

function deleteMenuButtons() {
  document.addEventListener("click", (event) => {
    const target = event.target;

    // Handle Cancel button
    if (target.matches(".cancelButton")) {
      const deleteMenu = target.closest(".fixed");
      if (deleteMenu) {
        deleteMenu.classList.add("hidden");
        deleteMenu.classList.remove("flex");
        document.documentElement.classList.remove("overflow-hidden");
      }
    }

    // Handle Confirm button
    if (target.matches(".confirmButton")) {
      const deleteMenu = target.closest(".fixed");
      const postId = deleteMenu?.id.split("-")[1]; 
      if (postId) {
        const deleteForm = document.getElementById(`deleteForm-${postId}`);
        if (deleteForm) {
          target.disabled = true;
          deleteForm.submit(); 
        }
      }
    }
  });
}

function closeDeleteMenu(postid) {
  const modal = document.getElementById(`deleteMenu-${postid}`);
  if (modal) {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
      document.documentElement.classList.remove('overflow-hidden');
  }
}

function openDeleteMenu(postid) {
  insertDeleteMenu(postid);
  const deleteMenu = document.getElementById(`deleteMenu-${postid}`);
  if (!deleteMenu) {
      console.error(`Delete menu for post ${postid} not found.`);
      return;
  }
  deleteMenu.classList.remove('hidden');
  deleteMenu.classList.add('flex');
  document.documentElement.classList.add('overflow-hidden');
}

function insertDeleteMenu(postid) {
  if (document.getElementById(`deleteMenu-${postid}`)) {
      return;
  }

  let menu = document.createElement('div');
  menu.setAttribute('id', `deleteMenu-${postid}`);
  menu.classList.add("fixed", "inset-0", "bg-black", "bg-opacity-50", "hidden", "flex", "items-center", "justify-center", "z-20");

  menu.innerHTML = `
      <div class="bg-white rounded-xl shadow-lg p-6 max-w-sm w-full">
          <h2 class="text-xl font-semibold text-gray-900">Delete Post</h2>
          <p class="mt-4 text-sm text-gray-600">Are you sure you want to delete this post? This action cannot be undone.</p>
          <div class="mt-6 flex justify-end gap-3">
              <button class="cancelButton px-4 py-2 text-white bg-gray-400 hover:bg-gray-600 rounded-2xl focus:outline-none">
                  Cancel
              </button>
              <button class="confirmButton px-4 py-2 text-white bg-red-600 hover:bg-red-700 rounded-2xl focus:outline-none">
                  Delete
              </button>
          </div>
      </div>
  `;

  document.body.appendChild(menu);
}

function createPost(postInfo) {
  let likeCount = postInfo.likes_count ?? 0; 
  let commentCount = postInfo.comments_count ?? 0;
  let post = document.createElement('div');
  post.classList.add("post", "border-b", "border-gray-300", "p-4", "bg-white", "cursor-pointer");
  
  post.setAttribute(
      "onclick",
      `window.location.href='/posts/${postInfo.postid}'`
  );

  let user = postInfo.user;

  const profilePicture = user.profile_picture.length > 0
      ? user.profile_picture[0].path.includes('profile')
          ? '/storage/' + user.profile_picture[0].path
          : user.profile_picture.length > 1
          ? '/storage/' + user.profile_picture[1].path
          : ''
      : null;

  post.innerHTML = `
      <div class="post-header mb-1 flex justify-between items-center">
          <div>
              <a href="${postInfo.user.state === 'deleted' ? '#' : '/profile/' + postInfo.user.username}">
                  <div class="flex flex-row gap-2">
                      <div class="h-8 w-8 rounded-full overflow-hidden bg-gray-300">
                          ${profilePicture 
                              ? `<img class="h-full w-full object-cover rounded-md mb-2 mx-auto" 
                                       src="${profilePicture}" 
                                       alt="ProfilePicture">`
                              : ''}
                      </div>
                      <h3 class="font-bold text-black hover:text-sky-900">
                          ${postInfo.user.state === 'deleted' ? 'Deleted User' : postInfo.user.username}
                      </h3>
                  </div>
              </a>
              <span class="text-gray-500 text-sm">${postInfo.createddate}</span>
          </div>
      </div>
      <div class="post-body mb-2 max-w-screen-lg" id="post-content-${postInfo.postid}">
          <p>${postInfo.message}</p>
      </div>
      <div class="post-interactions flex items-center gap-4 mt-4">
          ${createLikeButton(postInfo.postid, likeCount, postInfo.liked)}
          ${createCommentButton(postInfo.postid, commentCount)}
      </div>
  `;

  return post;
}

function createLikeButton(postId, likeCount, likedByUser) {
  return `
      <div class="post-likes flex items-center gap-2">
          <button 
              type="button" 
              class="flex items-center text-gray-500 hover:text-red-600 group" 
              onclick="likePost(${postId}, event); event.stopPropagation();">
              
              <!-- No like -->
              <svg 
                  id="heart-empty-${postId}" viewBox="0 0 24 24" aria-hidden="true"
                  class="h-5 w-5 ${likedByUser ? 'hidden' : 'fill-gray-500 hover:fill-red-600 group-hover:fill-red-600'}">
                  <g>
                      <path d="M16.697 5.5c-1.222-.06-2.679.51-3.89 2.16l-.805 1.09-.806-1.09C9.984 6.01 8.526 5.44 7.304 5.5c-1.243.07-2.349.78-2.91 1.91-.552 1.12-.633 2.78.479 4.82 1.074 1.97 3.257 4.27 7.129 6.61 3.87-2.34 6.052-4.64 7.126-6.61 1.111-2.04 1.03-3.7.477-4.82-.561-1.13-1.666-1.84-2.908-1.91zm4.187 7.69c-1.351 2.48-4.001 5.12-8.379 7.67l-.503.3-.504-.3c-4.379-2.55-7.029-5.19-8.382-7.67-1.36-2.5-1.41-4.86-.514-6.67.887-1.79 2.647-2.91 4.601-3.01 1.651-.09 3.368.56 4.798 2.01 1.429-1.45 3.146-2.1 4.796-2.01 1.954.1 3.714 1.22 4.601 3.01.896 1.81.846 4.17-.514 6.67z"></path>
                  </g>
              </svg>
              
              <!-- Yes like -->
              <svg 
                  id="heart-filled-${postId}" viewBox="0 0 24 24" aria-hidden="true"
                  class="h-5 w-5 ${likedByUser ? 'fill-red-600 group-hover:fill-red-600' : 'hidden'}">
                  <g>
                      <path d="M20.884 13.19c-1.351 2.48-4.001 5.12-8.379 7.67l-.503.3-.504-.3c-4.379-2.55-7.029-5.19-8.382-7.67-1.36-2.5-1.41-4.86-.514-6.67.887-1.79 2.647-2.91 4.601-3.01 1.651-.09 3.368.56 4.798 2.01 1.429-1.45 3.146-2.1 4.796-2.01 1.954.1 3.714 1.22 4.601 3.01.896 1.81.846 4.17-.514 6.67z"></path>
                  </g>
              </svg>
              
              <span id="like-count-${postId}" class="ml-1 group-hover:text-red-600">${likeCount}</span>
          </button>
      </div>
  `;
}

function likePost(postId,event) {

  event?.stopPropagation();

  if(isadmin) return;
  if (userId == -1) return; 
  if (postId == null) return;

  const likeCountElement = document.getElementById(`like-count-${postId}`);
  const heartEmpty = document.getElementById(`heart-empty-${postId}`);
  const heartFilled = document.getElementById(`heart-filled-${postId}`);

  if(heartEmpty.classList.contains('hidden')) {
    heartEmpty?.classList.remove('hidden');
    heartFilled?.classList.add('hidden');
    heartEmpty.classList.add('fill-gray-500', 'group-hover:fill-red-600');
    if (likeCountElement !== null) {
      likeCountElement.textContent = parseInt(likeCountElement.textContent) - 1;
    }
    likeCountElement.classList.remove('text-red-600');
  }
  else {
    heartEmpty.classList.add('hidden');
        heartFilled.classList.remove('hidden');
        heartFilled.classList.add('fill-red-600', 'group-hover:fill-red-600');
        likeCountElement.textContent = parseInt(likeCountElement.textContent) + 1;
        likeCountElement.classList.add('text-red-600');

  }


  // Make the AJAX request to like/unlike the post
  sendAjaxRequest('post', '/like-post/' + postId, null,  null);
}

function createCommentButton(postId, commentCount = 0) {
  return `
      <div class="post-comments flex items-center gap-2">
            <button 
                type="button" 
                class="flex items-center text-gray-500 hover:text-sky-600 group" 
                onclick="commentPost(${postId}, event); event.stopPropagation();">
                <svg 
                    xmlns="http://www.w3.org/2000/svg" 
                    id="comment-icon-${postId}" 
                    class="h-5 w-5 fill-gray-500 group-hover:fill-sky-600" 
                    viewBox="0 0 24 24" 
                    fill="currentColor">
                    <path d="M12 2C6.477 2 2 6.067 2 10.5c0 1.875.656 3.625 1.844 5.094l-1.308 3.922c-.19.57.474 1.065.997.736l3.875-2.325A9.435 9.435 0 0012 19c5.523 0 10-4.067 10-8.5S17.523 2 12 2zm0 2c4.418 0 8 3.067 8 6.5S16.418 17 12 17c-1.173 0-2.292-.232-3.318-.656a1 1 0 00-.97.035l-2.898 1.739.835-2.501a1 1 0 00-.176-.964A7.36 7.36 0 014 10.5C4 7.067 7.582 4 12 4z" />
                </svg>
                <!-- Comment Count -->  
                <span id="comment-count-${postId}" class="ml-1 text-gray-500 group-hover:text-sky-600">${commentCount}</span>
            </button>
      </div>
  `;
}

function createPostOptions(post, id, needReport) {
  const postheader = post.querySelector('.post-header');
  let options = document.createElement('div');
  options.classList.add("flex", "items-center", "gap-2");
  options.setAttribute('id', 'postOptions-' + id);
  
  if (!needReport) {
    options.innerHTML = `
      <button type="button" onclick="toggleEditPost(${id}); event.stopPropagation();" class="text-gray-500 hover:text-black">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="black" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="0.6" d="M10.973 1.506a18.525 18.525 0 00-.497-.006A4.024 4.024 0 006.45 5.524c0 .43.095.865.199 1.205.054.18.116.356.192.527v.002a.75.75 0 01-.15.848l-4.937 4.911a.871.871 0 000 1.229.869.869 0 001.227 0L7.896 9.31a.75.75 0 01.847-.151c.17.079.35.139.529.193.34.103.774.198 1.204.198A4.024 4.024 0 0014.5 5.524c0-.177-.002-.338-.006-.483-.208.25-.438.517-.675.774-.32.345-.677.696-1.048.964-.354.257-.82.512-1.339.512-.396 0-.776-.156-1.059-.433L9.142 5.627a1.513 1.513 0 01-.432-1.06c0-.52.256-.985.514-1.34.27-.37.623-.727.97-1.046.258-.237.529-.466.78-.675zm-2.36 9.209l-4.57 4.59a2.37 2.37 0 01-3.35-3.348l.002-.001 4.591-4.568a6.887 6.887 0 01-.072-.223 5.77 5.77 0 01-.263-1.64A5.524 5.524 0 0110.476 0 12 12 0 0112 .076c.331.044.64.115.873.264a.92.92 0 01.374.45.843.843 0 01-.013.625.922.922 0 01-.241.332c-.26.257-.547.487-.829.72-.315.26-.647.535-.957.82a5.947 5.947 0 00-.771.824c-.197.27-.227.415-.227.457 0 .003 0 .006.003.008l1.211 1.211a.013.013 0 00.008.004c.043 0 .19-.032.46-.227.253-.183.532-.45.826-.767.284-.308.56-.638.82-.95.233-.28.463-.565.72-.823a.925.925 0 01.31-.235.841.841 0 01.628-.033.911.911 0 01.467.376c.15.233.22.543.262.87.047.356.075.847.075 1.522a5.524 5.524 0 01-5.524 5.525c-.631 0-1.221-.136-1.64-.263a6.969 6.969 0 01-.222-.071z"/>
          </svg>
      </button>
      <form action="../posts/delete/${id}" method="POST" id="deleteForm-${id}">
        <button type="button" onclick="openDeleteMenu(${id}); event.stopPropagation();" class="text-red-500 hover:text-red-700 ml-2">
            <input type="hidden" name="_token" value=${getCsrfToken()} />
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
      </form>
    `;
  } else {
    options.innerHTML = `
      <button type="button" onclick="event.stopPropagation(); toggleReportForm('${id}', 'post');" class="text-gray-500 hover:text-black">
          Report
      </button>
    `;
  }
  postheader.appendChild(options);
  return post;
}

//stores the authentication state
let isAuthenticated = false; 
let userId = -1;
let isadmin = false;
let currentUsername = "";
sendAjaxRequest('post', '/api/auth-check', null, authInfo);
function authInfo() {
  const response = JSON.parse(this.responseText);
  isAuthenticated = response.authenticated;
  if(isAuthenticated) {
    sendAjaxRequest('post', '/api/auth-id', null, authId);
  }
}

function authId() {
  const response = JSON.parse(this.responseText);
  userId = response.id;
  isadmin = response.isadmin;
  currentUsername = response.username;
}

let selectedFiles = [];

function updateFileList() {
  const fileInput = document.getElementById('image');
  const fileDisplay = document.getElementById('fileDisplay');

  // Append new files to the list (preserve existing files)
  Array.from(fileInput.files).forEach(file => {
    if (file.size > 1048576) {
      const messageContainer = document.getElementById('messageContainer');
      createAlert(messageContainer, "File is too big (>1Mb)", true);
    }
    else {
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
        <button type="button" onclick="removeSpecificFile(${index})" class="text-sm text-red-500 hover:text-red-700">Remove</button>
    `;
    fileDisplay.appendChild(li);
  });

  fileDisplay.classList.remove('hidden');

  // Reset the file input to allow adding more files
  fileInput.value = '';
}
// Object to store the original values and files for each post
const originalFormData = {};

// Toggle the edit form visibility
function toggleEditPost(postid) {
    event.stopPropagation();
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

let selectedFilesEdit = [];  // New files selected by user (not yet submitted)

// Edit Post Helpers
function updateFileNameEdit(postId) {
  const fileInput = document.getElementById(`image-${postId}`);
  const fileDisplay = document.getElementById(`fileDisplay-${postId}`);
  const newFileDisplay = document.getElementById(`newFiles-${postId}`)

  Array.from(fileInput.files).forEach(file => {
    if (file.size > 1048576) {
      const messageContainer = document.getElementById('messageContainer');
      createAlert(messageContainer, "File is too big (>1Mb)", true);
    }
    else {
      selectedFilesEdit.push(file);
    }
  });

  
  const lenStoreMedia = fileDisplay.querySelectorAll('div').length - 1;
  // Check if there are more than 4 files
  if (lenStoreMedia + selectedFilesEdit.length > 4) {
    const messageContainer = document.getElementById('messageContainer');
      createAlert(messageContainer, "You can only select up to 4 files", true);
      // Remove the newly added files from the selectedFiles array
      selectedFilesEdit.splice(-fileInput.files.length);
      return; 
  }

  // Clear previous file list
  newFileDisplay.innerHTML = '';

  // Show updated list of file names
  selectedFilesEdit.forEach((file, index) => {
    if(newFileDisplay.classList.contains('hidden')) {
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


function syncPostFilesWithInputEventListener() {
  // Synchronize selectedFiles with the file input before form submission
  document.querySelector('.addPost form')?.addEventListener('submit', function (e) {
    
    if (selectedFiles.length > 4) {
      e.preventDefault(); // Prevent the form from submitting
      const messageContainer = document.getElementById('messageContainer');
      createAlert(messageContainer, "You can only submit up to 4 files", true);
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
function addEventListenerToPostForms() {

  if(document.querySelectorAll('.edit-post-form form').length === 0) {
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

//adds a event listener to a post form
function addEventListenerToForm(form) {
  form.addEventListener('submit', function (e) {
    const postId = form.dataset.postId;
    const fileInput = document.getElementById(`image-${postId}`);
    
    // Check if there are more than 4 files, prevent submission
    if (selectedFilesEdit.length > 4) {
        e.preventDefault();
        const messageContainer = document.getElementById('messageContainer');
        createAlert(messageContainer, "You can only submit up to 4 files", true);
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

    //adjust the arrays that will be sent with the topics to be added and deleted
    let removeTopics = document.getElementById(`removeTopics-${postId}`);
    let topicInput = document.getElementById(`topicInput-${postId}`);
    topicInput.value = selectedTopics;
    removeTopics.value = topicsToDelete;
    let notRemovedLen = document.querySelectorAll(`topicDisplay-${postId} > div`).length - 1;

    
    if(selectedTopics + notRemovedLen - topicsToDelete > 5) {
      e.preventDefault();
      const messageContainer = document.getElementById('messageContainer');
      createAlert(messageContainer, "You can only submit up to 5 topics", true);
      return;
    }
  });
}

//inserts the media (images, audio and video) of a post into a post container. Returns the updated post container
function insertPostMedia(post, mediaArray) {
  const postbody = post.querySelector('.post-body');
  let mediaContainer = document.createElement('div');
  mediaContainer.setAttribute('onclick','event.stopPropagation();');
  mediaContainer.setAttribute("class", "post-media mt-4 flex flex-row flex-wrap gap-2 sm:justify-start items-center justify-center");
  
  for(let i = 0; i < mediaArray.length; i++) {
    let media = mediaArray[i];
    let fileExtension = media.path.split('.').pop();
  
    let newMedia = document.createElement('img');
  
      
      if(['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
        const imageDetailButton = document.createElement('button');
        imageDetailButton.setAttribute('onclick', `toggleImageDetails('${'/storage/' + media.path}')`);
        imageDetailButton.setAttribute("class", "h-60 w-60 sm:w-80 sm:h-80 overflow-hidden  rounded-md mb-2");
        imageDetailButton.innerHTML = `
          <img src="${'/storage/' + media.path}" alt="Image" class="max-w-full max-h-96  object-cover rounded-md mb-2 mx-auto ">
        `;
        mediaContainer.appendChild(imageDetailButton);
        continue;
      }
  
      else if(['mp4', 'avi', 'mov'].includes(fileExtension)) {
        newMedia = document.createElement('video');
        newMedia.setAttribute("controls", "");
        let source = document.createElement('source');
    
        source.setAttribute('src', "/storage/" + media.path);
        source.setAttribute('type', "video/" + fileExtension);
  
      newMedia.appendChild(source);
      newMedia.classList.add("max-w-full", "max-h-96", "object-cover", "rounded-md", "mb-2", "mx-auto");
    }
  
      else if(['mp3', 'wav', 'ogg'].includes(fileExtension)) {
        newMedia = document.createElement('audio');
        let source = document.createElement('source');
      
        source.setAttribute('src', "/storage/" + media.path);
        source.setAttribute('type', "audio/" + fileExtension);
  
      newMedia.appendChild(source);
      newMedia.classList.add("w-full","mb-2");
    }
  
    else {
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
function insertUpdateForm(post, id, message, media, topics) {
  event.stopPropagation();
  let formContainer = document.createElement('div');
  formContainer.classList.add("edit-post-form", "hidden", "mt-4", "bg-white", "rounded-xl", "shadow-md", "p-4");
  formContainer.setAttribute('id', "edit-post-" + id);

  formContainer.innerHTML = `
    <form action="/posts/update/${id}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4" data-post-id="${id}" onclick="event.stopPropagation();">
        <input type="hidden" name="_token" value=${getCsrfToken()} />
        <div class="mb-4">
            <label for="message" class="block text-sm font-medium text-gray-700">Edit Message</label>
            <textarea name="message" rows="2" class="mt-1 block w-full p-4 border rounded-xl focus:ring-2 focus:ring-sky-700 shadow-sm outline-none" placeholder="Edit your message">${message}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Edit Media</label>
            <label for="image-${id}" class="cursor-pointer flex items-center gap-2 text-gray-500 hover:text-black mt-2">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 sm:w-7 sm:h-7">
                    <path d="M19.8278 11.2437L12.7074 18.3641C10.7548 20.3167 7.58896 20.3167 5.63634 18.3641C3.68372 16.4114 3.68372 13.2456 5.63634 11.293L12.4717 4.45763C13.7735 3.15589 15.884 3.15589 17.1858 4.45763C18.4875 5.75938 18.4875 7.86993 17.1858 9.17168L10.3614 15.9961C9.71048 16.647 8.6552 16.647 8.00433 15.9961C7.35345 15.3452 7.35345 14.2899 8.00433 13.6391L14.2258 7.41762" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
                <span class = "w-20 sm:w-full text-sm sm:text-base" >Attach new file</span>
            </label>
            <div id="fileDisplay-${id}" class="flex-col items-center gap-2 text-gray-500 hover:text-black mt-2 ${media.length === 0 ? 'hidden' : ''}"></div>
            <input type="file" name="media[]" id="image-${id}" class="hidden" onchange="updateFileNameEdit('${id}')" multiple>
            <input type="hidden" name="remove_media" id="removeMedia-${id}" value="[]">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Edit Topics</label>
            <label for="topic-${id}" class="cursor-pointer flex items-center gap-2 text-gray-500 hover:text-black mt-2">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 sm:w-7 sm:h-7">
                    <path d="M19.8278 11.2437L12.7074 18.3641C10.7548 20.3167 7.58896 20.3167 5.63634 18.3641C3.68372 16.4114 3.68372 13.2456 5.63634 11.293L12.4717 4.45763C13.7735 3.15589 15.884 3.15589 17.1858 4.45763C18.4875 5.75938 18.4875 7.86993 17.1858 9.17168L10.3614 15.9961C9.71048 16.647 8.6552 16.647 8.00433 15.9961C7.35345 15.3452 7.35345 14.2899 8.00433 13.6391L14.2258 7.41762" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
                <span class = "w-20 sm:w-full text-sm sm:text-base" >Add new topic</span>
            </label>
            <div id="topicDisplay-${id}" class="flex-col items-center gap-2 text-gray-500 hover:text-black mt-2 ${topics.length === 0 ? 'hidden' : ''}">
            </div>
            <button type="button" class="hidden" id="topic-{{ $index }}" onclick="toggleAddPostTopics(${id}, true)"></button>
            <input type="hidden" id="topicInput-{{ $index }}" class="topicInput" name="topics[]" value="[]">
            <input type="hidden" name="remove_topics[]" id="removeTopics-${id}" class="topicRemove" value="[]">
        </div>

        <button type="submit" class="px-4 py-2 w-20 bg-sky-700 text-white font-semibold rounded-3xl hover:bg-sky-800">Update</button>
    </form>
  `;

  // Add existing media items
  const fileDisplay = formContainer.querySelector(`#fileDisplay-${id}`);
  media.forEach(mediaItem => {
    let mediaRemove = document.createElement('div');
    mediaRemove.classList.add("flex", "items-center", "gap-2");
    mediaRemove.setAttribute('id', `file-${mediaItem.mediaid}`);
    mediaRemove.innerHTML = `
      <span class="text-sm text-gray-500">${mediaItem.path.split('/')[1]}</span>
      <button type="button" onclick="removeFileEdit('${id}', '${mediaItem.mediaid}')" class="text-sm text-red-500 hover:text-red-700">Remove</button>
    `;
    fileDisplay.appendChild(mediaRemove);
  });

  // Add existing topics
  const topicDisplay = formContainer.querySelector(`#topicDisplay-${id}`);
  topics.forEach(topic => {
    let topicDiv = document.createElement('div');
    topicDiv.classList.add("flex", "items-center", "gap-2");
    topicDiv.setAttribute('id', `post-${id}Topic-${topic.topicid}`);
    topicDiv.innerHTML = `
      <span class="text-sm text-gray-500">${topic.topicname}</span>
      <button type="button" onclick="addToDeleteTopic(${topic.topicid}, ${id})" class="text-sm text-red-500 hover:text-red-700">Remove</button>
    `;
    topicDisplay.appendChild(topicDiv);
  });

  post.appendChild(formContainer);
  return post;
}

//inserts the topics into the posts
function insertPostTopics(post, topics) {
  const postheader = post.querySelector('.post-header');
  let postTopics = document.createElement('div');
  postTopics.setAttribute('id', 'postTopics');
  postTopics.setAttribute('class', "flex flex-row gap-2");

  for(let i = 0; i < topics.length; i++) {
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
          <span class="text-sm text-gray-500 sm:w-12 text-ellipsis overflow-hidden ...">${file.name}</span>
          <button type="button" onclick="removeSpecificFile(${i})" class="text-sm text-red-500 hover:text-red-700">Remove</button>
      `;
      fileDisplay.appendChild(li);
  });

  // Hide the display if no files remain
  if (selectedFiles.length === 0) {
      fileDisplay.classList.add('hidden');
  }
}

function toggleAddPostTopics(postid, isedit) {
  if(document.getElementById("addPostTopics").classList.contains('hidden')) {
    postTopicPage = 0;
    topicPostId = postid;
    isEditPost = isedit;
    loadMorePostTopics();   
  }
  else {
    //remove the topics when we hide the the menu
    let topics = document.querySelectorAll('#postTopicsList .topicList li,#postTopicsList .topicList p ');
    topics.forEach(function (e) {e.remove()});
  }
  document.getElementById("addPostTopics").classList.toggle('hidden');
  document.getElementById("addPostTopics").classList.toggle('flex');
  
}

function searchPostTopics(e) {
  e.preventDefault();
  postTopicPage = 0;
  isQuery = true;
  searchQuery = document.querySelector('#topicsPostSearch').value;

   //cancel the search if there is not a query
   if(searchQuery == "") {
    isQuery = false;
  }

  //remove the existing topics from the list that is being displayed to the user 
  let topics = document.querySelectorAll("#postTopicsList > ul li, #postTopicsList > ul p");
  topics.forEach( function (topic) {
    topic.remove();
  })

  loadMorePostTopics();
}

//loads more post topics from the database and calls the insert more topics
let postTopicPage = 0;
let postTopicPageMax = -1;
let isEditPost = false;
function loadMorePostTopics() {

  let topicsList = null;

  topicsList = document.querySelector("#postTopicsList > ul");
  

  insertLoadingCircle(topicsList);

  
  if(isQuery) {
    postTopicPage++;
    sendAjaxRequest('get', '/api/topics/search/all/'+ topicPostId + '?q=' + searchQuery + '&page=' + postTopicPage,null,insertMorePostTopics);
  }
  else {
    postTopicPage++;
    sendAjaxRequest('get', '/api/topics/all/' + topicPostId + '?page=' + postTopicPage,null,insertMorePostTopics);
  }


}

let selectedTopics = [];
let topicPostId = -1;
function insertMorePostTopics() {
  removeLoadingCircle();
  let topics = JSON.parse(this.responseText);

  let topicsList = document.querySelector("#postTopicsList > ul");

  postTopicPageMax = topics.last_page;

  if(topics.response !== undefined) {
    const messageContainer = document.getElementById('messageContainer');
    createAlert(messageContainer, topics.message, true);
    return;
  }

  if(postTopicPageMax === postTopicPage) {
    //hide the button if there is the last page is being displayed
    if(!document.querySelector('#postTopicsList > button').classList.contains('hidden')) {
      document.querySelector('#postTopicsList > button').classList.toggle('hidden');
    }
  }

  //iterate throw the topics and add them into the list
  for(let i = 0; i < topics.data.length; i++) {
    //only create and add a topic if it isn't already selected
    if(selectedTopics.includes(topics.data[i].topicid)) {
      continue;
    }
    //do not show the general topic because it is the default
    if(topics.data[i].topicid === 1) {
      continue;
    }

    let topic = createTopic(topics.data[i], false, true, topicPostId);
    topicsList.appendChild(topic);
  }

  if(topics.data.length > 0) {
    if(postTopicPageMax > postTopicPage) {
      //Show the button if there is more data to display
      if(document.querySelector('#postTopicsList > button').classList.contains('hidden')) {
        document.querySelector('#postTopicsList > button').classList.toggle('hidden');
      }
    }
  }
  else {
    //there are no topics in the list and we could not found new ones with the ajax request so a warning is displayed
    if(topicsList.querySelector('p') == null && topicsList.querySelector('li') == null) {
      let warning = document.createElement('p');
      warning.innerHTML='No topics found';
      topicsList.appendChild(warning);
    }

    //hide the button when there is no more content to display
    if(!document.querySelector('#postTopicsList > button').classList.contains('hidden')) {
      document.querySelector('#postTopicsList > button').classList.toggle('hidden');
    }
    
  }
}

function addTopicToPost(topicid, topicname, postid) {
  const messageContainer = document.getElementById('messageContainer');

  //maximum number of topics per post reached
  if(selectedTopics.length == 5) {
    createAlert(messageContainer, "Can only select up to 5 topics per post!", true);
    return;
  }
  if(!selectedTopics.includes(topicid)) {
    selectedTopics.push(topicid);
  }

  //remove topic from the menu
  const topic = document.getElementById(`topic-${topicid}`);
  topic.remove();

  //Alert user that the topic was selected
  createAlert(messageContainer, `Topic ${topicname} was added`, false);

  //add the topic to the topic display
  let topicDisplay = document.getElementById(`topicDisplay-${postid}`);

  const div = document.createElement('div');
  div.classList.add('flex', 'items-center', 'gap-2');
  div.setAttribute('id', `post-${postid}Topic-${topicid}`);

  div.innerHTML = `
      <span class="text-sm text-gray-500">${topicname}</span>
  
      <button type="button" onclick="removeSpecificTopic(${topicid},${postid})" class="text-sm text-red-500 hover:text-red-700">Remove</button>
  `;

  topicDisplay.appendChild(div);

  if(topicDisplay.classList.contains('hidden')) {
    topicDisplay.classList.toggle('hidden');
  }

  
}

function removeSpecificTopic(topicid, postid) {
  //remove from the display topics so that the user knows that the topic was removed
  let topic = document.getElementById(`post-${postid}Topic-${topicid}`);
  topic.remove();

  //remove from the selected array so that the the user can select it again
  const index = selectedTopics.findIndex(id => id === topicid);
  const deleted = selectedTopics.splice(index, 1);
}

let topicsToDelete = []
//adds a topic to the list of topics that is going to be removed when the update is done
function addToDeleteTopic(topicid, postid) {
  if(!topicsToDelete.includes(topicid)) {
    topicsToDelete.push(topicid);
  }
  //remove the topic from the screen
  removeSpecificTopic(topicid,postid);
}



function syncPostTopicsWithInputEventListener() {
  document.querySelector('.addPost form')?.addEventListener('submit', function (e) {
    //update the values before sending the form
    let topicInput = document.getElementById('topicInput-0');
    topicInput.value = selectedTopics;
  });
}


addEventListeners();