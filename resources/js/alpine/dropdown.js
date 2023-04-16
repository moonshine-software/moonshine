/* Popper Dropdown */

import { createPopper } from '@popperjs/core'

export default () => ({
  open: false,
  popperInstance: null,
  dropdownBtn: null,
  dropdownBody: null,
  visibilityClasses: ['pointer-events-auto', 'visible', 'opacity-100'],

  init() {
    this.dropdownBtn = this.$refs.dropdownEl.querySelector(".dropdown-btn")
    this.dropdownBody = this.$refs.dropdownEl.querySelector(".dropdown-body")

    const dropdownPlacement = this.$refs.dropdownEl.dataset.dropdownPlacement;

    this.popperInstance = createPopper(this.dropdownBtn, this.dropdownBody, {
      placement: dropdownPlacement ? dropdownPlacement : "auto",
      modifiers: [
        {
          name: "offset",
          options: {
            offset: [0, 6],
          },
        },
        {
          name: "flip",
          options: {
            allowedAutoPlacements: ["right", "left", "top", "bottom"],
            rootBoundary: "viewport",
          },
        },
      ],
    })
  },

  toggleDropdown() {
    this.open = !this.open
    this.visibilityClasses.forEach(cssClass => this.dropdownBody.classList.toggle(cssClass))
    this.popperInstance.update()
  },

  closeDropdown() {
    this.open = false
    this.visibilityClasses.forEach(cssClass => this.dropdownBody.classList.remove(cssClass))
  }

})