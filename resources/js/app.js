import './bootstrap'
import './layout'

import AlpineMS from 'alpinejs'
import {MoonShine} from './moonshine.js'
import persist from '@alpinejs/persist'
import mask from '@alpinejs/mask'

// Alpine components
import formBuilder from './alpine/formBuilder'
import tableBuilder from './alpine/tableBuilder'
import cardsBuilder from './alpine/cardsBuilder'
import dropdown from './alpine/dropdown'
import modal from './alpine/modal'
import offcanvas from './alpine/offcanvas'
import actionButton from './alpine/actionButton'
import select from './alpine/select'
import toasts from './alpine/toasts'
import tooltip from './alpine/tooltip'
import navTooltip from './alpine/navTooltip'
import popovers from './alpine/popovers'
import pivot from './alpine/pivot'
import asyncSearch from './alpine/asyncSearch'
import interactsWithAsync from './alpine/interactsWithAsync'
import tinymce from './alpine/tinymce'
import range from './alpine/range'
import code from './alpine/code'
import tree from './alpine/tree'
import charts from './alpine/charts'
import sortable from './alpine/sortable'
import asyncLink from './alpine/asyncLink'
import numberUpDown from './alpine/numberUpDown'
import fragment from './alpine/fragment'
import globalSearch from './alpine/globalSearch'

window.MoonShine = new MoonShine()
document.dispatchEvent(new CustomEvent('moonshine:init'))

const alpineExists = !!window.Alpine

/** @type {import('@types/alpinejs').Alpine} */
const Alpine = alpineExists ? window.Alpine : AlpineMS

Alpine.data('formBuilder', formBuilder)
Alpine.data('tableBuilder', tableBuilder)
Alpine.data('cardsBuilder', cardsBuilder)
Alpine.data('asyncLink', asyncLink)
Alpine.data('actionButton', actionButton)
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
Alpine.data('interactsWithAsync', interactsWithAsync)
Alpine.data('tinymce', tinymce)
Alpine.data('range', range)
Alpine.data('code', code)
Alpine.data('tree', tree)
Alpine.data('charts', charts)
Alpine.data('numberUpDown', numberUpDown)
Alpine.data('fragment', fragment)
Alpine.data('globalSearch', globalSearch)

window.Alpine = Alpine

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

if (window.Livewire === undefined) {
  Alpine.plugin(persist)
  Alpine.plugin(mask)
}

if (!alpineExists) {
  Alpine.start()
}
