import './bootstrap'
import './layout'

import AlpineMS from 'alpinejs'
import {MoonShine} from './moonshine.js'
import persist from '@alpinejs/persist'
import mask from '@alpinejs/mask'
import {validationInHiddenBlocks} from './alpine/formFunctions'

// Alpine components
import actionButton from './alpine/actionButton'
import asyncLink from './alpine/asyncLink'
import asyncSearch from './alpine/asyncSearch'
import cardsBuilder from './alpine/cardsBuilder'
import carousel from './alpine/carousel.js'
import charts from './alpine/charts'
import code from './alpine/code'
import collapse from './alpine/collapse'
import dropdown from './alpine/dropdown'
import easyMde from './alpine/easyMde'
import formBuilder from './alpine/formBuilder'
import fragment from './alpine/fragment'
import globalSearch from './alpine/globalSearch'
import interactsWithAsync from './alpine/interactsWithAsync'
import modal from './alpine/modal'
import navTooltip from './alpine/navTooltip'
import offcanvas from './alpine/offcanvas'
import pivot from './alpine/pivot'
import popovers from './alpine/popovers'
import range from './alpine/range'
import select from './alpine/select'
import sortable from './alpine/sortable'
import tableBuilder from './alpine/tableBuilder'
import tabs from './alpine/tabs.js'
import tinymce from './alpine/tinymce'
import toasts from './alpine/toasts'
import tooltip from './alpine/tooltip'
import tree from './alpine/tree'

window.MoonShine = new MoonShine()
document.dispatchEvent(new CustomEvent('moonshine:init'))

const alpineExists = !!window.Alpine

/** @type {import('@types/alpinejs').Alpine} */
const Alpine = alpineExists ? window.Alpine : AlpineMS

Alpine.data('actionButton', actionButton)
Alpine.data('asyncLink', asyncLink)
Alpine.data('asyncSearch', asyncSearch)
Alpine.data('cardsBuilder', cardsBuilder)
Alpine.data('carousel', carousel)
Alpine.data('charts', charts)
Alpine.data('code', code)
Alpine.data('collapse', collapse)
Alpine.data('dropdown', dropdown)
Alpine.data('easyMde', easyMde)
Alpine.data('formBuilder', formBuilder)
Alpine.data('fragment', fragment)
Alpine.data('globalSearch', globalSearch)
Alpine.data('interactsWithAsync', interactsWithAsync)
Alpine.data('modal', modal)
Alpine.data('navTooltip', navTooltip)
Alpine.data('offcanvas', offcanvas)
Alpine.data('pivot', pivot)
Alpine.data('popover', popovers)
Alpine.data('range', range)
Alpine.data('select', select)
Alpine.data('sortable', sortable)
Alpine.data('tableBuilder', tableBuilder)
Alpine.data('tabs', tabs)
Alpine.data('tinymce', tinymce)
Alpine.data('toasts', toasts)
Alpine.data('tooltip', tooltip)
Alpine.data('tree', tree)

window.Alpine = Alpine

document.addEventListener('alpine:init', () => {
  document.querySelectorAll('.remove-after-init').forEach(e => e.parentNode.removeChild(e))

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
})

if (window.Livewire === undefined) {
  Alpine.plugin(persist)
  Alpine.plugin(mask)
}

if (!alpineExists) {
  Alpine.start()
}
