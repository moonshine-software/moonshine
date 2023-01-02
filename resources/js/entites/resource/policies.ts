export interface IPolicy {
    delete: boolean
    edit: boolean
    forceDelete: boolean
    restore: boolean
    show: boolean
    viewAny: boolean
}

export class Policy implements IPolicy {
    delete: boolean = false
    edit: boolean = false
    forceDelete: boolean = false
    restore: boolean = false
    show: boolean = false
    viewAny: boolean = false

    constructor(policies?: IPolicy) {
        Object.assign(this, policies)
    }
}
