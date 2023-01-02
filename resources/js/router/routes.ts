// @ts-ignore
import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'
import Index from '../pages/Index.vue'
import Dashboard from '../pages/Dashboard.vue'
import Login from '../pages/Login.vue'
import Edit from '../pages/Edit.vue'
import Show from '../pages/Show.vue'
import Create from '../pages/Create.vue'
import Error404 from '../pages/404.vue'
import Error403 from '../pages/403.vue'
import guest from './middleware/guest'
import auth from './middleware/auth'

import { RouteMiddleware } from './middleware'
import middlewarePipeline from './middlewarePipeline'
import { usePageStore } from '../store/page'

const routes: RouteRecordRaw[] = [
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: { middleware: [guest] },
    },
    {
        path: '/',
        name: 'dashboard',
        component: Dashboard,
        meta: { middleware: [auth] },
    },
    {
        name: 'index',
        path: '/resource/:resourceName',
        component: Index,
        props: true,
        meta: { middleware: [auth] },
    },
    {
        name: 'show',
        path: '/resource/:resourceName/:resourceId',
        component: Show,
        props: true,
        meta: { middleware: [auth] },
    },
    {
        name: 'edit',
        path: '/resource/:resourceName/:resourceId/edit',
        component: Edit,
        props: true,
        meta: { middleware: [auth] },
    },
    {
        name: 'create',
        path: '/resource/:resourceName/create',
        component: Create,
        props: true,
        meta: { middleware: [auth] },
    },
    {
        name: '404',
        path: '/404',
        component: Error404,
        meta: { middleware: [auth] },
    },
    {
        name: '403',
        path: '/403',
        component: Error403,
        meta: { middleware: [auth] },
    },
]

const router = createRouter({
    history: createWebHistory('moonshine'),
    routes,
})

router.beforeEach((to, from, next) => {
    const pageStore = usePageStore()
    pageStore.fullReset()

    if (!to.meta.middleware) {
        return next()
    }

    switch (to.name) {
        case 'show':
            pageStore.fetchResourceEntity(String(to.params.resourceName), String(to.params.resourceId))
            break
        case 'edit':
            pageStore.fetchResourceEntity(String(to.params.resourceName), String(to.params.resourceId), 'edit')
            break
        case 'create':
        case 'index':
            pageStore.fetchResource(String(to.params.resourceName))
            break
    }

    // @ts-ignore
    const middleware: RouteMiddleware[] = to.meta.middleware
    const context = { to, from, next }
    return middleware[0]({
        ...context,
        next: middlewarePipeline(context, middleware, 1),
    })
})
export default router
