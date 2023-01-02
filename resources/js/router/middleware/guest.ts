import {MoonshineMiddlewareParams} from "./index";
import {useUserStore} from "../../store/user";

export default function guest({next}: MoonshineMiddlewareParams) {
    if(useUserStore().authenticated){
        return next({name: 'dashboard'})
    }

    return next()
}
