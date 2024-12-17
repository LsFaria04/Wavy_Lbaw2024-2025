function addEventListeners() {
  document.addEventListener('DOMContentLoaded', fadeAlert);

  reportFormSubmission();
  
}

  function encodeForAjax(data) {
    if (data == null) return null;
    return Object.keys(data).map(function(k){
      return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
    }).join('&');
  }

  function sendAjaxRequest(method, url, data, handler) {
    // Debug Statement: console.log(`Method: ${method}, URL: ${url}, Data:`, data);
    let request = new XMLHttpRequest();
    request.open(method, url, true);
    request.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    // Debug Statement: request.onerror = function () {
      // Debug Statement: console.error('AJAX request failed.');
    // Debug Statement: };

    // Debug Statement: request.onload = function () {
        // Debug Statement: console.log('Response Status:', this.status);
        // Debug Statement: console.log('Response Text:', this.responseText);
    // Debug Statement: };

    request.addEventListener('load', handler);
    request.send(encodeForAjax(data));
  }

  //gets the csrf token to insert in new forms
  function getCsrfToken(){
    return document.querySelector('meta[name="csrf-token"]').content;
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

  function createAlert(element,message, isError){
    const alert = document.createElement('div');
    alert.classList.add("self-center", "alert", "rounded","w-full", "max-w-full", "p-4", isError ? "bg-red-100" : "bg-green-100" , isError ?  "text-red-800" : "text-green-800" , "border", "shadow-md", "text-center", isError ? "border-red-300" : "border-green-300", "z-10");
    alert.innerHTML = message;
    element.appendChild(alert);
    setTimeout(() => { alert.remove()}, 3000);
  }

  function passwordRecovery(){
    //hide the email form and show the token form 
    const recoveryEmail = document.getElementById('recoveryEmail');
    
    const email = document.getElementById('email');
    const recoverPasswordDiv = document.getElementById('recoveryContainer');
    
    recoveryEmail.classList.add('hidden');
    recoveryEmail.classList.remove('flex');

    insertLoadingCircle(recoverPasswordDiv);
    sendAjaxRequest('post', '/forgot-password', {'email': email.value}, emailSentConfirmation);
  }

  function emailSentConfirmation(){
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

  function tokenCheck(){
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

  function tokenCheckConfirmation(){
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


  function toggleReportForm(contentId, category){
    const reportModal = document.getElementById('reportFormModal');
    reportModal.classList.toggle('hidden');
    reportModal.classList.toggle('flex');

    if(contentId == null){
      return;
    }

    if(category === 'post'){
      document.getElementById('reportPost').value = contentId;
    }
    else{
      category.getElementById('reportComment').value = contentId;
    }
  }

  function reportFormSubmission(){
    document.getElementById('reportFormModal')?.addEventListener('submit', function (form){
      form.preventDefault();
      let reason = document.querySelector("#reason").value;
      let postid = document.querySelector("#reportPost").value;
      let commentid = document.querySelector("#reportComment").value;
      let sendButton = document.querySelector("#sendReport");
      insertLoadingCircle(sendButton);
      
      //resize the loading circle
      document.querySelector('#loading_circle').classList.remove('h-8');
      document.querySelector('#loading_circle').classList.remove('w-8');
      document.querySelector('#loading_circle').classList.add('h-6');
      document.querySelector('#loading_circle').classList.add('w-6');

      sendAjaxRequest('post', '/api/reports/create', {'reason' : reason, "postid" : postid, "commentid" : commentid, "userid": userId}, confirmReport);
    });
  }

  function confirmReport(){
    removeLoadingCircle();
    toggleReportForm(null, null);
    const response = JSON.parse(this.responseText);
    
    const messageContainer = document.getElementById("messageContainer");
    if(response.response === '200'){
      createAlert(messageContainer, response.message,false);
    }
    else{
      createAlert(messageContainer, response.message,true);
    }

  }

  addEventListeners();
