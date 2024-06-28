import {crudFormQuery} from '../Support/Forms.js'

export default route => ({
  items: [],
  match: [],
  query: '',
  select(item) {
    this.query = ''
    this.match = []

    const pivot = this.$root.querySelector('.js-pivot-table')

    if (pivot !== null) {
      const tableName = pivot.dataset.tableName.toLowerCase()

      this.$dispatch('table_row_added:' + tableName)

      const tr = pivot.querySelector('table > tbody > tr:last-child')
      tr.querySelector('.js-pivot-title').innerHTML = item.label
      tr.dataset.rowKey = item.value
      tr.querySelector('.js-pivot-checker').checked = true

      this.$dispatch('table_reindex:' + tableName)
    }
  },
  async search() {
    if (this.query.length > 0) {
      let query = '&query=' + this.query

      const form = this.$el.closest('form')
      const formQuery = crudFormQuery(form.querySelectorAll('[name]'))

      fetch(route + query + (formQuery.length ? '&' + formQuery : ''))
        .then(response => {
          return response.json()
        })
        .then(data => {
          this.match = data
        })
    }
  },
})
