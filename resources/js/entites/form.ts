import { FormHTMLAttributes } from 'vue'
import { Field, IField } from './fields/base'
import { ClassSAssign } from '../utilities/class_assign'
import { Resource } from '@/js/entites/resource/base'

export interface IForm {
    attributes?: FormHTMLAttributes
    title?: string
    fields: Field[] | IField[]
}

export class ResourceForm extends Resource {
    form: Form

    constructor(resource: IResource) {
        const {form, ...resourceWithoutTable} = resource
        super(resourceWithoutTable);

        this.form = new Form(form)
    }

    fetchForm(){
        this.form.fetch(this.name, this.id)
    }
}

export class Form implements IForm {
    attributes?: FormHTMLAttributes
    title?: string
    fields: Field[] = []

    constructor(form?: IForm) {
        if (form) {
            this.setForm(form)
        }
    }

    getNonRelationFields(): Field[] {
        return this.fields?.filter((f) => !f.resource)
    }

    getRelationFields(): Field[] {
        return this.fields?.filter((f) => !!f.resource)
    }

    setForm(form: IForm) {
        const { fields, ...other } = form
        fields?.forEach((field) => this.fields.push(new Field(field)))
        new ClassSAssign(other, this).apply()
    }

    get hasFields() {
        return this.fields.length
    }
}
