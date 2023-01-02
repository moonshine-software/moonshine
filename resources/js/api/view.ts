import { http } from './http'
import { PrimaryKey } from '../entites/primary_key'

export const fetchView = async (
    resourceUriKey: string,
    viewUriKey: string,
    id?: PrimaryKey
) => {
    return await http.get(
        `view/${resourceUriKey}/${viewUriKey}` + (id ? `/${id}` : '')
    )
}

export const fetchViewComponent = async (
    resourceUriKey: string,
    viewUriKey: string,
    viewComponentUriKey: string,
    id?: PrimaryKey
) => {
    return await http.get(
        `view-component/${resourceUriKey}/${viewUriKey}/${viewComponentUriKey}` +
            (id ? `/${id}` : '')
    )
}
