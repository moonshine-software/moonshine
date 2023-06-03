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
    if (!async) {
      window.location = this.$el.href
    }

    this.loading = true

    axios
      .get(this.$el.href, {
        headers: {
          'X-Fragment': 'crud-table',
        },
      })
      .then(response => response.data)
      .then(html => {
        this.loading = false
        this.$root.innerHTML = html
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
