import { http } from './http'
import { PrimaryKey } from '../entites/primary_key'

export const getResource = async (resourceUriKey: string) => {
    return await http.get(`resource/${resourceUriKey}`)
}

export const getResourceEntity = async (
    resourceUriKey: string,
    resourceId: PrimaryKey,
    type?: string
) => {
    return await http.get(`resource/${resourceUriKey}/${resourceId}` + (type ? `/${type}` : ''))
}

export const createResource = async (resourceUriKey: string) => {
    return await http.get(`resource/${resourceUriKey}/create`)
}

export const deleteResourceEntity = async (
    resourceUriKey: string,
    resourceId: PrimaryKey
) => {
    return await http.delete(`resource/${resourceUriKey}/${resourceId}`)
}
