import { defineStore } from 'pinia'
import { useResourceStore } from './resource'
import { useViewStore } from './view'
import { fetchView, fetchViewComponent } from './../api/view'
import { getResource, getResourceEntity } from './../api/resource'
import { PrimaryKey } from '../entites/primary_key'

export type Page = {
    resource?: string,
    view?: string
}

export const usePageStore = defineStore('page', {
    state: () => ({
        loaded: false,
        resource: useResourceStore(),
        view: useViewStore(),
    }),
    actions: {
        fetchResource(resourceUriKey: string) {
            this.fullReset()

            getResource(resourceUriKey).then((resp) => {
                const { data } = resp
                this.resource.setResource(data.resource),
                this.view.setView(data.view)
                this.loaded = true
            })
        },
        fetchResourceEntity(resourceUriKey: string, resourceId: PrimaryKey, type?: string) {
            this.fullReset()

            getResourceEntity(resourceUriKey, resourceId, type).then((resp) => {
                const { data } = resp
                this.resource.setResource(data.resource),
                    this.view.setView(data.view)
                this.loaded = true
            })
        },
        fullReset() {
            this.resource.$reset()
            this.view.$reset()
            this.$reset()
        },
    },
})
