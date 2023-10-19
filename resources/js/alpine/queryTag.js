export default () => ({
  dispatchAsyncEvent(data) {
    if (this.$root.classList.contains('active-query-tag')) {
      this.$dispatch('async-table', {queryTag: 'query-tag=null'})
      this.$root.classList.remove('btn-primary')
      this.$root.classList.remove('active-query-tag')
      return
    }

    this.$dispatch('async-table', {queryTag: 'query-tag=' + data})

    document.querySelectorAll('.query-tag-button').forEach(function (element) {
      element.classList.remove('btn-primary')
    })

    this.$root.classList.add('btn-primary')
    this.$root.classList.add('active-query-tag')
  },
})
