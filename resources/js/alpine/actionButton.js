import {moonShineRequest} from './asyncFunctions'
import {ComponentRequestData} from '../moonshine.js'

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

    this.withParams = this.$el?.dataset?.withParams

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

  request() {
    this.url = this.$el.href

    if (this.loading) {
      return
    }

    this.loading = true

    let body = {}

    if (this.withParams !== undefined && this.withParams) {
      this.method = this.method.toLowerCase() === 'get' ? 'post' : this.method

      const selectors = this.withParams.split(',')
      selectors.forEach(function (selector) {
        let parts = selector.split('/')

        let paramName = parts[1] ?? parts[0]

        const el = document.querySelector(parts[0])
        if (el != null) {
          body[paramName] = el.value
        }
      })
    }

    let stopLoading = function (data, t) {
      t.loading = false
    }

    let componentRequestData = new ComponentRequestData()
    componentRequestData
      .fromDataset(this.$el?.dataset ?? {})
      .withBeforeCallback(stopLoading)
      .withErrorCallback(stopLoading)

    moonShineRequest(this, this.url, this.method, body, {}, componentRequestData)
  },
})
