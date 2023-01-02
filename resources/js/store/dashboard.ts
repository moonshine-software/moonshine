import {defineStore} from "pinia";
import {fetchDashboard} from "../api/dashboard";

export const useDashboardStore = defineStore('dashboard', {
    state: () => ({
        blocks: {},
        loaded: false
    }),
    getters: {
    },
    actions: {
        async fetch() {
            fetchDashboard().then(resp => {
                const {data} = resp
                this.blocks = data
                this.loaded = true
            })
        }
    }
})
