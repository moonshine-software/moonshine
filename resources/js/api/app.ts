import { http } from './http'
import { AxiosResponse } from 'axios'
import { IMenu } from './../entites/menu'

export type InitialRequest = {
    app: { name: string },
    menu: IMenu,
    settings: Record<string, any>
}

export const initial = async () => {
    return await http.get<undefined, AxiosResponse<InitialRequest>>('/initial')
}
