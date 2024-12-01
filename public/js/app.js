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



//gets the csrf token to insert in new forms
function getCsrfToken(){
return document.querySelector('meta[name="csrf-token"]').content;
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


//inserts more users into an element
function insertMoreUsers(element, users){
  for(let i = 0; i < users.data.length; i++){
    let user = createUser(users.data[i]);
    element.appendChild(user);
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


//fades the alert messages after a certain period of time
function fadeAlert(){
const alertBoxes = document.querySelectorAll('.alert');
  alertBoxes.forEach(alertBox => {
      setTimeout(() => {
          alertBox.remove()
      }, 3000); 
    });// Time before fade-out
}

const buttonsG = document.querySelectorAll('.tab-btn');
const sectionsG = document.querySelectorAll('.tab-content');
let groupTab = "group-posts"; // Default tab

function switchGroupTab() {
  buttonsG.forEach(button => {
    button.addEventListener('click', () => {
      currentPage = 1;  // Reset page for new tab content
      groupTab = button.dataset.tab;

      // Toggle active button
      buttonsG.forEach(btn => {
        btn.classList.remove('text-sky-900', 'border-sky-900');
      });
      button.classList.add('text-sky-900', 'border-sky-900');
      
      // TO-DO loadGroupContent(groupTab);
    });
  });
}


addEventListeners();
