/* Search */

export default (route) => ({
  items: [],
  match: [],
  query: '',
  select(index) {
    if (!this.items.includes(this.match[index])) {
      this.items.push({ key: index, value: this.match[index] })
    }

    this.query = ''
    this.match = []
  },
  async search() {
    if (this.query.length > 0) {
      let query = '&query=' + this.query;

      fetch(route + query).then((response) => {
        return response.json();
      }).then((data) => {
        this.match = data
      })
    }
  },

})
