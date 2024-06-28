export default (activeClass, componentEvent) => ({
  queryTagRequest(data) {
    const queryParams = new URLSearchParams(window.location.search)

    if (this.$root.classList.contains(activeClass)) {
      queryParams.set('query-tag', '')
      this.disableQueryTags()
      this.activeDefaultQueryTag()
    } else {
      queryParams.set('query-tag', data)
      this.disableQueryTags()
      this.$root.classList.add(activeClass)
    }

    this.$dispatch(componentEvent.toLowerCase(), {
      queryTag: this.prepareQueryString(queryParams, '_component_name,_token,_method,page'),
    })
  },
  prepareQueryString(queryParams, exclude = null) {
    if (exclude !== null) {
      const excludes = exclude.split(',')

      excludes.forEach(function (excludeName) {
        queryParams.delete(excludeName)
      })
    }

    return new URLSearchParams(queryParams).toString()
  },
  disableQueryTags() {
    document.querySelectorAll('.js-query-tag-button').forEach(function (element) {
      element.classList.remove(activeClass)
    })
  },
  activeDefaultQueryTag() {
    const element = document.querySelector('.js-query-tag-default')
    element?.classList.add(activeClass)
  },
})
