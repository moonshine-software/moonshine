import selectorsParams from '../Support/SelectorsParams.js'
import {ComponentRequestData} from '../DTOs/ComponentRequestData.js'
import {dispatchEvents as de} from '../Support/DispatchEvents.js'
import request from '../Request/Core.js'

export default () => ({
  url: '',
  method: 'GET',
  withParams: '',
  loading: false,
  btnText: '',

  init() {
    this.url = this.$el.href
    this.btnText = this.$el.innerHTML
    this.method = this.$el?.dataset?.asyncMethod

    this.withParams = this.$el?.dataset?.asyncWithParams

    this.loading = false
    const el = this.$el
    const btnText = this.btnText

    this.$watch('loading', function (value) {
      el.setAttribute('style', 'opacity:' + (value ? '.5' : '1'))
      el.innerHTML = value
        ? '<div class="spinner spinner--primary spinner-sm"></div>' + btnText
        : btnText
    })
  },

  dispatchEvents(componentEvent, exclude = null, extra = {}) {
    const url = new URL(this.$el.href)
    extra['_data'] = Object.fromEntries(new URLSearchParams(url.search))

    de(componentEvent, '', this, extra)
  },

  request() {
    this.url = this.$el.href

    if (this.loading) {
      return
    }

    this.loading = true

    if (this.withParams !== undefined && this.withParams) {
      this.method = this.method.toLowerCase() === 'get' ? 'post' : this.method
    }

    let body = selectorsParams(this.withParams)

    let stopLoading = function (data, t) {
      t.loading = false
    }

    let componentRequestData = new ComponentRequestData()
    componentRequestData
      .fromDataset(this.$el?.dataset ?? {})
      .withBeforeCallback(stopLoading)
      .withErrorCallback(stopLoading)

    request(this, this.url, this.method, body, {}, componentRequestData)
  },
})
