import {defineStore} from "pinia";
import {appLocalStorageKey, menuToNavBreakpoint} from '../config'
import {Menu} from "../entites/menu";
import {initial, InitialRequest} from "../api/app";
import {themeController} from "../utilities/theme_controller";

type AppStore = {
    app?: {
        name: string
    },
    menu: Menu,
    settings?: Record<string, any>,
    theme: {
        logo?: string|null,
        mode: 'light' | 'system' | 'dark',
        isSmallDevice: boolean,
        showMenu: boolean,
    }
}

const initAppFromStorage = (): AppStore => {
    const
        app = localStorage.getItem(appLocalStorageKey),
        isSmallDevice = window.innerWidth <= menuToNavBreakpoint

    //Setup for mobile
    if (isSmallDevice) {
        document.body.style.height = window.innerHeight + 'px'
    }

    if (!app) return {
        app: {
            name: ''
        },
        theme: {
            mode: 'system',
            isSmallDevice,
            logo: null,
            showMenu: false
        },
        menu: new Menu()
    }

    const parsedApp = JSON.parse(app)
    document.title = parsedApp.app.name
    themeController.setupTheme()

    return {
        app: parsedApp.app,
        menu: new Menu(parsedApp.menu),
        settings: parsedApp.settings,
        theme: Object.assign(parsedApp.theme ?? {}, {isSmallDevice, showMenu: false})
    }
}

export const useAppStore = defineStore('app', {
    state: (): AppStore => initAppFromStorage(),
    getters: {
        loaded: (state): boolean => !!state.app?.name,
        isDarkModeTheme: (state): boolean => state.theme.mode === 'dark',
        isSmallDevice: (state): boolean => state.theme.isSmallDevice
    },
    actions: {
        async initialSetup() {
            const {data} = await initial()
            this.saveToStorage(data)
            this.$state = initAppFromStorage()

            window.addEventListener('resize',
                () => this.theme.isSmallDevice = window.innerWidth <= menuToNavBreakpoint,
                true)
        },
        saveToStorage(data?: InitialRequest) {
            localStorage.setItem(appLocalStorageKey, JSON.stringify(data || this.$state))
        },
        switchTheme() {
            themeController.switch()
            this.theme.mode = themeController.getCurrent()
            this.saveToStorage()
        },
        switchMenuShow(){
            this.theme.showMenu = !this.theme.showMenu
        },
        hideMenu(){
            this.theme.showMenu = false
        }
    }
})
