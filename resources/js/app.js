import './bootstrap'
import './layout'

import AlpineMS from 'alpinejs'
import {MoonShine} from './moonshine.js'
import persist from '@alpinejs/persist'
import mask from '@alpinejs/mask'

// Alpine components
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
import pivot from './Components/Pivot'
import asyncSearch from './Components/AsyncSearch'
import interactsWithAsync from './Components/InteractsWithAsync'
import tinymce from './Components/TinyMce'
import range from './Components/Range'
import code from './Components/Code'
import tree from './Components/Tree'
import charts from './Components/Charts'
import sortable from './Components/Sortable'
import asyncLink from './Components/AsyncLink'
import fragment from './Components/Fragment'
import globalSearch from './Components/GlobalSearch'
import tabs from './Components/Tabs.js'
import collapse from './Components/Collapse.js'
import easyMde from './Components/EasyMde'
import {validationInHiddenBlocks} from './Support/Forms.js'

window.MoonShine = new MoonShine()
document.dispatchEvent(new CustomEvent('moonshine:init'))

const alpineExists = !!window.Alpine

/** @type {import('@types/alpinejs').Alpine} */
const Alpine = alpineExists ? window.Alpine : AlpineMS

Alpine.data('formBuilder', formBuilder)
Alpine.data('tableBuilder', tableBuilder)
Alpine.data('cardsBuilder', cardsBuilder)
Alpine.data('carousel', carousel)
Alpine.data('asyncLink', asyncLink)
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
Alpine.data('pivot', pivot)
Alpine.data('asyncSearch', asyncSearch)
Alpine.data('interactsWithAsync', interactsWithAsync)
Alpine.data('tinymce', tinymce)
Alpine.data('range', range)
Alpine.data('code', code)
Alpine.data('tree', tree)
Alpine.data('charts', charts)
Alpine.data('fragment', fragment)
Alpine.data('globalSearch', globalSearch)
Alpine.data('tabs', tabs)
Alpine.data('easyMde', easyMde)
Alpine.data('collapse', collapse)

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
})

if (window.Livewire === undefined) {
  Alpine.plugin(persist)
  Alpine.plugin(mask)
}

if (!alpineExists) {
  Alpine.start()
}
