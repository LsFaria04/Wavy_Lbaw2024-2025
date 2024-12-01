 
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
 
 
 //used to switch from section in the admin page(only for the final product)
  function showSectionAdmin(sectionId) {
    /*
      document.querySelectorAll('.tab-section').forEach((el) => {
          el.classList.add('hidden');
      });

      document.getElementById(sectionId).classList.remove('hidden');
      */
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


//Admin Delete Post
document.querySelectorAll('.delete-post-button').forEach(button => {
  button.addEventListener('click', function(event) {
      const postId = event.target.getAttribute('data-post-id');
      const postMessage = event.target.getAttribute('data-post-message');

      openDeleteMenu(postId, postMessage);
  });
});


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

addEventListeners();