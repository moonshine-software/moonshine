import { defineStore } from 'pinia'
import { FormHTMLAttributes } from 'vue'
import { Field } from '../entites/fields/base'
import { IForm } from '../entites/form'
import { fetchViewComponent } from '../api/view'
import { PrimaryKey } from '../entites/primary_key'

export interface IForm {
    attributes?: FormHTMLAttributes
    fields: Field[]
    loaded: boolean
    fetching: boolean
}

const storeDefinition = (key: string) =>
    defineStore(key, {
        state: (): IForm => ({
            attributes: {},
            fields: [],
            loaded: false,
            fetching: false,
        }),
        getters: {
            nonRelationFields(state) {
                return state.fields.filter((f) => !f.resource)
            },
            relationFields(state) {
                return state.fields.filter((f) => !!f.resource)
            },
            getAjaxForm() {
                const form: Record<string, any> = {}

                this.fields.forEach((field) => (form[field.key] = field.value))
                return form
            },
        },
        actions: {
            setForm(form: IForm) {
                const { fields, ...other } = form

                this.fields = []
                fields?.forEach((field) => this.fields.push(new Field(field)))
                Object.assign(this.$state, other)
            },
            fetch(resourceKey: string, viewKey: string, viewComponentKey: string, resourceId: PrimaryKey) {
                this.fetching = true
                fetchViewComponent(resourceKey, viewKey, viewComponentKey, resourceId).then((r) => {
                    this.setForm(r.data)
                    this.fetching = false
                })
            },
            reset() {
                this.$reset()
            },
        },
    })

export type FormStore = ReturnType<typeof storeDefinition>

const formStores: Record<string, FormStore> = {}

export function useFormStore(key: string) {
    key = `form-${key}`
    if (!formStores[key]) formStores[key] = storeDefinition(key)

    return storeDefinition(key)()
}
