function addEventListeners() {
    syncCommentFilesWithInputEventListener();
    addEventListenerToCommentForms();
}

//inserts the media (images, audio and video) of a comment into a comment container. Returns the updated comment container
function insertCommentMedia(comment, mediaArray){
  const commentbody = comment.querySelector('.comment-body');
  let mediaContainer = document.createElement('div');
  mediaContainer.classList.add("comment-media", "mt-4", "grid", "grid-cols-2", "gap-4");
  
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
  
  commentbody.appendChild(mediaContainer);
  
  return comment;
}

function toggleEditComment(commentid) {
  const editForm = document.getElementById(`edit-comment-${commentid}`);
  const commentContent = document.getElementById(`comment-content-${commentid}`);
  const editFormFields = editForm.querySelectorAll('input, textarea, select'); // Editable fields

  // File-related elements
  const fileInput = document.getElementById(`image-${commentid}`);
  const fileDisplay = document.getElementById(`fileDisplay-${commentid}`);
  const newFilesContainer = document.getElementById(`newFiles-${commentid}`);
  const removeMediaInput = document.getElementById(`removeMedia-${commentid}`);


  // Toggle visibility
  editForm.classList.toggle('hidden');
  commentContent.classList.toggle('hidden');

  // If showing the edit form, save original values
  if (!editForm.classList.contains('hidden')) {
      if (!originalFormData[commentid]) {
          // Store the original values in the object
          originalFormData[commentid] = {
              formValues: {},
              mediaFiles: fileDisplay.innerHTML, // Store current files displayed
              removeMedia: removeMediaInput.value, // Store any files marked for removal
          };
          editFormFields.forEach(field => {
              originalFormData[commentid].formValues[field.name] = field.value;
          });
      }
  } else {
      // If hiding the edit form without saving, restore original values
      if (originalFormData[commentid]) {
          // Restore original form values
          editFormFields.forEach(field => {
              field.value = originalFormData[commentid].formValues[field.name];
          });

          // Restore original files
          fileDisplay.innerHTML = originalFormData[commentid].mediaFiles;
          removeMediaInput.value = originalFormData[commentid].removeMedia;
          
          // Clear new files added during the edit session
          if (newFilesContainer) {
              newFilesContainer.innerHTML = "";
          }

          // Reset the file input
          fileInput.value = "";
      }

      // Reset file input and media display if switching from edit form to comment content
      selectedFilesEdit = []; // Reset media selection when returning to the comment content view
  }
}

// Open delete confirmation menu
function openDeleteCommentMenu(commentid) {
  console.log("hello");
  const deleteMenu = document.getElementById('deleteCommentMenu');
  deleteMenu.classList.remove('hidden');
  deleteMenu.classList.add('flex');
  html.classList.add('overflow-hidden'); // Ensure the whole page is locked
  window.selectedCommentId = commentid;
}

function closeDeleteCommentMenu() {
  console.log("Closing the delete comment");
  const modal = document.getElementById('deleteCommentMenu');
  modal.classList.add('hidden');
}


