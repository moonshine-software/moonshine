import selectorsParams from '../Support/SelectorsParams.js'
import {ComponentRequestData} from '../DTOs/ComponentRequestData.js'
import request from '../Request/Core.js'

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

    const query = new URLSearchParams(body)

    if(this.$event.detail) {
      for (const [key, value] of Object.entries(this.$event.detail)) {
        if (typeof value === 'object' && value !== null) {
          for (const k in value) {
            query.append(`${key}[${k}]`, value[k]);
          }
        } else {
          query.append(key, value);
        }
      }
    }
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

    request(this, this.asyncUpdateRoute, 'get', body, {}, componentRequestData)
  },
})
