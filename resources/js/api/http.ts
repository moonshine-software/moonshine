import axios, {
    AxiosError,
    AxiosInstance,
    AxiosRequestConfig,
    AxiosResponse,
} from 'axios'
import { useUserStore } from '../store/user'
import router from '../router/routes'

const baseURL: string = `/moonshine`
enum StatusCode {
    Unauthorized = 401,
    Forbidden = 403,
    NotFound = 404,
    TooManyRequests = 429,
    UnprocessableEntity = 422,
    InternalServerError = 500,
}

const headers: Readonly<Record<string, string | boolean>> = {
    Accept: 'application/json',
    'Content-Type': 'application/json; charset=utf-8',
    'Access-Control-Allow-Credentials': true,
    'X-Requested-With': 'XMLHttpRequest',
}

class Http {
    private instance: AxiosInstance | null = null
    private get http(): AxiosInstance {
        return this.instance != null ? this.instance : this.initHttp()
    }

    initHttp() {
        const http = axios.create({
            baseURL,
            headers,
            withCredentials: true,
        })

        http.interceptors.response.use(
            (response) => response,
            (error) => {
                return this.handleError(error)
            }
        )

        this.instance = http
        return http
    }

    request<T = any, R = AxiosResponse<T>>(
        config: AxiosRequestConfig
    ): Promise<R> {
        return this.http.request(config)
    }

    get<T = any, R = AxiosResponse<T>>(
        url: string,
        config?: AxiosRequestConfig
    ): Promise<R> {
        return this.http.get<T, R>(url, config)
    }

    post<T = any, R = AxiosResponse<T>>(
        url: string,
        data?: T,
        config?: AxiosRequestConfig
    ): Promise<R> {
        return this.http.post<T, R>(url, data, config)
    }

    put<T = any, R = AxiosResponse<T>>(
        url: string,
        data?: T,
        config?: AxiosRequestConfig
    ): Promise<R> {
        return this.http.put<T, R>(url, data, config)
    }

    delete<T = any, R = AxiosResponse<T>>(
        url: string,
        config?: AxiosRequestConfig
    ): Promise<R> {
        return this.http.delete<T, R>(url, config)
    }

    private handleError(error: AxiosError) {
        const { response } = error

        switch (response?.status) {
            case StatusCode.UnprocessableEntity: {
                // Handle UnprocessableEntity
                break
            }
            case StatusCode.InternalServerError: {
                // Handle InternalServerError
                break
            }
            case StatusCode.NotFound: {
                router.push('/404')
                break
            }
            case StatusCode.Forbidden: {
                // Handle Forbidden
                break
            }
            case StatusCode.Unauthorized: {
                const user = useUserStore()
                user.resetUser()
                break
            }
            case StatusCode.TooManyRequests: {
                // Handle TooManyRequests
                break
            }
        }

        return Promise.reject(error)
    }
}

export const http = new Http()
