import {crudFormQuery} from './formFunctions'

export default async => ({
  actionsOpen: false,
  async: async,
  loading: false,
  init() {
    if (this.$refs.foot !== undefined) {
      this.$refs.foot.classList.remove('hidden')
    }
  },
  canBeAsync() {
    this.$event.preventDefault()

    const isForm = this.$el.tagName === 'FORM'
    const url = isForm
      ? this.$el.getAttribute('action') + '?' + crudFormQuery(this.$el.querySelectorAll('[name]'))
      : this.$el.href

    if (!async && isForm) {
      this.$el.submit()
    }

    if (!async) {
      window.location = url
    }

    this.loading = true

    axios
      .get(url, {
        headers: {
          'X-Fragment': 'crud-table',
        },
      })
      .then(response => response.data)
      .then(html => {
        this.$root.outerHTML = html
      })
      .catch(error => {
        //
      })
  },
  actions(type) {
    let all = this.$root.querySelector('.actionsAllChecked')

    if (all === null) {
      return
    }

    let checkboxes = this.$root.querySelectorAll('.tableActionRow')
    let ids = document.querySelectorAll('.actionsCheckedIds')

    let values = []

    for (let i = 0, n = checkboxes.length; i < n; i++) {
      if (type === 'all') {
        checkboxes[i].checked = all.checked
      }

      if (checkboxes[i].checked && checkboxes[i].value) {
        values.push(checkboxes[i].value)
      }
    }

    for (let i = 0, n = ids.length; i < n; i++) {
      ids[i].value = values.join(';')
    }

    this.actionsOpen = !!(all.checked || values.length)
  },
})
