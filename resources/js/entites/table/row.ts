import { Field, IField } from './../fields/base'
import { PrimaryKey } from './../primary_key'
import { Policy } from './../resource/policies'

export interface ITableRow {
    fields: Field[] | IField[]
    selected?: boolean
    selectable?: boolean
    policies: Policy
    id: PrimaryKey
}

export class TableRow implements ITableRow {
    fields: Field[] = []
    selected: boolean = false
    selectable: boolean = true
    policies: Policy
    id: PrimaryKey

    constructor(row: ITableRow) {
        const { fields, selectable, id, policies } = row
        this.id = id
        fields?.forEach((field) => this.fields.push(new Field(field)))

        this.selectable = selectable ?? false
        this.policies = policies
    }

    setSelected(value = true) {
        this.selected = value
    }

    isSelected(): boolean {
        return this.selected
    }

    getVisibleFields(visibleFields: string[]) {
        return this.fields.filter((field) => visibleFields.includes(field.key))
    }

    getID(): PrimaryKey {
        return `row-${this.id}`
    }
}
