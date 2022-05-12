import Alpine from 'alpinejs'
import persist from '@alpinejs/persist'
import mask from '@alpinejs/mask'

window.Alpine = Alpine

Alpine.plugin(persist)
Alpine.plugin(mask)
Alpine.start()