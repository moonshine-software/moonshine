import Sortable from 'sortablejs'

export default (url = null, group = null) => ({
  init() {
    let options = {
      group: group
        ? {
            name: group,
          }
        : null,
      ...this.$el.dataset,
      onSort: async function (evt) {
        if (url) {
          let formData = new FormData()

          formData.append('id', evt.item.dataset?.id)
          formData.append('parent', evt.to.dataset?.id)
          formData.append('index', evt.newIndex)
          formData.append('data', this.toArray())

          await axios.post(url, formData)
        }
      },
    }

    Sortable.create(this.$el, options)
  },
})