function updateFileNameEditComment(commentId) {
  const fileInput = document.getElementById(`image-${commentId}`);
  const fileDisplay = document.getElementById(`fileDisplay-${commentId}`);
  const newFileDisplay = document.getElementById(`newFiles-${commentId}`)


  Array.from(fileInput.files).forEach(file => {
    if (file.size > 2097152){
      alert('File too big');
    }
    else{
      selectedFilesEdit.push(file);
    }
  });

  console.log(selectedFilesEdit);

  
  const lenStoreMedia = fileDisplay.querySelectorAll('div').length - 1;
  // Check if there are more than 4 files
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
          <button type="button" onclick="removeSpecificFileEdit(${commentId}, ${index})" class="text-sm text-red-500 hover:text-red-700">Remove</button>
      `;
      newFileDisplay.appendChild(li);
  });

  fileDisplay.classList.remove('hidden');
  fileInput.value = '';
}

function removeFileEdit(commentId, mediaId) {
  const fileElement = document.getElementById(`file-${mediaId}`);
  const removeMediaInput = document.getElementById(`removeMedia-${commentId}`);
  let removedFiles = JSON.parse(removeMediaInput.value);

  // Add the removed mediaId to the array for server processing
  removedFiles.push(mediaId);
  removeMediaInput.value = JSON.stringify(removedFiles);

  // Remove the media element from the display
  fileElement.remove();
}

// Synchronize selectedFilesEdit with the file input before form submission
function addEventListenerToCommentForms(){

  if(document.querySelectorAll('.edit-comment-form form').length === 0){
    //did not found comment edit forms
    return;
  }

  document.querySelectorAll('.edit-comment-form form').forEach(function (form) {
    addEventListenerToCommentForm(form);
  });
}

//removes the file from the options in the edit comment menu
function removeFileEdit(commentId, mediaId) {
  const fileElement = document.getElementById(`file-${mediaId}`);
  const removeMediaInput = document.getElementById(`removeMedia-${commentId}`);
  let removedFiles = JSON.parse(removeMediaInput.value);

  // Add the removed mediaId to the array for server processing
  removedFiles.push(mediaId);
  removeMediaInput.value = JSON.stringify(removedFiles);

  // Remove the media element from the display
  fileElement.remove();
}

function removeSpecificFileEdit(commentId, index) {
  const fileDisplay = document.getElementById(`newFiles-${commentId}`);

  // Remove file from the list
  selectedFilesEdit.splice(index, 1);

  // Clear previous display and update with new list
  fileDisplay.innerHTML = '';
  selectedFilesEdit.forEach((file, i) => {
      const li = document.createElement('li');
      li.classList.add('flex', 'items-center', 'gap-2');

      li.innerHTML = `
          <span class="text-sm text-gray-500">${file.name}</span>
          <button type="button" onclick="removeSpecificFileEdit(${commentId}, ${i})" class="text-sm text-red-500 hover:text-red-700">Remove</button>
      `;
      fileDisplay.appendChild(li);
  });

  // Hide the display if no files remain
  if (selectedFilesEdit.length === 0) {
      fileDisplay.classList.add('hidden');
  }
}

//adds a event listener to a comment form
function addEventListenerToCommentForm(form){
  form.addEventListener('submit', function (e) {
    const commentId = form.dataset.commentId;
    console.log(commentId);
    const fileInput = document.getElementById(`image-${commentId}`);
    
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
//inserts the delete menu into a comment container. Returns an updated comment
function insertDeleteCommentMenu(comment){
  const commentheader = comment.querySelector('.comment-header');
  let menu = document.createAttribute('div');
  menu.setAttribute('id', 'deleteCommentMenu');
  menu.classList("fixed", "inset-0", "bg-black", "bg-opacity-50", "hidden", "flex", "items-center", "justify-center", "z-20");
  
  menu.innerHTML = `
    <div class="bg-white rounded-xl shadow-lg p-6 max-w-sm w-full">
        <h2 class="text-xl font-semibold text-gray-900">Delete comment</h2>
        <p class="mt-4 text-sm text-gray-600">Are you sure you want to delete this comment? This action cannot be undone.</p>
        <div class="mt-6 flex justify-end gap-3">
            <button id="cancelCommentButton" class="px-4 py-2 text-white bg-gray-400 hover:bg-gray-600 rounded-2xl focus:outline-none">
                Cancel
            </button>
            <button id="confirmCommentButton" class="px-4 py-2 text-white bg-red-600 hover:bg-red-700 rounded-2xl focus:outline-none">
                Delete
            </button>
        </div>
    </div>
  `
  
  commentheader.appendChild(menu);
  
  return comment;
}

//inserts the update comment form into a comment container. Return the updated comment container.
function insertUpdateCommentForm(comment, id, message, media){
  let formContainer = document.createElement('div');
  formContainer.classList.add("edit-comment-form", "hidden", "mt-4", "bg-white", "rounded-xl", "shadow-md", "p-4");
  formContainer.setAttribute('id',"edit-comment-" + id);
  
  formContainer.innerHTML = `
    <form action="/comments/update/${id}" method="POST" enctype="multipart/form-data"  class="flex flex-col gap-4" data-comment-id = "${id}">
        <input type="hidden" name="_token" value= ${getCsrfToken()} />
        <div class="mb-4">
            <label for="message" class="block text-sm font-medium text-gray-700">Edit Message</label>
            <textarea id="message" name="message" rows="2" class="mt-1 block w-full p-4 border rounded-xl focus:ring-2 focus:ring-sky-700 shadow-sm outline-none" placeholder="Edit your message">${ message}</textarea>
        </div>
  
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Edit Media</label>
  
              <label for="image-${id}" class="cursor-pointer flex items-center gap-2 text-gray-500 hover:text-black mt-2">
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
  
  comment.appendChild(formContainer);
  
  return comment
}

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

