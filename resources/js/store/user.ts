import {defineStore} from "pinia";
import {PrimaryKey} from "../entites/primary_key";
import {appConfig} from '../config'
import {checkUser, login, logout, UserResponse} from "../api/user";
import router from "../router/routes";

type UserStore = {
    id?: PrimaryKey
    name?: string
    avatar?: string | null
    email?: string
    authenticated: boolean
    loaded: boolean
}

const userInitial = (): UserStore => {
    const storage = localStorage.getItem(`${appConfig.localStorageTokenPrefix}user`)
    return Object.assign(
        storage ? JSON.parse(storage) : {
            id: undefined,
            name: undefined,
            email: undefined,
            avatar: undefined,
            authenticated: false
        },
        {loaded: false})
}

export const useUserStore = defineStore('user', {
    state: userInitial,
    getters: {},
    actions: {
        async login(email: string, password: string) {
            const {data} = await login(email, password)
            this.setUser(data)
            this.setAuthenticated()
            this.saveState()
            this.setLoaded()
        },
        setAuthenticated() {
            this.authenticated = true
        },
        setLoaded() {
            this.loaded = true
        },
        setUser(user: UserResponse) {
            this.name = user.name
            this.avatar = user.avatar
            this.id = user.id
            this.email = user.email
        },
        check() {
            checkUser().then(({data}) => {
                this.setUser(data)
                this.setAuthenticated()
                this.saveState()
                this.setLoaded()
            })
        },
        saveState() {
            const {loaded, ...state} = this.$state
            localStorage.setItem(`${appConfig.localStorageTokenPrefix}user`, JSON.stringify(state))
        },
        resetUser() {
            logout().then(() => {
                localStorage.removeItem(`${appConfig.localStorageTokenPrefix}user`)
                this.$reset()
                router.push({name: 'login'})
            })
        }
    }
})
