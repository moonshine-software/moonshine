import { defineAsyncComponent } from 'vue'

export interface IViewComponent {
    uri: string
    component: string
    endpoint: string
    getComponent(): any
}

export class ViewComponent implements IViewComponent {
    uri: string
    component: string
    endpoint: string

    constructor(view: IViewComponent) {
        this.uri = view.uri
        this.endpoint = view.endpoint
        this.component = view.component
    }

    /**
     * Return async View component
     */
    getComponent() {
        return defineAsyncComponent({
            loader: () =>
                import(`../components/ViewComponents/${this.component}.vue`),
            onError: () =>
                console.warn(
                    `ViewComponent /components/ViewComponents/${this.component}.vue not found`
                ),
            delay: 200,
        })
    }
}

export interface IView {
    uri: string
    component: string
    endpoint: string
    components: IViewComponent[]

    getComponent(): any
}

export class View implements IView {
    uri: string
    component: string
    endpoint: string
    components: IViewComponent[]

    constructor(view: IView) {
        this.uri = view.uri
        this.endpoint = view.endpoint
        this.component = view.component
        this.components = view.components
    }

    /**
     * Return async View component
     */
    getComponent() {
        return defineAsyncComponent({
            loader: () => import(`../views/${this.component}.vue`),
            onError: () =>
                console.warn(`View /views/${this.component}.vue not found`),
            delay: 200,
        })
    }
}
