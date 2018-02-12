import { debounce } from '../lib/helper'

const searchInputId = 'searchParam';
const searchInput = document.getElementById(`${searchInputId}`);
const classAutosuggestionContainer = 'makaira-autosuggestion'

const renderAutosuggestions = (response, searchForm) => {
  // if its the first time we render, add the initial container
  if (!document.querySelector(`.${classAutosuggestionContainer}`)) {
    let container = document.createElement('div');
    container.className = 'makaira-autosuggestion';
    searchForm.appendChild(container);
  }

  // render List
  const autosuggestionContainer = document.querySelector(`.${classAutosuggestionContainer}`);
  autosuggestionContainer.innerHTML = response;
};

const fetchAutosuggestions = debounce((event) => {
  const searchTerm = event.target.value.trim();
  const searchForm = event.target.parentNode;

  if (searchTerm.length > 2) {
    var request = new XMLHttpRequest();
    request.open('GET', `?cl=makaira_connect_autosuggest&term=${searchTerm}`, true);

    request.onload = () => {
      if (request.status >= 200 && request.status < 400) {
        // Succes -> we render the suggestions
        const response = request.responseText;
        renderAutosuggestions(response, searchForm);
      } else {
        // We reached our target server, but it returned an error
        console.error('Processing in Makaira failed');
      }
    };

    request.onerror = () => {
      // There was a connection error of some sort
      console.error('Connection to Makaira failed');
    };

    request.send();
  }

}, 250);

const closeAutosuggestions = (event) => {
  const target = event.target;
  const autosuggestionContainer = document.querySelector(`.${classAutosuggestionContainer}`)

  // only act if search has been fired at least once
  if (autosuggestionContainer) {
    let targetNotSearchInput = (target.id !== searchInputId);
    let targetNotSubmitButton = !target.classList.contains('makaira-autosuggestion__submit');
    // close for all targets except the searchInput and submitbutton
    if (targetNotSearchInput && targetNotSubmitButton) {
      autosuggestionContainer.innerHTML = '';
    }
  }
};

const setupSearchInput = () => {
  searchInput.setAttribute('autocomplete', 'off');
};

const initHandlers = () => {
  // fetch and display autosuggestions
  searchInput.addEventListener('input', fetchAutosuggestions);

  // close autosuggestion list
  document.body.addEventListener('click', closeAutosuggestions);
};

if (searchInput) {
  setupSearchInput();
  initHandlers();
}
