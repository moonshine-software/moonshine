import { defineStore } from 'pinia'
import { IView, View, IViewComponent, ViewComponent } from '../entites/view'
import { TableColumn } from '@/js/entites/table/column'
import type { PropType } from 'vue'

export const useViewStore = defineStore('view', {
    state: () => ({
        class: {},
        endpoint: '',
        uri: '',
        component: '',
        components: [],
    }),
    actions: {
        setView(view: View) {
            const { components, ...other } = view
            this.class = new View(view)
            this.components = []
            components?.forEach((component) =>
                this.components.push(new ViewComponent(component))
            )

            Object.assign(this.$state, other)
        },
        fullReset() {
            this.$reset()
        },
    },
})
