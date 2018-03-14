import noUiSlider from 'nouislider'

const classFilterForm = 'makaira-form'
const classFilterList = 'makaira-filter__list'
const classFilterListExpanded = 'makaira-filter__list--expanded'
const classFilterItemHidden = 'makaira-filter__item--hidden'
const classListExpandButton = 'makaira-filter__button--expand'
const classListCollapseButton = 'makaira-filter__button--collapse'
const classFilterCheckbox = 'makaira-input--checkbox'
const classRangeSliderContainer = 'makaira-filter__slider-container'
const classRangeSlider = 'makaira-filter__range-slider'
const classRangeSliderMinValue = 'makaira-filter__value--min'
const classRangeSliderMaxValue = 'makaira-filter__value--max'
const classRangeSliderMinInput = 'makaira-filter__input--min'
const classRangeSliderMaxInput = 'makaira-filter__input--max'

const submitClosestForm = el => {
  const form = el.closest(`.${classFilterForm}`)
  form.submit()
}

const initRangeSliders = () => {
  const sliderContainers = document.querySelectorAll(`.${classRangeSliderContainer}`)

  sliderContainers.forEach(container => {
    const slider = container.querySelector(`.${classRangeSlider}`)
    const elementMinValue = container.querySelector(`.${classRangeSliderMinValue}`)
    const elementMaxValue = container.querySelector(`.${classRangeSliderMaxValue}`)
    const inputMinValue = container.querySelector(`.${classRangeSliderMinInput}`)
    const inputMaxValue = container.querySelector(`.${classRangeSliderMaxInput}`)
    const minValue = Math.floor(slider.dataset.min)
    const maxValue = Math.ceil(slider.dataset.max)
    const minStart = Math.floor(slider.dataset.left)
    const maxStart = Math.ceil(slider.dataset.right)

    // init slider
    noUiSlider.create(slider, {
      start: [minStart, maxStart],
      connect: true,
      range: {
        min: minValue,
        max: maxValue,
      },
    })

    // bind events
    slider.noUiSlider.on('update', (values, handle) => {
      /*
       * @param {Array} values array containing the current values of the slider
       * @param {Number} handle number indicating the handle that was used to update the slider
       */
      elementMinValue.innerHTML = Math.floor(values[0])
      elementMaxValue.innerHTML = Math.ceil(values[1])
      inputMinValue.value = Math.floor(values[0])
      inputMaxValue.value = Math.ceil(values[1])
    })

    slider.noUiSlider.on('change', (values, handle) => {
      submitClosestForm(slider)
    })
  })
}

const expandList = (list, expandButtonItem) => {
  const listCollapseButton = list.querySelector(`.${classListCollapseButton}`)
  const listCollapseButtonItem = listCollapseButton.parentNode

  list.classList.add(classFilterListExpanded)
  expandButtonItem.classList.add(classFilterItemHidden)
  listCollapseButtonItem.classList.remove(classFilterItemHidden)
}

const collapseList = (list, collapseButtonItem) => {
  const listExpandButton = list.querySelector(`.${classListExpandButton}`)
  const listExpandButtonItem = listExpandButton.parentNode

  list.classList.remove(classFilterListExpanded)
  collapseButtonItem.classList.add(classFilterItemHidden)
  listExpandButtonItem.classList.remove(classFilterItemHidden)
}

const resetSingleFilterValues = el => {
  const filterName = el.getAttribute('name')
  const otherInputs = Array.from(document.querySelectorAll(`[name="${filterName}"]`)).filter(
    input => input != el
  )

  otherInputs.forEach(input => input.removeAttribute('checked'))
}

const initHandlers = () => {
  const filterLists = document.querySelectorAll(`.${classFilterList}`)

  const addExpandButtonHandler = list => {
    // delegate button-click to filter-list
    list.addEventListener('click', event => {
      const target = event.target

      if (target.classList.contains(classListExpandButton)) {
        expandList(list, target.parentNode)
      }
    })
  }

  const addCollapseButtonHandler = list => {
    // delegate button-click to filter-list
    list.addEventListener('click', event => {
      const target = event.target

      if (target.classList.contains(classListCollapseButton)) {
        collapseList(list, target.parentNode)
      }
    })
  }

  const addCheckboxHandler = list => {
    // delegate change-event to filter-list
    list.addEventListener('click', event => {
      const target = event.target

      if (target.classList.contains(classFilterCheckbox)) {
        if (target.closest('.makaira-filter__filter--list')) {
          resetSingleFilterValues(target)
        }
        submitClosestForm(target)
      }
    })
  }

  filterLists.forEach(list => {
    addExpandButtonHandler(list)
    addCollapseButtonHandler(list)
    addCheckboxHandler(list)
  })
}

initRangeSliders()
initHandlers()
