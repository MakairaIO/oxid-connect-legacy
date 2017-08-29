import { debounce } from '../lib/helper'

const searchInputId = 'searchParam';
const classAutosuggestionContainer = 'makaira-autosuggestion'

const SuggestionItem = (item) => {
  return (
    `<li class="makaira-autosuggestion__list-item">
      <a href="${item.link}" class="makaira-autosuggestion__link">
        <figure class="makaira-autosuggestion__image">
          <img src="${item.image}">
        </figure>
        <span class="makaira-autosuggestion__title">${item.label}</span>
      </a>
     </li>`
  )
};

const SuggestionList = ({ items, productCount }) => {
  return (
    `<ul class="makaira-autosuggestion__list">
      ${items.map(SuggestionItem).join('')}
      <li class="makaira-autosuggestion__list-item">
        <button class="makaira-autosuggestion__submit" type="submit">
          Alle Ergebnisse anzeigen (${productCount})
        </button>
      </li>
     </ul>`
  )
};

const renderAutosuggestions = (response, searchForm) => {
  // if its the first time we render, add the initial container
  if (!document.querySelector(`.${classAutosuggestionContainer}`)) {
    let container = document.createElement('div');
    container.className = 'makaira-autosuggestion'
    searchForm.appendChild(container);
  }

  // render List
  const autosuggestionContainer = document.querySelector(`.${classAutosuggestionContainer}`)
  autosuggestionContainer.innerHTML = SuggestionList(response)
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
        renderAutosuggestions(JSON.parse(response), searchForm);
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
    // close for alle targets except the searchInput and submitbutton
    if (targetNotSearchInput && targetNotSubmitButton) {
      autosuggestionContainer.innerHTML = '';
    }
  }
};

const initHandlers = () => {
  const searchInput = document.getElementById(`${searchInputId}`);
  // fetch and display autosuggestions
  searchInput.addEventListener('input', fetchAutosuggestions);

  // close autosuggestion list
  document.body.addEventListener('click', closeAutosuggestions);
};

initHandlers();
