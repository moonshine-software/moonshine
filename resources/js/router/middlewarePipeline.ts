import { MoonshineMiddlewareParams, RouteMiddleware } from './middleware'

export default function middlewarePipeline(
    context: MoonshineMiddlewareParams,
    middleware: RouteMiddleware[],
    index: number
) {
    const nextMiddleware = middleware[index]

    if (!nextMiddleware) {
        return context.next
    }
    return () => {
        const nextPipeline = middlewarePipeline(context, middleware, index + 1)
        nextMiddleware({ ...context, next: nextPipeline })
    }
}
