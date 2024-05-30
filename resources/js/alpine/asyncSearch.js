/* Search */
import {crudFormQuery} from './formFunctions'

export default route => ({
  items: [],
  match: [],
  query: '',
  select(item) {
    this.query = ''
    this.match = []

    const pivot = this.$root.querySelector('.pivotTable')

    if (pivot !== null) {
      const tableName = pivot.dataset.tableName.toLowerCase()

      this.$dispatch('table_row_added:' + tableName)

      const tr = pivot.querySelector('table > tbody > tr:last-child')
      tr.querySelector('.pivotTitle').innerHTML = item.label
      tr.dataset.key = item.value
      tr.querySelector('.pivotChecker').checked = true

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
