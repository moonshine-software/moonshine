import {prepareQueryParams} from '../Support/URLs.js'

export default (activeClass, componentEvent) => ({
  request(data, prefix = '') {
    const queryParams = new URLSearchParams(window.location.search)

    if (this.$root.classList.contains(activeClass)) {
      queryParams.set(`${prefix}query-tag`, '')
      this.disableQueryTags()
      this.activeDefaultQueryTag()
    } else {
      queryParams.set(`${prefix}query-tag`, data)
      this.disableQueryTags()
      this.$root.classList.add(activeClass)
    }

    this.$dispatch(componentEvent.toLowerCase(), {
      queryTag: prepareQueryParams(queryParams, '_component_name,_token,_method,page').toString(),
      events: this.$el.dataset.asyncEvents ?? '',
    })
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
