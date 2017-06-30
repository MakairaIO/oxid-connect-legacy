import noUiSlider from 'nouislider';

const classFilterList = 'makaira-filter__list';
const classFilterListExpanded = 'makaira-filter__list--expanded';
const classFilterItemHidden = 'makaira-filter__item--hidden';
const classListExpandButton = 'makaira-filter__button--expand';
const classListCollapseButton = 'makaira-filter__button--collapse';
const classRangeSliderContainer = 'makaira-filter__slider-container';
const classRangeSlider = 'makaira-filter__range-slider';
const classRangeSliderMinValue = 'makaira-filter__value--min';
const classRangeSliderMaxValue = 'makaira-filter__value--max';

const initRangeSliders = () => {
  const sliderContainers = document.querySelectorAll(`.${classRangeSliderContainer}`);

  sliderContainers.forEach((container) => {
    const slider = container.querySelector(`.${classRangeSlider}`);
    const elementMinValue = container.querySelector(`.${classRangeSliderMinValue}`)
    const elementMaxValue = container.querySelector(`.${classRangeSliderMaxValue}`)
    const minValue = parseInt(slider.dataset.min);
    const maxValue = parseInt(slider.dataset.max);

    // init slider
    noUiSlider.create(slider, {
      start: [minValue, maxValue],
      connect: true,
      range: {
        'min': minValue,
        'max': maxValue
      }
    });

    // bind events
    slider.noUiSlider.on('update', (values, handle) => {
      /*
       * @param {Array} values array containing the current values of the slider
       * @param {Number} handle number indicating the handle that was used to update the slider
       */
      elementMinValue.innerHTML = Math.floor(values[0]);
      elementMaxValue.innerHTML = Math.ceil(values[1]);
    });
  });

};

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

initRangeSliders();
initHandlers();
