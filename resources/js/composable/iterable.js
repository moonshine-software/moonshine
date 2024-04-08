import sortableFunction from '../alpine/sortable.js'

export class Iterable {
  sortable(element, url = null, group = null, events = null, attributes = {}, onSort = null) {
    sortableFunction(
      url ?? null,
      group ?? null,
      element,
      events ?? null,
      attributes,
    ).init(onSort)
  }

  reindex(block, itemSelector, closestSelector = null) {
    function _reindex(element, level, prev, index = null) {
      element.querySelectorAll(`[data-level="${level}"]`).forEach(function (field) {
        let parent = field.closest(closestSelector ?? itemSelector)
        let name = field.dataset.name
        let _key = parent.dataset.key ?? parent.rowIndex ?? index

        prev['${index' + level + '}'] = _key

        Object.entries(prev).forEach(function ([key, value]) {
          name = name.replace(key, value)
        })

        field.setAttribute('name', name)

        if (field.dataset?.incrementPosition) {
          field.innerHTML = _key
        }

        _reindex(parent, level + 1, prev, _key)
      })
    }

    block.querySelectorAll(itemSelector).forEach(function (element, index) {
      _reindex(element, 0, {}, index + 1)
    })
  }
}
