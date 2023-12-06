import {dispatchEvents, responseCallback} from './asyncFunctions'

export default () => ({
  url: '',
  selector: null,
  method: 'GET',
  events: '',
  callback: '',
  loading: false,
  btnText: '',

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
  },

  request() {
    if (this.loading) {
      return
    }

    this.loading = true

    const t = this

    axios({
      url: this.url,
      method: this.method,
    })
      .then(function (response) {
        t.loading = false

        const data = response.data

        if (t.callback) {
          responseCallback(t.callback, data, t.$el, t.events, t)

          return
        }

        if (t.selector) {
          const element = document.querySelector(t.selector)
          element.innerHTML = data.html ? data.html : data
        }

        if (data.redirect) {
          window.location = data.redirect
        }

        const type = data.messageType ? data.messageType : 'success'

        if (data.message) {
          t.$dispatch('toast', {
            type: type,
            text: data.message,
          })
        }

        dispatchEvents(t.events, type, t)
      })
      .catch(errorResponse => {
        t.loading = false

        const data = errorResponse.response.data

        if (t.callback) {
          responseCallback(t.callback, data, t.$el, t.events, t)

          return
        }

        t.$dispatch('toast', {type: 'error', text: data.message ?? data})
      })
  },
})
