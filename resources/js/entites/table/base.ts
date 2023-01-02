import { PrimaryKey } from './../primary_key'
import { defineAsyncComponent } from 'vue'
import { AttributeBag } from './../attribute_bag'

import { TableColumn, ITableColumn } from './column'
import { TableRow, ITableRow } from './row'
import { Paginator } from './../paginator'

export type TableSortPayload = {
    column?: string
    direction?: 'ASC' | 'DESC'
}

export interface ITable {
    title?: string
    endpoint?: string
    attributes?: AttributeBag
    columns?: TableColumn[] | ITableColumn[]
    rows?: TableRow[] | ITableRow[]
    paginator?: Paginator
    selectedRows?: PrimaryKey[]
    allRowsSelected?: boolean
}

export class Table implements ITable {
    title?: string
    endpoint?: string
    attributes?: AttributeBag
    columns: TableColumn[] = []
    rows: TableRow[] = []
    paginator?: Paginator
    selectedRows: PrimaryKey[] = []
    allRowsSelected?: boolean

    constructor(table?: ITable) {
        if (table) {
            this.setTable(table)
        }
    }

    getComponent() {
        return defineAsyncComponent({
            loader: () =>
                import('../../components/ViewComponents/TableComponent.vue'),
            delay: 200,
        })
    }

    setTable(table: ITable) {
        const { rows, columns, ...other } = table

        this.columns = []
        if (!!columns) {
            columns?.forEach((col) => this.columns.push(new TableColumn(col)))
        } else if (rows) {
            this.columns = TableColumn.getColumnsFromFields(
                rows[0].fields,
                false,
                'ASC'
            )
        }

        this.rows = []
        rows?.forEach((row) => {
            this.rows.push(new TableRow(row!))
        })

        Object.assign(this, other)
        // todo: с ClassSAssign происходит потеря реактивности массивов в мультивложенности
        // todo: проверить этот класс везде
        // См Index table <!--   потеря реактивности пагинации происходит на уровне вложенности TheCard   -->
        // проверить везде
        // new ClassSAssign(other, this).apply()
    }

    getVisibleColumns(): TableColumn[] {
        return this.columns.filter((col) => col.visible)
    }

    getVisibleFields() {
        const cols = this.getVisibleColumns()
        return cols.map((col) => col.key)
    }

    setAllRowsSelected(value = true) {
        this.allRowsSelected = value
    }

    get isEmpty() {
        return this.rows.length === 0
    }

    get isSelectable() {
        return false
    }

    get vueTransitionKey() {
        return `index-table-${this.rows[0]?.getID() ?? 0}-${
            this.getVisibleColumns().length
        }`
    }
}
