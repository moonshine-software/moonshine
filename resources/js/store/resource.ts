import { defineStore } from 'pinia'
import { Policy } from '../entites/resource/policies'
import { PrimaryKey } from '../entites/primary_key'
import { deleteResourceEntity } from '../api/resource'

export interface IResource {
    id?: PrimaryKey
    uri?: string
    title?: string
    endpoint?: string
    policies?: Policy
}

export interface IResourceState {
    id?: PrimaryKey
    uri?: string
    title?: string
    endpoint?: string
    policies?: Policy
}

export const useResourceStore = defineStore('resource', {
    state: (): IResourceState => ({
        id: undefined,
        title: undefined,
        endpoint: undefined,
        uri: undefined,
        policies: undefined,
    }),
    getters: {
        key: (state) => state.uri,
        showRoute:
            (state) =>
            (
                id?: PrimaryKey
            ): {
                name: string
                params: { resourceId: PrimaryKey; resourceName?: string }
            } => ({
                name: 'show',
                params: {
                    resourceName: state.uri,
                    resourceId: id || state.id,
                },
            }),
        editRoute:
            (state) =>
            (
                id?: PrimaryKey
            ): {
                name: string
                params: { resourceId: PrimaryKey; resourceName?: string }
            } => ({
                name: 'edit',
                params: {
                    resourceName: state.uri,
                    resourceId: id || state.id,
                },
            }),
        createRoute:
            (state) =>
            (
                id?: PrimaryKey
            ): {
                name: string
                params: { resourceId: PrimaryKey; resourceName?: string }
            } => ({
                name: 'create',
                params: {
                    resourceName: state.uri,
                    resourceId: id || state.id,
                },
            }),
    },
    actions: {
        setResource(resource: IResource) {
            const { policies, ...other } = resource
            this.policies = new Policy(policies)

            Object.assign(this.$state, other)
        },
        async delete(resourceId: PrimaryKey) {
            await deleteResourceEntity(this.uri!, resourceId)
        },
    },
})
