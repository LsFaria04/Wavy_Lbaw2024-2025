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

  setupCreateUserMenu();
  //listeners related to the posts
  addEventListenerToPostForms();
  syncFilesWithInputEventListener()

  addEventListenerEditUserAdmin();
  eventListernerFormsAdmin();
  
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
    menu.classList.toggle('hidden');
    menu.classList.toggle('flex');
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

  addEventListeners();
  