export default (activeClass, componentEvent) => ({
  queryTagRequest(data) {
    const queryParams = new URLSearchParams(window.location.search)

    this.disableQueryTags()

    if (this.$root.classList.contains(activeClass)) {
      queryParams.set('query-tag', null)
      this.activeDefaultQueryTag()
    } else {
      queryParams.set('query-tag', data)
      this.$root.classList.add(activeClass)
    }

    this.$dispatch(componentEvent.toLowerCase(), {
      queryTag: queryParams.toString(),
    })
  },
  disableQueryTags() {
    document.querySelectorAll('.query-tag-button').forEach(function (element) {
      element.classList.remove(activeClass)
    })
  },
  activeDefaultQueryTag() {
    const element = document.querySelector('.query-tag-default')
    element.classList.add(activeClass)
  },
})
