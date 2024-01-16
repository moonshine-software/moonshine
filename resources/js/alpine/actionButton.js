import {moonShineRequest} from './asyncFunctions'

export default () => ({
  url: '',
  selector: null,
  method: 'GET',
  events: '',
  callback: '',
  loading: false,
  btnText: '',
  beforeCallback: undefined,
  errorCallback: undefined,

  init() {
    this.url = this.$el.href
    this.btnText = this.$el.innerHTML
    this.selector = this.$el?.dataset?.asyncSelector
    this.method = this.$el?.dataset?.asyncMethod
    this.events = this.$el?.dataset?.asyncEvents
    this.callback = this.$el?.dataset?.asyncCallback
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
    if (this.loading) {
      return
    }

    this.loading = true

    moonShineRequest(this, this.url, this.method)
  },
})
