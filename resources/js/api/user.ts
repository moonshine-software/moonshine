import { http } from './http'
import { AxiosResponse } from 'axios'
import { PrimaryKey } from '../entites/primary_key'

export type UserResponse = {
    id: PrimaryKey
    name: string
    avatar?: string | null
    email?: string
}

export type UserLoginRequest = {
    email: string
    password: string
}

export const login = async (email: string, password: string) => {
    await http.get('/sanctum/csrf-cookie', { baseURL: 'http://127.0.0.1:8000' })
    return await http.post<UserLoginRequest, AxiosResponse<UserResponse>>(
        '/authenticate',
        { email, password }
    )
}

export const checkUser = async () => {
    return await http.get<UserLoginRequest, AxiosResponse<UserResponse>>(
        '/authenticate'
    )
}

export const logout = async () => {
    return await http.delete('/logout')
}
