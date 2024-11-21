function addEventListeners() {
    let itemCheckers = document.querySelectorAll('article.card li.item input[type=checkbox]');
    [].forEach.call(itemCheckers, function(checker) {
      checker.addEventListener('change', sendItemUpdateRequest);
    });
  
    let itemCreators = document.querySelectorAll('article.card form.new_item');
    [].forEach.call(itemCreators, function(creator) {
      creator.addEventListener('submit', sendCreateItemRequest);
    });
  
    let itemDeleters = document.querySelectorAll('article.card li a.delete');
    [].forEach.call(itemDeleters, function(deleter) {
      deleter.addEventListener('click', sendDeleteItemRequest);
    });
  
    let cardDeleters = document.querySelectorAll('article.card header a.delete');
    [].forEach.call(cardDeleters, function(deleter) {
      deleter.addEventListener('click', sendDeleteCardRequest);
    });
  
    let cardCreator = document.querySelector('article.card form.new_card');
    if (cardCreator != null)
      cardCreator.addEventListener('submit', sendCreateCardRequest);

    document.addEventListener('DOMContentLoaded', fadeAlert);

    document.addEventListener('DOMContentLoaded', switchProfileTab);
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
    request.addEventListener('load', handler);
    request.send(encodeForAjax(data));
  }
  
  function sendItemUpdateRequest() {
    let item = this.closest('li.item');
    let id = item.getAttribute('data-id');
    let checked = item.querySelector('input[type=checkbox]').checked;
  
    sendAjaxRequest('post', '/api/item/' + id, {done: checked}, itemUpdatedHandler);
  }
  
  function sendDeleteItemRequest() {
    let id = this.closest('li.item').getAttribute('data-id');
  
    sendAjaxRequest('delete', '/api/item/' + id, null, itemDeletedHandler);
  }
  
  function sendCreateItemRequest(event) {
    let id = this.closest('article').getAttribute('data-id');
    let description = this.querySelector('input[name=description]').value;
  
    if (description != '')
      sendAjaxRequest('put', '/api/cards/' + id, {description: description}, itemAddedHandler);
  
    event.preventDefault();
  }
  
  function sendDeleteCardRequest(event) {
    let id = this.closest('article').getAttribute('data-id');
  
    sendAjaxRequest('delete', '/api/cards/' + id, null, cardDeletedHandler);
  }
  
  function sendCreateCardRequest(event) {
    let name = this.querySelector('input[name=name]').value;
  
    if (name != '')
      sendAjaxRequest('put', '/api/cards/', {name: name}, cardAddedHandler);
  
    event.preventDefault();
  }
  
  function itemUpdatedHandler() {
    let item = JSON.parse(this.responseText);
    let element = document.querySelector('li.item[data-id="' + item.id + '"]');
    let input = element.querySelector('input[type=checkbox]');
    element.checked = item.done == "true";
  }
  
  function itemAddedHandler() {
    if (this.status != 200) window.location = '/';
    let item = JSON.parse(this.responseText);
  
    // Create the new item
    let new_item = createItem(item);
  
    // Insert the new item
    let card = document.querySelector('article.card[data-id="' + item.card_id + '"]');
    let form = card.querySelector('form.new_item');
    form.previousElementSibling.append(new_item);
  
    // Reset the new item form
    form.querySelector('[type=text]').value="";
  }
  
  function itemDeletedHandler() {
    if (this.status != 200) window.location = '/';
    let item = JSON.parse(this.responseText);
    let element = document.querySelector('li.item[data-id="' + item.id + '"]');
    element.remove();
  }
  
  function cardDeletedHandler() {
    if (this.status != 200) window.location = '/';
    let card = JSON.parse(this.responseText);
    let article = document.querySelector('article.card[data-id="'+ card.id + '"]');
    article.remove();
  }
  
  function cardAddedHandler() {
    if (this.status != 200) window.location = '/';
    let card = JSON.parse(this.responseText);
  
    // Create the new card
    let new_card = createCard(card);
  
    // Reset the new card input
    let form = document.querySelector('article.card form.new_card');
    form.querySelector('[type=text]').value="";
  
    // Insert the new card
    let article = form.parentElement;
    let section = article.parentElement;
    section.insertBefore(new_card, article);
  
    // Focus on adding an item to the new card
    new_card.querySelector('[type=text]').focus();
  }
  
  function createCard(card) {
    let new_card = document.createElement('article');
    new_card.classList.add('card');
    new_card.setAttribute('data-id', card.id);
    new_card.innerHTML = `
  
    <header>
      <h2><a href="cards/${card.id}">${card.name}</a></h2>
      <a href="#" class="delete">&#10761;</a>
    </header>
    <ul></ul>
    <form class="new_item">
      <input name="description" type="text">
    </form>`;
  
    let creator = new_card.querySelector('form.new_item');
    creator.addEventListener('submit', sendCreateItemRequest);
  
    let deleter = new_card.querySelector('header a.delete');
    deleter.addEventListener('click', sendDeleteCardRequest);
  
    return new_card;
  }
  
  function createItem(item) {
    let new_item = document.createElement('li');
    new_item.classList.add('item');
    new_item.setAttribute('data-id', item.id);
    new_item.innerHTML = `
    <label>
      <input type="checkbox"> <span>${item.description}</span><a href="#" class="delete">&#10761;</a>
    </label>
    `;
  
    new_item.querySelector('input').addEventListener('change', sendItemUpdateRequest);
    new_item.querySelector('a.delete').addEventListener('click', sendDeleteItemRequest);
  
    return new_item;
  }

  function fadeAlert(){
    const alertBoxes = document.querySelectorAll('.alert');
      alertBoxes.forEach(alertBox => {
          setTimeout(() => {
              alertBox.remove()
          }, 3000); 
        });// Time before fade-out
  }

  function toggleEditMenu() {
    const menu = document.getElementById('edit-profile-menu');
    const html = document.documentElement;
    menu.classList.toggle('hidden');
    html.classList.toggle('overflow-hidden');
}
  
  addEventListeners();

  const buttons = document.querySelectorAll('.tab-btn');
  const sections = document.querySelectorAll('.tab-content');

  function switchProfileTab() {
    buttons.forEach(button => {
      button.addEventListener('click', () => {
        const targetTab = button.dataset.tab;

        // Toggle active button
        buttons.forEach(btn => {
          btn.classList.remove('text-sky-900', 'border-sky-900');
        });
        button.classList.add('text-sky-900', 'border-sky-900');

        // Toggle visible content
        sections.forEach(section => {
          if (section.id === targetTab) {
            section.classList.remove('hidden');
          } else {
            section.classList.add('hidden');
          }
        });
      });
    });
  }

  const navigationMenu = document.getElementById('navigation-menu');
  const menuText = document.querySelectorAll("#navigation-menu span");
  const menuOptions = document.querySelectorAll("#navigation-menu li");
  const menuHeader = document.querySelector("#navigation-menu header");
  const menuArrow = document.querySelector("#navigation-menu header button > svg");

  //Allows the expantion of the menu
  function navigationMenuOperation(){
    if(navigationMenu.classList.contains("lg:w-60")){
      navigationMenu.classList.remove("lg:w-60");
      navigationMenu.classList.add("lg:w-14");
    }
    else{
      navigationMenu.classList.add("lg:w-60");
      navigationMenu.classList.remove("lg:w-14");
    }
    menuText.forEach(function(element){
      element.classList.toggle("hidden");
    })
    menuOptions.forEach(function(option){
      option.classList.toggle("gap-3");
    })
    menuArrow.classList.toggle("rotate-180");
  }

  const searchMenu = document.getElementById('search-menu');
  const searchBar = document.getElementById('search-bar');
  const searchIcon = document.getElementById('search-icon');
  const searchMenuArrow = document.querySelector("#search-menu header button > svg");

  //allows the operation of the search menu
  function searchMenuOperation(){
    if(searchMenu.classList.contains("lg:w-60")){
      searchMenu.classList.remove("lg:w-60");
      searchMenu.classList.add("lg:w-14");
      searchBar.classList.add("hidden");
      searchIcon.classList.remove("hidden");
    }
    else{
      searchMenu.classList.add("lg:w-60");
      searchMenu.classList.remove("lg:w-14");
      searchBar.classList.remove("hidden");
      searchIcon.classList.add("hidden");
    }
    searchMenuArrow.classList.toggle("rotate-180");
  }

  function changeCategory(category) {
      document.querySelector('input[name="category"]').value = category;
      document.getElementById('search-form').submit();
  }

  function showSectionAdmin(sectionId) {
    document.querySelectorAll('.tab-section').forEach((el) => {
        el.classList.add('hidden');
    });

    document.getElementById(sectionId).classList.remove('hidden');
}
  // Toggle the edit form visibility
   function toggleEditPost(postid) {
    const editForm = document.getElementById(`edit-post-${postid}`);
    editForm.classList.toggle('hidden'); 
  }
  // Confirm delete dialog
  function confirmDelete() {
      return confirm('Are you sure you want to delete this post? This action cannot be undone.');
  }
