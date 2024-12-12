function addEventListeners() {
    document.addEventListener('DOMContentLoaded', fadeAlert);
    window.addEventListener("scroll", infiniteScroll);
}

// Used to change the search category when a user clicks on a search tab option
function changeGroupCategory(category) {
    currentPage = 1;  // Reset pagination to the first page

    searchGroupCategory = category;
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
    loadSearchGroupContent(category, query);
}

let searchGroupCategory = null;
if(document.querySelector('input[name="category"]') !== null){
    searchGroupCategory = document.querySelector('input[name="category"]').value;
}

// Loads the search content based on the selected category
function loadSearchGroupContent(category, query) {
    const groupResults = document.querySelector("#group-results");

    while (groupResults.firstChild) {
        groupResults.removeChild(groupResults.firstChild);
    }

    insertLoadingCircle(groupResults);
    sendAjaxRequest('get', '/groups?page=' + currentPage + "&q=" + query + "&category=" + category, null, insertMoreGroupSearchResults);
}

function insertMoreGroupSearchResults() {
    removeLoadingCircle();  // Remove the loading spinner
    const groupResults = document.querySelector("#group-results");

    let results = JSON.parse(this.responseText); // Parse the JSON response

    switch (searchGroupCategory) {
        case 'your-groups':
            if(results[0] === undefined) {
                break;
            }
            maxPage = results[0].lastPage;
            insertMoreGroups(groupResults, results[0]);
            break;

        case 'search-groups':
            if (results[1] === undefined) {
                break;
            }
            maxPage = results[1].lastPage;
            insertMoreGroups(groupResults, results[1]);
            break;

        default:
            return;
    }

    if(groupResults.firstChild == null){
        groupResults.innerHTML = `
          <div class="flex justify-center items-center h-32">
              <p class="text-gray-600 text-center">No groups found matching your search.</p>
          </div>
        `;       
    }
}

function toggleCreateGroupMenu() {
    const menu = document.getElementById('create-group-menu');
    menu.classList.toggle('hidden');
    menu.classList.toggle('flex');
}

addEventListeners();
