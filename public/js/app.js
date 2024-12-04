function addEventListeners() {
  document.addEventListener('DOMContentLoaded', fadeAlert);

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

  addEventListeners();
