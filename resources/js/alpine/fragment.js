import {moonShineRequest, withSelectorsParams} from './asyncFunctions.js'
import {ComponentRequestData} from '../moonshine.js'

export default (asyncUpdateRoute = '') => ({
  asyncUpdateRoute: asyncUpdateRoute,
  withParams: '',
  loading: false,

  init() {
    this.loading = false
    this.withParams = this.$el?.dataset?.asyncWithParams
  },
  fragmentUpdate() {
    if (this.asyncUpdateRoute === '') {
      return
    }

    if (this.loading) {
      return
    }

    this.loading = true

    let body = withSelectorsParams(this.withParams)

    const t = this

    const query = new URLSearchParams(body).toString()

    t.asyncUpdateRoute += t.asyncUpdateRoute.includes('?') ? '&' + query : '?' + query

    let stopLoading = function (data, t) {
      t.loading = false
    }

    let componentRequestData = new ComponentRequestData()
    componentRequestData
      .withAfterCallback(function (data) {
        t.$root.outerHTML = data
      })
      .withBeforeCallback(stopLoading)
      .withErrorCallback(stopLoading)

    moonShineRequest(this, this.asyncUpdateRoute, 'get', body, {}, componentRequestData)
  },
})
