import {Resource, IResource} from "./resource/base";
import { IView, View } from './view'

export interface IPage {
    title?: string
    resource: IResource,
    view: IView
}

export class Page implements IPage {
    title?: string
    resource: IResource
    view: IView

    constructor(page: IPage) {
        this.title = page.title
        this.resource = new Resource(page.resource)
        this.view = new View(page.view)
    }
}
