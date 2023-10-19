export default () => ({
  dispatchAsyncEvent(data) {
    if (this.$root.classList.contains('active-query-tag')) {
      this.$dispatch('async-table', {queryTag: 'query-tag=null'})
      this.disableQueryTags()
      return
    }

    this.$dispatch('async-table', {queryTag: 'query-tag=' + data})

    this.disableQueryTags()

    this.$root.classList.add('btn-primary')
    this.$root.classList.add('active-query-tag')
  },
  disableQueryTags() {
    document.querySelectorAll('.query-tag-button').forEach(function (element) {
      element.classList.remove('btn-primary')
      element.classList.remove('active-query-tag')
    })
  }
})
