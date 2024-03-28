import {moonShineRequest} from './asyncFunctions'

export default () => ({
  url: '',
  selector: null,
  method: 'GET',
  events: '',
  withParams: '',
  callback: '',
  loading: false,
  btnText: '',

  beforeCallback: null,
  errorCallback: null,
  beforeFunction: null,

  init() {
    this.url = this.$el.href
    this.btnText = this.$el.innerHTML
    this.selector = this.$el?.dataset?.asyncSelector
    this.method = this.$el?.dataset?.asyncMethod
    this.events = this.$el?.dataset?.asyncEvents
    this.withParams = this.$el?.dataset?.withParams
    this.callback = this.$el?.dataset?.asyncCallback
    this.beforeFunction = this.$el?.dataset?.asyncBeforeFunction
    this.loading = false
    const el = this.$el
    const btnText = this.btnText

    this.$watch('loading', function (value) {
      el.setAttribute('style', 'opacity:' + (value ? '.5' : '1'))
      el.innerHTML = value
        ? '<div class="spinner spinner--primary spinner-sm"></div>' + btnText
        : btnText
    })

    this.beforeCallback = function () {
      this.loading = false
    }

    this.errorCallback = function () {
      this.loading = false
    }
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
        if(el != null) {
          body[paramName] = el.value
        }
      })
    }

    moonShineRequest(this, this.url, this.method, body)
  },
})
