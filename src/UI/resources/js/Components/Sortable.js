import Sortable from 'sortablejs'
import {filterAttributeStartsWith} from '../Support/Forms.js'
import {prepareUrl} from '../Request/Core.js'

export default (url = null, group = null, element = null, events = null, attributes = null) => ({
  init(onSort = null) {
    const el = element || this.$el
    const data = attributes || el.dataset

    let options = {
      group: group
        ? {
            name: group,
          }
        : null,
      ...filterAttributeStartsWith(data, 'async'),
      onSort: async function (evt) {
        if (url) {
          let formData = new FormData()

          formData.append('id', evt.item.dataset?.id)
          formData.append('parent', evt.to.dataset?.id ?? '')
          formData.append('index', evt.newIndex)
          formData.append('data', this.toArray())

          await MoonShine.request(this, prepareUrl(url), 'post', formData)
        }

        if (typeof onSort === 'function') {
          onSort(evt)
        }
      },
    }

    Sortable.create(el, options)
  },
})
