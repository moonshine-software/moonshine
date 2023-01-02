import { PrimaryKey } from './../primary_key'
import { RouteLinkData } from './../router'
import { ClassSAssign } from '../../utilities/class_assign'
import { Policy } from './policies'

export interface IResource {
    id?: PrimaryKey
    endpoint?: string
    uri: string
    title?: string
    name: string
    policies?: Policy
    softDeletes?: boolean
}

export class Resource implements IResource {
    id?: PrimaryKey
    title?: string
    uri!: string
    endpoint?: string
    name!: string
    policies: Policy
    softDeletes?: boolean

    constructor(resource: IResource) {
        const { policies, ...other } = resource
        this.policies = new Policy(policies)

        new ClassSAssign(other, this).apply()
    }

    getRoute(
        routeName: RouteLinkData['name'],
        id?: PrimaryKey,
        uri?: string
    ): RouteLinkData {
        const resourceId = id ?? this.id
        const resourceUri = uri ?? this.uri

        return {
            name: routeName,
            params: {
                resourceName: this.name,
                resourceUriKey: resourceUri,
                resourceId: resourceId,
            },
        }
    }

    getShowRoute(resourceId?: PrimaryKey): RouteLinkData {
        return this.getRoute('show', resourceId)
    }

    getEditRoute(resourceId?: PrimaryKey): RouteLinkData {
        return this.getRoute('edit', resourceId)
    }

    getCreateRoute(resourceId?: PrimaryKey): RouteLinkData {
        return this.getRoute('create', resourceId)
    }

    get isLoaded(): boolean {
        return !!this.name
    }
}
