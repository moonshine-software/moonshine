import { ClassSAssign } from '../utilities/class_assign'
import { slug } from '../utilities/string_helpers'

export interface IMenuItem {
    icon?: null | string
    title: null | string
    url?: null | string
    target?: null | string
    resource?: string
    notification?: string | number
    items?: IMenuItem[]
}

const collapsedLSKeyPrefix = 'ms-m-collapsed-'

export class MenuItem implements IMenuItem {
    collapsed?: boolean
    icon?: string | null
    items: MenuItem[] = []
    title: string | null = null
    resource?: string
    url?: string | null
    notification?: string | number
    target?: null | string

    constructor(item: IMenuItem) {
        const { items, ...other } = item
        new ClassSAssign(other, this).apply()
        items?.forEach((item) => this.items.push(new MenuItem(item)))
        if (this.isGroup) {
            this.collapsed = !localStorage.getItem(this.id)
        } else if (this.isUrl && !this.icon) {
            this.icon = 'external-link'
        }
    }

    get isGroup() {
        return this.items?.length && this.items.constructor === Array
    }

    get isUrl() {
        return !!this.url
    }

    get isResourceRoute() {
        return this.resource?.length
    }

    get id() {
        const text = this.title ?? this.resource ?? ''
        return collapsedLSKeyPrefix + slug(text)
    }

    get routerTo() {
        return this.isResourceRoute
            ? {
                  name: 'index',
                  params: {
                      resourceName: this.resource,
                  },
              }
            : undefined
    }

    switchCollapse() {
        if (this.isGroup) {
            this.collapsed = !this.collapsed
            if (!this.collapsed) localStorage.setItem(this.id, '1')
            else localStorage.removeItem(this.id)
        }
    }
}

export interface IMenu {
    items: IMenuItem[]
}

export class Menu implements IMenu {
    items: MenuItem[] = []

    constructor(menu?: IMenu) {
        if (menu?.items) {
            this.setMenu(menu)
        }
    }

    setMenu(menu: IMenu) {
        menu.items.forEach((item) =>
            this.items.push(new MenuItem(item))
        )
    }

    get navItems() {
        return this.items.filter(
            (item) => !item.isGroup && item.isResourceRoute && item.icon
        )
    }
}