function syncCommentFilesWithInputEventListener(){
  // Synchronize selectedFiles with the file input before form submission
  document.getElementById('commentForm')?.addEventListener('submit', function (e) {
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

  document.getElementById('subCommentForm')?.addEventListener('submit', function (e) {
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
function addEventListenerToCommentForms(){

  if(document.querySelectorAll('.edit-comment-form form').length === 0){
    //did not found comment edit forms
    return;
  }

  document.querySelectorAll('.edit-comment-form form').forEach(function (form) {
    addEventListenerToCommentForm(form);
  });
}

/*
function likeComment(commentId) {
  
  const likeCountElement = document.getElementById(`like-count-${commentId}`);
  
  const heartEmpty = document.getElementById(`heart-empty-${commentId}`);
  const heartFilled = document.getElementById(`heart-filled-${commentId}`);

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
*/
function likeComment(commentId,event) {

  event?.stopPropagation();

  if(isadmin) return;

  if (userId == -1) return;
  
  if (commentId == null) return;

  const likeCountElement = document.getElementById(`like-count-${commentId}`);
  const heartEmpty = document.getElementById(`heart-empty-${commentId}`);
  const heartFilled = document.getElementById(`heart-filled-${commentId}`);

  console.log(commentId);
  console.log("Liked .");

  // Make the AJAX request to like/unlike the comment
  sendAjaxRequest('post', '/like-comment/' + commentId, null,  updateLikeComment);

function updateLikeComment() {
    const response = JSON.parse(this.responseText);
    if (response.liked) {
        heartEmpty.classList.add('hidden');
        heartFilled.classList.remove('hidden');
        likeCountElement.textContent = parseInt(response.likeCount);
        likeCountElement.classList.add('text-red-600');
    } else {
        heartEmpty?.classList.remove('hidden');
        heartFilled?.classList.add('hidden');
        if (likeCountElement !== null) {
          likeCountElement.textContent = parseInt(response.likeCount);
          likeCountElement.classList.remove('text-red-600');
        }
    }
  }
}

function toggleSubcommentForm(commentId) {
    const existingForm = document.getElementById(`subComment-form-${commentId}`);
    
    document.querySelectorAll('.addComment').forEach(function(form) {
      if (form.id !== 'subComment-form-' + commentId) {
        form.remove(); // Remove the form from the DOM
      }
    });
  
    if (existingForm) {
        // Toggle visibility of the existing form
        existingForm.classList.toggle('hidden');
    } else {
        // Create a new form if it doesn't exist
        const parentComment = document.getElementById(`comment-content-${commentId}`);
        if (!parentComment) {
            console.error(`Comment with ID ${commentId} not found.`);
            return;
        }

        // Create the form dynamically
        const newForm = document.createElement('div');
        newForm.id = `subComment-form-${commentId}`;
        newForm.classList.add('addComment', 'mt-4', 'p-4', 'bg-gray-50', 'rounded-xl', 'shadow-md', 'border');

        newForm.innerHTML = `
            <form id="subCommentForm-${commentId}" action="/comments/storeSubcomment" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                <input type="hidden" name="parent_comment_id" value="${commentId}">
                
                <!-- Text Area -->
                <textarea id="message-${commentId}" name="message" rows="3"
                    class="w-full p-4 rounded-xl border focus:ring-2 focus:ring-sky-700 shadow-sm outline-none resize-none placeholder-gray-400 text-gray-700 text-sm"
                    placeholder="Write your comment here..."></textarea>
                
                <!-- Action Buttons -->
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <label for="image-${commentId}" class="cursor-pointer flex items-center gap-2 text-gray-500 hover:text-black">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6">
                                <path d="M19.828 11.244L12.707 18.364C10.755 20.317 7.589 20.317 5.636 18.364C3.684 16.411 3.684 13.246 5.636 11.293L12.472 4.458C13.774 3.156 15.884 3.156 17.186 4.458C18.488 5.759 18.488 7.87 17.186 9.172L10.361 15.996C9.71 16.647 8.655 16.647 8.004 15.996C7.353 15.345 7.353 14.29 8.004 13.639L14.226 7.418" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            <span class="text-sm">Attach Media</span>
                        </label>
                        <input type="file" name="media[]" id="image-${commentId}" class="hidden" multiple onchange="updateFileList()">
                    </div>
                    
                    <button type="submit" class="px-6 py-2 bg-sky-700 text-white font-semibold rounded-xl hover:bg-sky-800 text-sm">
                        Comment
                    </button>
                </div>
                
                <ul id="fileDisplay-${commentId}" class="text-sm text-gray-500 mt-2 hidden"></ul>
            </form>
        `;

        // Append the form to the parent comment
        parentComment.parentNode.insertBefore(newForm, parentComment.nextSibling);
    }
}

function createComment(commentInfo){
  console.log(commentInfo);
  let comment = document.createElement('div');
  comment.classList.add("comment", "mb-4", "p-4","bg-white","rounded-md" , "shadow", "cursor-pointer");

  let subcommentsHtml = '';

  const subcomments = Array.isArray(commentInfo.subcomments) ? commentInfo.subcomments : [];
  
  if (subcomments.length > 0) {
    subcommentsHtml = insertMoreSubCommentsToComment(subcomments); 
  }
  
  comment.innerHTML = `
    <div class="comment-header mb-2 flex justify-between items-center">
        <div>
            <h3 class="font-bold">
              <a href="${ commentInfo.user.state === 'deleted' ? '#' : '../profile/' + commentInfo.user.username }" 
                  class="text-black hover:text-sky-900">
                  ${ commentInfo.user.state === 'deleted' ? 'Deleted User' : commentInfo.user.username }
              </a>
            </h3>
            <span class="text-gray-500 text-sm">${ commentInfo.createddate }</span>
        </div>
    </div>
    <div class="comment-body mb-2" id=comment-content-${commentInfo.commentid}>
        <p>${ commentInfo.message }</p>
    </div>
    <div class="comment-interactions flex items-center gap-4 mt-4">
          ${createCommentLikeButton(commentInfo.commentid, commentInfo.likes_count, commentInfo.liked)}
          ${createCommentCommentButton(commentInfo.commentid, commentInfo.subcomments_count)}
    </div>
    <div class="subcomments mt-4 pl-4 border-l border-gray-200">
          ${subcommentsHtml}   <!-- Recursive call -->
    </div>
    <div id="subComment-form-${commentInfo.commentid}" class="addComment mt-4 p-4 bg-gray-50 rounded-xl shadow-md border hidden">
      ${createCommentHiddenForm(commentInfo.commentid)}
    </div>
  `;
  console.log(commentInfo.subcomments);
  return comment
}


function createCommentHiddenForm(commentId){
  return `
        <form id="subCommentForm" action="/comments/storeSubcomment" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
            <input type="hidden" name="parent_comment_id" value="${commentId}">

            <!-- Text Area -->
            <textarea id="message" name="message" rows="3"
                    class="w-full p-4 rounded-xl border focus:ring-2 focus:ring-sky-700 shadow-sm outline-none resize-none placeholder-gray-400 text-gray-700 text-sm"
                    placeholder="Write your comment here..."></textarea>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <label for="image" class="cursor-pointer flex items-center gap-2 text-gray-500 hover:text-black">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6">
                            <path d="M19.828 11.244L12.707 18.364C10.755 20.317 7.589 20.317 5.636 18.364C3.684 16.411 3.684 13.246 5.636 11.293L12.472 4.458C13.774 3.156 15.884 3.156 17.186 4.458C18.488 5.759 18.488 7.87 17.186 9.172L10.361 15.996C9.71 16.647 8.655 16.647 8.004 15.996C7.353 15.345 7.353 14.29 8.004 13.639L14.226 7.418" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                        <span class="text-sm">Attach Media</span>
                    </label>
                    <input type="file" name="media[]" id="image" class="hidden" multiple onchange="updateFileList()">
                </div>

                <button type="submit" class="px-6 py-2 bg-sky-700 text-white font-semibold rounded-xl hover:bg-sky-800 text-sm">
                    Comment
                </button>
            </div>

            <ul id="fileDisplay" class="text-sm text-gray-500 mt-2 hidden">
                <!-- File names appended dynamically -->
            </ul>
        </form>
  `
}

function createCommentLikeButton(commentId, likeCount, likedByUser) {
  console.log(likeCount);
  return `
      <div class="comment-likes flex items-center gap-2">
          <button 
              type="button" 
              class="flex items-center text-gray-500 hover:text-red-600 group" 
              onclick="likeComment(${commentId}, event); event.stopPropagation();">
              
              <!-- No like -->
              <svg 
                  id="heart-empty-${commentId}" viewBox="0 0 24 24" aria-hidden="true"
                  class="h-5 w-5 ${likedByUser ? 'hidden' : 'fill-gray-500 hover:fill-red-600 group-hover:fill-red-600'}">
                  <g>
                      <path d="M16.697 5.5c-1.222-.06-2.679.51-3.89 2.16l-.805 1.09-.806-1.09C9.984 6.01 8.526 5.44 7.304 5.5c-1.243.07-2.349.78-2.91 1.91-.552 1.12-.633 2.78.479 4.82 1.074 1.97 3.257 4.27 7.129 6.61 3.87-2.34 6.052-4.64 7.126-6.61 1.111-2.04 1.03-3.7.477-4.82-.561-1.13-1.666-1.84-2.908-1.91zm4.187 7.69c-1.351 2.48-4.001 5.12-8.379 7.67l-.503.3-.504-.3c-4.379-2.55-7.029-5.19-8.382-7.67-1.36-2.5-1.41-4.86-.514-6.67.887-1.79 2.647-2.91 4.601-3.01 1.651-.09 3.368.56 4.798 2.01 1.429-1.45 3.146-2.1 4.796-2.01 1.954.1 3.714 1.22 4.601 3.01.896 1.81.846 4.17-.514 6.67z"></path>
                  </g>
              </svg>
              
              <!-- Yes like -->
              <svg 
                  id="heart-filled-${commentId}" viewBox="0 0 24 24" aria-hidden="true"
                  class="h-5 w-5 ${likedByUser ? 'fill-red-600 group-hover:fill-red-600' : 'hidden'}">
                  <g>
                      <path d="M20.884 13.19c-1.351 2.48-4.001 5.12-8.379 7.67l-.503.3-.504-.3c-4.379-2.55-7.029-5.19-8.382-7.67-1.36-2.5-1.41-4.86-.514-6.67.887-1.79 2.647-2.91 4.601-3.01 1.651-.09 3.368.56 4.798 2.01 1.429-1.45 3.146-2.1 4.796-2.01 1.954.1 3.714 1.22 4.601 3.01.896 1.81.846 4.17-.514 6.67z"></path>
                  </g>
              </svg>
              
              <span id="like-count-${commentId}" class="ml-1 group-hover:text-red-600">${likeCount}</span>
          </button>
      </div>
  `;
}
function createCommentCommentButton(commentId, commentCount = 0) {
  return `
      <div class="comment-comments flex items-center gap-2">
            <button 
                type="button" 
                class="flex items-center text-gray-500 hover:text-sky-600 group" 
                onclick="toggleSubcommentForm(${commentId})">
                
                <!-- Comment Icon -->
                <svg 
                    xmlns="http://www.w3.org/2000/svg" 
                    id="comment-icon-${commentId}" 
                    class="h-5 w-5 fill-gray-500 group-hover:fill-sky-600 transition duration-200 ease-in-out" 
                    viewBox="0 0 24 24" 
                    fill="currentColor">
                    <path d="M12 2C6.477 2 2 6.067 2 10.5c0 1.875.656 3.625 1.844 5.094l-1.308 3.922c-.19.57.474 1.065.997.736l3.875-2.325A9.435 9.435 0 0012 19c5.523 0 10-4.067 10-8.5S17.523 2 12 2zm0 2c4.418 0 8 3.067 8 6.5S16.418 17 12 17c-1.173 0-2.292-.232-3.318-.656a1 1 0 00-.97.035l-2.898 1.739.835-2.501a1 1 0 00-.176-.964A7.36 7.36 0 014 10.5C4 7.067 7.582 4 12 4z" />
                </svg>
                
                <!-- Comment Count -->
                <span id="comment-count-${commentId}" 
                    class="ml-1 group-hover:text-sky-600 transition duration-200 ease-in-out">
                    ${commentCount}
                </span>
            </button>
        </div>
  `;
}

function createCommentOptions(comment, id, needReport){
  const commentheader = comment.querySelector('.comment-header');
  let options = document.createElement('div');
  options.classList.add("flex", "items-center", "gap-2");
  options.setAttribute('id', 'commentOptions');
  
  if(!needReport){
    options.innerHTML = `
      <button type="button" onclick="toggleEditComment(${id})" class="text-gray-500 hover:text-black">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="black" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="0.6" d="M10.973 1.506a18.525 18.525 0 00-.497-.006A4.024 4.024 0 006.45 5.524c0 .43.095.865.199 1.205.054.18.116.356.192.527v.002a.75.75 0 01-.15.848l-4.937 4.911a.871.871 0 000 1.229.869.869 0 001.227 0L7.896 9.31a.75.75 0 01.847-.151c.17.079.35.139.529.193.34.103.774.198 1.204.198A4.024 4.024 0 0014.5 5.524c0-.177-.002-.338-.006-.483-.208.25-.438.517-.675.774-.32.345-.677.696-1.048.964-.354.257-.82.512-1.339.512-.396 0-.776-.156-1.059-.433L9.142 5.627a1.513 1.513 0 01-.432-1.06c0-.52.256-.985.514-1.34.27-.37.623-.727.97-1.046.258-.237.529-.466.78-.675zm-2.36 9.209l-4.57 4.59a2.37 2.37 0 01-3.35-3.348l.002-.001 4.591-4.568a6.887 6.887 0 01-.072-.223 5.77 5.77 0 01-.263-1.64A5.524 5.524 0 0110.476 0 12 12 0 0112 .076c.331.044.64.115.873.264a.92.92 0 01.374.45.843.843 0 01-.013.625.922.922 0 01-.241.332c-.26.257-.547.487-.829.72-.315.26-.647.535-.957.82a5.947 5.947 0 00-.771.824c-.197.27-.227.415-.227.457 0 .003 0 .006.003.008l1.211 1.211a.013.013 0 00.008.004c.043 0 .19-.032.46-.227.253-.183.532-.45.826-.767.284-.308.56-.638.82-.95.233-.28.463-.565.72-.823a.925.925 0 01.31-.235.841.841 0 01.628-.033.911.911 0 01.467.376c.15.233.22.543.262.87.047.356.075.847.075 1.522a5.524 5.524 0 01-5.524 5.525c-.631 0-1.221-.136-1.64-.263a6.969 6.969 0 01-.222-.071z"/>
          </svg>
      </button>
      <form action="../comments/delete/${id}" method="POST" id="deleteCommentForm-${id}">
        <button type="button" onclick="openDeleteCommentMenu(${id})" class="text-red-500 hover:text-red-700 ml-2">
            <input type="hidden" name="_token" value= ${getCsrfToken()} />
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
      </form>
    `
  }else{
    options.innerHTML= `
      <button type="button" onclick="event.stopPropagation(); toggleReportForm('${id}', 'comment');" class="text-gray-500 hover:text-black">
          Report
      </button>
    `;
  }
  commentheader.appendChild(options);
  return comment;
}


  addEventListeners();
  