import sortableFunction from '../Components/Sortable.js'

export class Iterable {
  sortable(element, url = null, group = null, events = null, attributes = {}, onSort = null) {
    sortableFunction(url ?? null, group ?? null, element, events ?? null, attributes).init(onSort)
  }

  reindex(block, itemSelector, closestSelector = null) {
    function _reindex(element, level, prev, index = null) {
      element.querySelectorAll(`[data-level="${level}"]`).forEach(function (field) {
        let parent = field.closest('[data-re-index-item-selector]')
        let name = field.dataset.name
        let _key = parent.dataset.rowKey ?? parent.rowIndex ?? index

        prev['${index' + level + '}'] = _key

        Object.entries(prev).forEach(function ([key, value]) {
          name = name.replace(key, value)
        })

        field.setAttribute('name', name)
        field.setAttribute('data-r-index', _key)

        if (field.dataset?.incrementPosition) {
          field.innerHTML = _key
        }

        _reindex(parent, level + 1, prev, _key)
      })
    }

    block.querySelectorAll(itemSelector).forEach(function (element, index) {
      element.setAttribute('data-re-index-item-selector', closestSelector ?? itemSelector)

      let level = 0
      let prev = {}

      if (block.dataset.level) {
        prev['${index' + level + '}'] = block.dataset.rIndex ?? 1
        level = parseInt(block.dataset.level) + 1
      }

      _reindex(element, level, prev, index + 1)
    })
  }
}
