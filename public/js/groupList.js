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

    try {
        let results = JSON.parse(this.responseText); // Parse the JSON response

        // Ensure the results structure exists and has userGroups and searchGroups
        if (results.userGroups && results.searchGroups) {
            switch (searchGroupCategory) {
                case 'your-groups':
                    if (results.userGroups.data.length === 0) {
                        groupResults.innerHTML = `
                            <div class="flex justify-center items-center h-32">
                                <p class="text-gray-600 text-center">You are not part of any groups.</p>
                            </div>
                        `;
                        return;
                    }
                    maxPage = results.userGroups.lastPage;
                    insertMoreGroups(groupResults, results.userGroups);
                    break;

                case 'search-groups':
                    if (results.searchGroups.data.length === 0) {
                        groupResults.innerHTML = `
                            <div class="flex justify-center items-center h-32">
                                <p class="text-gray-600 text-center">No groups found matching your search.</p>
                            </div>
                        `;
                        return;
                    }
                    maxPage = results.searchGroups.lastPage;
                    insertMoreGroups(groupResults, results.searchGroups);
                    break;

                default:
                    return;
            }
        } else {
            // If the results don't have userGroups or searchGroups, show a default message
            groupResults.innerHTML = `
                <div class="flex justify-center items-center h-32">
                    <p class="text-gray-600 text-center">An error occurred, please try again later.</p>
                </div>
            `;
        }
    } catch (e) {
        console.error('Failed to parse response:', e);
        groupResults.innerHTML = `
            <div class="flex justify-center items-center h-32">
                <p class="text-gray-600 text-center">An error occurred while fetching data. Please try again.</p>
            </div>
        `;
    }
}


addEventListeners();
