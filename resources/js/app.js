import './bootstrap'
import './layout'

import Alpine from 'alpinejs'
import persist from '@alpinejs/persist'
import mask from '@alpinejs/mask'

// Alpine components
import formBuilder from './alpine/formBuilder'
import tableBuilder from './alpine/tableBuilder'
import dropdown from './alpine/dropdown'
import modal from './alpine/modal'
import offcanvas from './alpine/offcanvas'
import select from './alpine/select'
import toasts from './alpine/toasts'
import tooltip from './alpine/tooltip'
import navTooltip from './alpine/navTooltip'
import popovers from './alpine/popovers'
import pivot from './alpine/pivot'
import asyncSearch from './alpine/asyncSearch'
import asyncData from './alpine/asyncData'
import tinymce from './alpine/tinymce'
import range from './alpine/range'
import code from './alpine/code'
import tree from './alpine/tree'
import charts from './alpine/charts'
import sortable from './alpine/sortable'
import link from "./alpine/link";

Alpine.data('formBuilder', formBuilder)
Alpine.data('tableBuilder', tableBuilder)
Alpine.data('link', link)
Alpine.data('dropdown', dropdown)
Alpine.data('modal', modal)
Alpine.data('sortable', sortable)
Alpine.data('offcanvas', offcanvas)
Alpine.data('select', select)
Alpine.data('toasts', toasts)
Alpine.data('tooltip', tooltip)
Alpine.data('navTooltip', navTooltip)
Alpine.data('popover', popovers)
Alpine.data('pivot', pivot)
Alpine.data('asyncSearch', asyncSearch)
Alpine.data('asyncData', asyncData)
Alpine.data('tinymce', tinymce)
Alpine.data('range', range)
Alpine.data('code', code)
Alpine.data('tree', tree)
Alpine.data('charts', charts)

window.Alpine = Alpine

/* Alpine.js */
document.addEventListener('alpine:init', () => {
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
})

Alpine.plugin(persist)
Alpine.plugin(mask)
Alpine.start()
