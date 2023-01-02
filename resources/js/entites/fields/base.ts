import { AttributeBag } from '../attribute_bag'
import { defineAsyncComponent } from 'vue'
import { ClassSAssign } from '../../utilities/class_assign'
import FieldSkeleton from '../../components/UI/Skeleton/FieldSkeleton.vue'
import UndefinedFieldSkeleton from '../../components/UI/Skeleton/UndefinedFieldSkeleton.vue'
import { PrimaryKey } from '../primary_key'

export type FieldComponent =
    | 'TextField'
    | 'RangeField'
    | 'ColorField'
    | 'TextareaField'
    | 'IDField'
    | 'BooleanField'
    | 'CheckboxField'
    | 'SelectField'
    | 'EloquentSelectField'
    | 'CodeInput'
    | 'PasswordField'
    | 'PasswordRepeatInput'
    | string

export type ViewType = 'Index' | 'Show' | 'Form'

export interface IField {
    component: FieldComponent
    id: string
    name: string
    key: string
    value?: any
    _value?: any

    formatted_value?: any
    label: any
    hint?: string
    tippy?: string
    attributes?: AttributeBag
    liveValidation?: boolean
    validation?: string[]
    validationErrors?: string[]
    resource?: string
    sortable?: boolean
}

export class Field implements IField {
    _value: any

    formatted_value: any
    component: FieldComponent
    id!: string
    name!: string
    key!: string
    label: any
    hint?: string
    tippy?: string
    attributes?: AttributeBag
    liveValidation?: boolean
    validation?: string[]
    validationErrors?: string[]
    sortable?: boolean
    resource?: string

    constructor(field: IField) {
        const { value, component, ...other } = field

        if (field.component) {
            this.component = field.component
        } else {
            throw Error('The field component must be specified')
        }

        this._value = field.value

        new ClassSAssign(other, this).apply()
    }

    get value(): any {
        return this._value
    }

    set value(newValue: any) {
        //todo: validate here
        this._value = newValue
    }

    /**
     * Return async Field component
     */
    getComponent(viewType: ViewType) {
        return defineAsyncComponent({
            loader: () =>
                import(
                    `../../components/Field/${viewType}/${this.component}.vue`
                ),
            loadingComponent: FieldSkeleton,
            errorComponent: UndefinedFieldSkeleton,
            onError: () =>
                console.warn(
                    `Field ${viewType}/${this.component}.vue not found`
                ),
            delay: 200,
        })
    }

    /**
     * Return Index async Field component
     */
    getIndexComponent() {
        return this.getComponent('Index')
    }

    /**
     * Return View async Field component
     */
    getViewComponent() {
        return this.getComponent('Show')
    }

    /**
     * Return Edit async Field component
     */
    getEditComponent() {
        if (this.component === 'HasManyField') {
            return undefined
        }

        return this.getComponent('Form')
    }
}

type RelationFieldValue = {
    foreign_key: string
    key: string
    value: PrimaryKey
}

export class RelationField extends Field {
    _value: RelationFieldValue = {
        foreign_key: '',
        key: '',
        value: null,
    }

    resource?: string

    constructor(field: IField) {
        super(field)
    }
}
