const classFilterList = 'makaira-filter__list';
const classFilterListExpanded = 'makaira-filter__list--expanded';
const classFilterItemHidden = 'makaira-filter__item--hidden';
const classListExpandButton = 'makaira-filter__button--expand';
const classListCollapseButton = 'makaira-filter__button--collapse';

const expandList = (list, buttonItem) => {
  list.classList.add(classFilterListExpanded);
  buttonItem.classList.add(classFilterItemHidden);
};

const collapseList = (list) => {
  const listExpandButton = list.querySelector(`.${classListExpandButton}`);
  const listExpandButtonItem = listExpandButton.parentNode;

  list.classList.remove(classFilterListExpanded);
  listExpandButtonItem.classList.remove(classFilterItemHidden);
};

const initHandlers = () => {
  const filterLists = document.querySelectorAll(`.${classFilterList}`);

  const addExpandButtonHandler = (list) => {
    // delegate button-click to filter-list
    list.addEventListener('click', (event) => {
      const target = event.target;
      if (target.classList.contains(classListExpandButton)) {
        expandList(list, target.parentNode);
      }
    });
  };

  const addCollapseButtonHandler = (list) => {
    // delegate button-click to filter-list
    list.addEventListener('click', (event) => {
      const target = event.target;
      if (target.classList.contains(classListCollapseButton)) {
        collapseList(list);
      }
    });
  };

  filterLists.forEach((list) => {
    addExpandButtonHandler(list);
    addCollapseButtonHandler(list);
  });
}

initHandlers();
