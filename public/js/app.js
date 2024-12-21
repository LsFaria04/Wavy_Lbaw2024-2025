function addEventListeners() {
  document.addEventListener('DOMContentLoaded', fadeAlert);

  let cancelCommentButton = document.getElementById('cancelCommentButton');

  if(cancelCommentButton !== null){
    cancelCommentButton.addEventListener('click', () => {
      const deleteMenu = document.getElementById('deleteCommentMenu');
      html.classList.toggle('overflow-hidden');
      deleteMenu.classList.add('hidden');
    });
  }

  let confirmCommentButton = document.getElementById('confirmCommentButton');
  if(confirmCommentButton !== null){
    confirmCommentButton.addEventListener('click', () => {
      const deleteForm = document.getElementById(`deleteCommentForm-${window.selectedCommentId}`);
      deleteForm.submit();
    });
  }

  reportFormSubmission();
  
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


//fades the alert messages after a certain period of time
function fadeAlert() {
const alertBoxes = document.querySelectorAll('.alert');
  alertBoxes.forEach(alertBox => {
      setTimeout(() => {
          alertBox.remove()
      }, 3000); 
    });// Time before fade-out
}

//creates an alert message and inserts it into the page
function createAlert(element,message, isError){
  const alert = document.createElement('div');
  alert.classList.add("self-center", "alert", "rounded","w-full", "max-w-full", "p-4", isError ? "bg-red-100" : "bg-blue-100" , isError ?  "text-red-800" : "text-blue-800" , "border", "shadow-md", "text-center", isError ? "border-red-300" : "border-blue-300", "z-10");
  alert.innerHTML = message;

  while(element.firstChild !== null){
    element.firstChild.remove();
  }
  
  element.appendChild(alert);
  setTimeout(() => { alert.remove()}, 3000);
}



//Emails related functions -----------------------------------------------------------------------------


function contactEmail(){
  const email = document.getElementById('email');
  const name = document.getElementById('name');
  const message = document.getElementById('message');
  const sendButton = document.getElementById('submit');
  insertLoadingCircle(sendButton);
  sendButton.disable = true;

  //resize the loading circle
  document.querySelector('#loading_circle').classList.remove('h-8');
  document.querySelector('#loading_circle').classList.remove('w-8');
  document.querySelector('#loading_circle').classList.add('h-6');
  document.querySelector('#loading_circle').classList.add('w-6');

  sendAjaxRequest('post', '/api/contact/submit', {'email': email.value, 'name' : name.value, 'message' : message.value}, contactEmailSentConfirmation);

}

function contactEmailSentConfirmation() {
  removeLoadingCircle();
  const response = JSON.parse(this.responseText);
  const messageDiv = document.getElementById('messageContainer');

  const sendButton = document.getElementById('submit');
  sendButton.disable = false;

  if(response.response !== '200'){
    createAlert(messageDiv, response.message, true);
  }
  else{
    createAlert(messageDiv, response.message, false);

  }

}

//Sends the email from the account that wants to recover the password
function passwordRecovery() {
  //hide the email form and show the token form 
  const recoveryEmail = document.getElementById('recoveryEmail');
  
  const email = document.getElementById('email');
  const recoverPasswordDiv = document.getElementById('recoveryContainer');
  
  recoveryEmail.classList.add('hidden');
  recoveryEmail.classList.remove('flex');

  insertLoadingCircle(recoverPasswordDiv);
  sendAjaxRequest('post', '/forgot-password', {'email': email.value}, emailSentConfirmation);
}

//handles the email sent confirmation from the server 
function emailSentConfirmation() {
  removeLoadingCircle();
  const response = JSON.parse(this.responseText);
  const messageDiv = document.getElementById('messageContainer');

  if(response.response !== '200'){
    recoveryEmail.classList.remove('hidden');
    recoveryEmail.classList.add('flex');
    createAlert(messageDiv, response.message, true);
  }
  else{
    const recoveryToken = document.getElementById('recoveryToken');
    recoveryToken.classList.remove('hidden');
    recoveryToken.classList.add('flex');
    createAlert(messageDiv, response.message, false);
  }
  
}

//Send to the server the token and the new credentials
function tokenCheck() {
  const recoverPasswordDiv = document.getElementById('recoveryContainer');
  insertLoadingCircle(recoverPasswordDiv);

  const email = document.getElementById('email');
  const token = document.getElementById('token');
  const password = document.getElementById('password');
  const passwordConf = document.getElementById('password-confirm');

  const recoveryToken = document.getElementById('recoveryToken');
    recoveryToken.classList.add('hidden');
    recoveryToken.classList.remove('flex');
  
  sendAjaxRequest('post', '/reset-password', {'email': email.value, 'password':password.value, 'password_confirmation': passwordConf.value, 'token':token.value}, tokenCheckConfirmation);
}

//handles the token verification verification from the server
function tokenCheckConfirmation() {
  removeLoadingCircle();
  const response = JSON.parse(this.responseText);
  let messageDiv = document.getElementById('messageContainer');

  if(response.response !== '200'){
    const recoveryToken = document.getElementById('recoveryToken');
    recoveryToken.classList.remove('hidden');
    recoveryToken.classList.add('flex');
    createAlert(messageDiv, response.message, true);
  }
  else{
    window.location.replace("/login");
      
  }

}


//Reports ----------------------------------------------------------------------------------------


//hides/shoes the report form when the user clicks in the report butto
function toggleReportForm(contentId, category) {
  const reportModal = document.getElementById('reportFormModal');
  reportModal.classList.toggle('hidden');
  reportModal.classList.toggle('flex');

  if(contentId == null) {
    return;
  }

  if(category === 'post') {
    document.getElementById('reportPost').value = contentId;
  }
  else {
    category.getElementById('reportComment').value = contentId;
  }
}

//submits the report to the server
function reportFormSubmission() {
  document.getElementById('reportFormModal')?.addEventListener('submit', function (form){
    form.preventDefault();
    let reason = document.querySelector("#reason").value;
    let postid = document.querySelector("#reportPost").value;
    let commentid = document.querySelector("#reportComment").value;
    let sendButton = document.querySelector("#sendReport");
    sendButton.disable = true;
    insertLoadingCircle(sendButton);
    
    //resize the loading circle
    document.querySelector('#loading_circle').classList.remove('h-8');
    document.querySelector('#loading_circle').classList.remove('w-8');
    document.querySelector('#loading_circle').classList.add('h-6');
    document.querySelector('#loading_circle').classList.add('w-6');

    sendAjaxRequest('post', '/api/reports/create', {'reason' : reason, "postid" : postid, "commentid" : commentid, "userid": userId}, confirmReport);
  });
}

//handles the report confirmation from the server
function confirmReport() {
  removeLoadingCircle();
  toggleReportForm(null, null);
  const response = JSON.parse(this.responseText);

  let sendButton = document.querySelector("#sendReport");
    sendButton.disable = false;
  
  const messageContainer = document.getElementById("messageContainer");
  if(response.response === '200') {
    createAlert(messageContainer, response.message,false);
  }
  else {
    createAlert(messageContainer, response.message,true);
  }

}



//Images -------------------------------------------------------------------------------


//toggles the images details when a user clicks on an image
function toggleImageDetails(src) {
  if(src !== null){
    document.getElementById('detailImg').src = src;
  }

  const imageDetailMenu = document.getElementById('imageDetail');
  imageDetailMenu.classList.toggle('hidden');
  imageDetailMenu.classList.toggle('flex');

}

addEventListeners();
