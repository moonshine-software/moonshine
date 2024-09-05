import selectorsParams from '../Support/SelectorsParams.js'
import {ComponentRequestData} from '../DTOs/ComponentRequestData.js'
import request from '../Request/Core.js'
import {getQueryString} from '../Support/Forms.js'

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

    let body = selectorsParams(this.withParams)

    const t = this

    const query = new URLSearchParams(body).toString()

    t.asyncUpdateRoute += t.asyncUpdateRoute.includes('?') ? '&' + query : '?' + query
    t.asyncUpdateRoute += getQueryString(this.$event.detail)

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

    request(this, this.asyncUpdateRoute, 'get', body, {}, componentRequestData)
  },
})
