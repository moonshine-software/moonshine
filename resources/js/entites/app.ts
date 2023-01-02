import { IUser } from './user'
import { IMenu } from './menu'

export interface MoonshineAppInterface {
    settings: Object
    user: IUser
    menu: IMenu
}
