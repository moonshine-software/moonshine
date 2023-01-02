import {MoonshineMiddlewareParams} from "./index";
import {useUserStore} from "../../store/user";

export default function auth({next}: MoonshineMiddlewareParams) {
    const user = useUserStore()

    if(!user.authenticated){
        return next({name: 'login'})
    }

    return next()
}
