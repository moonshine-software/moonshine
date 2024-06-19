export default (activeClass, componentEvent) => ({
  queryTagRequest(data) {
    if (this.$root.classList.contains(activeClass)) {
      this.$dispatch(componentEvent.toLowerCase(), {queryTag: 'query-tag=null'})
      this.disableQueryTags()
      this.activeDefaultQueryTag()
      return
    }

    this.$dispatch(componentEvent.toLowerCase(), {queryTag: 'query-tag=' + data})

    this.disableQueryTags()

    this.$root.classList.add(activeClass)
  },
  disableQueryTags() {
    document.querySelectorAll('.query-tag-button').forEach(function (element) {
      element.classList.remove(activeClass)
    })
  },
  activeDefaultQueryTag() {
    const element = document.querySelector('.query-tag-default')
    element.classList.add(activeClass)
  }
})
