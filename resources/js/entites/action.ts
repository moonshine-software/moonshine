export interface Action {
    label: string
    name: string
    endpoint?: string | null
}

interface BulkAction extends Action {}
