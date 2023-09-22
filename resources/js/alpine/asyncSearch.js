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

    if(pivot !== null) {
      pivot.querySelector('.tableBuilderAddEvent')?.click()

      this.$nextTick(() => {
        const tr = pivot.querySelector('table > tbody > tr:last-child')
        tr.querySelector('.pivotTitle').innerHTML = item.label
        tr.dataset.key = item.value

        pivot.querySelector('.tableBuilderReIndexEvent')?.click()
      })
    }
  },
  async search() {
    if (this.query.length > 0) {
      let query = '&query=' + this.query

      fetch(route + query + '&' + crudFormQuery())
        .then(response => {
          return response.json()
        })
        .then(data => {
          this.match = data
        })
    }
  },
})
