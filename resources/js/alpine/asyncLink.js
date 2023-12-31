export default (activeClass, componentEvent) => ({
  queryTagRequest(data) {
    if (this.$root.classList.contains('active-query-tag')) {
      this.$dispatch(componentEvent, {queryTag: 'query-tag=null'})
      this.disableQueryTags()
      return
    }

    this.$dispatch(componentEvent, {queryTag: 'query-tag=' + data})

    this.disableQueryTags()

    this.$root.classList.add(activeClass)
    this.$root.classList.add('active-query-tag')
  },
  disableQueryTags() {
    document.querySelectorAll('.query-tag-button').forEach(function (element) {
      element.classList.remove(activeClass)
      element.classList.remove('active-query-tag')
    })
  },
})
