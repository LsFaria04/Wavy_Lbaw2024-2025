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
  
    setupCreateUserMenu();
  
    addEventListenerEditUserAdmin();
    eventListernerFormsAdmin();
    
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


  addEventListeners();