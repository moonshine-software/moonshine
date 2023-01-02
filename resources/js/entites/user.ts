import {PrimaryKey} from "./primary_key";
import {ClassSAssign} from "../utilities/class_assign";

export interface IUser {
    avatar?: string|null
    name?: string|null
    id: PrimaryKey
}

export class User implements IUser {
    avatar?: string | null;
    id: PrimaryKey;
    name?: string | null;

    constructor(user: IUser) {
        Object.assign(this, user)
    }
}
