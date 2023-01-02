import { PrimaryKey } from './primary_key'
import { useRoute } from 'vue-router'

export interface RouteLinkData {
    name: 'dashboard' | 'index' | 'show' | 'edit' | 'create'
    params: {
        resourceUriKey: string
        resourceName?: string
        resourceId?: PrimaryKey
        relationName?: string
        relationId?: PrimaryKey
    }
}

export const useResourceRouteInfo = (): RouteLinkData['params'] => {
    const r = useRoute()
    return { ...r.params, resourceUriKey: String(r.params.resourceName) }
}
