import type { InputHTMLAttributes } from 'vue'
import type { NavigationGuardNext, RouteLocationNormalized } from 'vue-router'

declare type PrimaryKey = string | number | null | undefined

declare type MiddlewareParams = {
  next: NavigationGuardNext
  to?: RouteLocationNormalized
  from?: RouteLocationNormalized
}

declare interface AttributeBag extends InputHTMLAttributes {
  class?: any
}
