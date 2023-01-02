import {NavigationGuardNext, RouteLocationNormalized} from "vue-router";
import guest from "./guest";
import auth from "./auth";

export type RouteMiddleware = typeof guest | typeof auth

export type MoonshineMiddlewareParams = {
    next: NavigationGuardNext
    to?: RouteLocationNormalized
    from?: RouteLocationNormalized
}
