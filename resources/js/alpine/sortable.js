import Sortable from 'sortablejs'

export default () => ({
  init() {
    Sortable.create(this.$el, this.$el.dataset)
  },
})

