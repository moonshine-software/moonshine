import './bootstrap'
import './layout'

import AlpineMS from 'alpinejs'
import {MoonShine} from './moonshine.js'
import persist from '@alpinejs/persist'
import mask from '@alpinejs/mask'
import morph from '@alpinejs/morph'

// Alpine components
import global from './Components/Global'
import formBuilder from './Components/FormBuilder'
import tableBuilder from './Components/TableBuilder'
import cardsBuilder from './Components/CardsBuilder'
import carousel from './Components/Carousel.js'
import dropdown from './Components/Dropdown'
import modal from './Components/Modal'
import offCanvas from './Components/OffCanvas'
import actionButton from './Components/ActionButton'
import select from './Components/Select'
import toasts from './Components/Toast'
import tooltip from './Components/Tooltip'
import navTooltip from './Components/NavTooltip'
import popovers from './Components/Popover'
import belongsToMany from './Components/BelongsToMany'
import range from './Components/Range'
import sortable from './Components/Sortable'
import queryTag from './Components/QueryTag'
import fragment from './Components/Fragment'
import tabs from './Components/Tabs.js'
import collapse from './Components/Collapse.js'
import bottomBarMenu from './Components/BottomBar.js'
import {validationInHiddenBlocks} from './Support/Forms.js'

window.MoonShine = new MoonShine()
document.dispatchEvent(new CustomEvent('moonshine:init'))

const alpineExists = !!window.Alpine

/** @type {import('@types/alpinejs').Alpine} */
const Alpine = alpineExists ? window.Alpine : AlpineMS

Alpine.data('formBuilder', formBuilder)
Alpine.data('global', global)
Alpine.data('tableBuilder', tableBuilder)
Alpine.data('cardsBuilder', cardsBuilder)
Alpine.data('carousel', carousel)
Alpine.data('queryTag', queryTag)
Alpine.data('actionButton', actionButton)
Alpine.data('dropdown', dropdown)
Alpine.data('modal', modal)
Alpine.data('sortable', sortable)
Alpine.data('offCanvas', offCanvas)
Alpine.data('select', select)
Alpine.data('toasts', toasts)
Alpine.data('tooltip', tooltip)
Alpine.data('navTooltip', navTooltip)
Alpine.data('popover', popovers)
Alpine.data('belongsToMany', belongsToMany)
Alpine.data('range', range)
Alpine.data('fragment', fragment)
Alpine.data('tabs', tabs)
Alpine.data('collapse', collapse)
Alpine.data('bottomBarMenu', bottomBarMenu)

window.Alpine = Alpine

document.addEventListener('alpine:init', () => {
  validationInHiddenBlocks()

  /* Dark mode */
  Alpine.store('darkMode', {
    init() {
      window.addEventListener('darkMode:toggle', () => this.toggle())
    },
    on: Alpine.$persist(window.matchMedia('(prefers-color-scheme: dark)').matches).as('darkMode'),
    toggle() {
      this.on = !this.on
    },
  })

  /* Navigation menues */
  Alpine.store('menu', {
    isSidebarOpen: false,
    isTopbarOpen: false,
    isMobileBarOpen: false,
    menuOverlayShown: false,

    toggleOverlay() {
      this.menuOverlayShown = !this.menuOverlayShown
    },

    toggleSidebar() {
      this.isSidebarOpen = !this.isSidebarOpen
      this.toggleOverlay()
    },

    toggleTopbar() {
      this.isTopbarOpen = !this.isTopbarOpen
    },

    toggleMobileBar() {
      this.isMobileBarOpen = !this.isMobileBarOpen
    },
  })
})

if (window.Livewire === undefined) {
  Alpine.plugin(persist)
  Alpine.plugin(mask)
  Alpine.plugin(morph)
}

if (!alpineExists) {
  Alpine.start()
}
