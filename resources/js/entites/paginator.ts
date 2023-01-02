interface PaginatorLink {
    number: number | null,
    label: string,
    active: boolean
}

interface Paginator {
    current_page: number,
    from: number,
    last_page: number,
    per_page: number,
    to: number,
    total: number
}

interface PageChangeEventData {
    number: number
}

interface PaginationShownInfoI18nData extends Record<string, unknown>{
    per_page: number,
    total: number,
    from?: number,
    to?: number
}



export type {
    Paginator,
    PaginatorLink,
    PageChangeEventData,
    PaginationShownInfoI18nData
}
