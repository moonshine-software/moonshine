import { defineStore } from 'pinia'

import { AttributeBag } from '../entites/attribute_bag'
import { TableColumn } from '../entites/table/column'
import { TableRow } from '../entites/table/row'
import { Paginator } from '../entites/paginator'
import { PrimaryKey } from '../entites/primary_key'
import { ITable, TableSortPayload } from '../entites/table/base'
import { fetchViewComponent } from '../api/view'

export type TableComponent = {
    title?: string
    endpoint: string
    attributes?: AttributeBag
    columns: TableColumn[]
    rows: TableRow[]
    paginator?: Paginator
    selectedRows: PrimaryKey[]
    allRowsSelected?: boolean
    loaded: boolean
    fetching: boolean
    search: string
    sort: TableSortPayload
}

const storeDefinition = (key: string) =>
    defineStore(key, {
        state: (): TableComponent => ({
            title: undefined,
            endpoint: '',
            attributes: {},
            columns: [],
            rows: [],
            paginator: undefined,
            selectedRows: [],
            allRowsSelected: false,
            loaded: false,
            fetching: false,
            search: '',
            sort: {
                column: undefined,
                direction: undefined,
            },
        }),
        getters: {
            visibleColumns: (state) =>
                state.columns.filter((col) => col.visible),
            visibleFields() {
                return this.visibleColumns.map((col) => col.key)
            },
            isSelectable: () => false,
            currentPage: (state) => state.paginator?.current_page || 1,
        },
        actions: {
            setTable(table: ITable) {
                const { rows, columns, ...other } = table
                this.columns = []
                columns?.forEach((col) =>
                    this.columns.push(new TableColumn(col))
                )

                this.rows = []
                rows?.forEach((row) => {
                    this.rows.push(new TableRow(row))
                })

                Object.assign(this.$state, other)
                this.loaded = true
            },
            switchSort(columnKey: string) {
                const col = this.columns.find((c) => c.key === columnKey)
                if (col) {
                    this.sort.direction =
                        col.sortDirection === 'ASC' ? 'DESC' : 'ASC'
                    this.sort.column = columnKey
                }
            },
            fetch(
                resourceKey: string,
                viewKey: string,
                viewComponentKey: string
            ) {
                this.fetching = true
                fetchViewComponent(resourceKey, viewKey, viewComponentKey).then(
                    (r) => {
                        this.setTable(r.data)
                        this.fetching = false
                    }
                )
            },
            reset() {
                this.$reset()
            },
        },
    })

export type TableStore = ReturnType<typeof storeDefinition>

const tableStores: Record<string, TableStore> = {}

export function useTableStore(key: string) {
    key = `index-table-${key}`

    if (!tableStores[key]) {
        tableStores[key] = storeDefinition(key)
    }

    return storeDefinition(key)()
}
