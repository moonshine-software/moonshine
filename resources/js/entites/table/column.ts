import { ClassSAssign } from '../../utilities/class_assign'
import { IField } from './../fields/base'
import { TableSortPayload } from '../../entites/table/base'

type SortDirection = TableSortPayload['direction']

export interface ITableColumn {
    key: string
    label: string
    sortable: boolean
    sortDirection: SortDirection
    visible?: boolean
}

export class TableColumn implements ITableColumn {
    key!: string
    label!: string
    sortDirection!: SortDirection
    sortable!: boolean
    visible: boolean = true

    constructor(column: ITableColumn) {
        new ClassSAssign(column, this).apply()
    }

    switchVisible() {
        //todo: Debounce
        this.visible = !this.visible
    }

    switchDirection() {
        this.sortDirection = this.sortDirection === 'ASC' ? 'DESC' : 'ASC'
    }

    static getColumnsFromFields(
        fields: IField[],
        sortable = false,
        sortDirection: SortDirection
    ): TableColumn[] {
        const columns: TableColumn[] = []
        fields.forEach((f) => {
            columns.push(
                new TableColumn({
                    key: f.key,
                    label: f.label ?? f.key,
                    sortable,
                    sortDirection,
                    visible: true,
                })
            )
        })

        return columns
    }
}
