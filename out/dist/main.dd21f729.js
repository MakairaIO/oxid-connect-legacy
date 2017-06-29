'use strict';

var classFilterList = 'makaira-filter__list';
var classFilterListExpanded = 'makaira-filter__list--expanded';
var classFilterItemHidden = 'makaira-filter__item--hidden';
var classListExpandButton = 'makaira-filter__button--expand';
var classListCollapseButton = 'makaira-filter__button--collapse';

var expandList = function expandList(list, buttonItem) {
  list.classList.add(classFilterListExpanded);
  buttonItem.classList.add(classFilterItemHidden);
};

var collapseList = function collapseList(list) {
  var listExpandButton = list.querySelector('.' + classListExpandButton);
  var listExpandButtonItem = listExpandButton.parentNode;

  list.classList.remove(classFilterListExpanded);
  listExpandButtonItem.classList.remove(classFilterItemHidden);
};

var initHandlers = function initHandlers() {
  var filterLists = document.querySelectorAll('.' + classFilterList);

  var addExpandButtonHandler = function addExpandButtonHandler(list) {
    // delegate button-click to filter-list
    list.addEventListener('click', function (event) {
      var target = event.target;
      if (target.classList.contains(classListExpandButton)) {
        expandList(list, target.parentNode);
      }
    });
  };

  var addCollapseButtonHandler = function addCollapseButtonHandler(list) {
    // delegate button-click to filter-list
    list.addEventListener('click', function (event) {
      var target = event.target;
      if (target.classList.contains(classListCollapseButton)) {
        collapseList(list);
      }
    });
  };

  filterLists.forEach(function (list) {
    addExpandButtonHandler(list);
    addCollapseButtonHandler(list);
  });
};

initHandlers();

