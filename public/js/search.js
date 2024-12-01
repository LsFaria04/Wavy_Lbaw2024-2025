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

//loads the first content of a search when selecting another category
function loadSearchContent(category, query){
    const searchResults = document.querySelector("#search-results");
    
    while (searchResults.firstChild) {
      searchResults.removeChild(searchResults.firstChild);
    }
    
    insertLoadingCircle(searchPage);
    sendAjaxRequest('get', '/search?page=' + currentPage + "&" + 'q=' + query + "&" + "category=" + category, null, insertMoreSearchResults);
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

  
  addEventListeners();