/* Popper Dropdown */

import {createPopper} from '@popperjs/core'

export default () => ({
  open: false,
  popperInstance: null,
  dropdownBtn: null,
  dropdownBody: null,
  dropdownSearch: null,
  dropdownItems: null,
  visibilityClasses: ['pointer-events-auto', 'visible', 'opacity-100'],

  init() {
    this.dropdownBtn = this.$root.querySelector('.dropdown-btn')
    this.dropdownBody = this.$root.querySelector('.dropdown-body')

    if(this.$root.dataset.searchable) {
      this.dropdownItems = this.$el.querySelectorAll('.dropdown-menu-item');
      this.$watch('dropdownSearch', value => this.search(value))
    }

    const dropdownPlacement = this.$root.dataset.dropdownPlacement

    this.popperInstance = createPopper(this.dropdownBtn, this.dropdownBody, {
      placement: dropdownPlacement ? dropdownPlacement : 'auto',
      modifiers: [
        {
          name: 'offset',
          options: {
            offset: [0, 6],
          },
        },
        {
          name: 'flip',
          options: {
            allowedAutoPlacements: ['right', 'left', 'top', 'bottom'],
            rootBoundary: 'viewport',
          },
        },
      ],
    })
  },

  search(searchVal){
    if(!searchVal || typeof searchVal !== 'string'){
      this.dropdownItems.forEach((item) => item.hidden = false)
      return;
    }


    const search = searchVal.toLowerCase();
    this.dropdownItems.forEach(
      (item) => {
        item.innerText.toLowerCase().includes(search) ? item.hidden = false : item.hidden = true
      }
    )
  },

  toggleDropdown() {
    this.open = !this.open
    this.visibilityClasses.forEach(cssClass => this.dropdownBody.classList.toggle(cssClass))
    this.popperInstance.update()
  },

  closeDropdown() {
    this.open = false
    this.visibilityClasses.forEach(cssClass => this.dropdownBody.classList.remove(cssClass))
  },
})
