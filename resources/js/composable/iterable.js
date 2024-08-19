import sortableFunction from '../alpine/sortable.js'

export class Iterable {
  sortable(element, url = null, group = null, events = null, attributes = {}, onSort = null) {
    sortableFunction(url ?? null, group ?? null, element, events ?? null, attributes).init(onSort)
  }

  reindex(block, itemSelector, closestSelector = null) {
    let topLevelBlock = block.hasAttribute('data-top-level')
      ? block
      : block.closest(`[data-top-level]`)

    if (topLevelBlock === null) {
      return
    }

    function _reindex(element, level, prev, index = null) {
      element.querySelectorAll(`[data-level="${level}"]`).forEach(function (field) {
        let parent = field.closest(closestSelector ?? itemSelector)
        let name = field.dataset.name
        let _key = parent.dataset.key ?? parent.rowIndex ?? index

        let currentPrev = {...prev}
        currentPrev['${index' + level + '}'] = _key

        Object.entries(currentPrev).forEach(function ([key, value]) {
          name = name.replace(key, value)
        })

        field.setAttribute('name', name)
        field.setAttribute('data-r-index', _key)

        if (field.dataset?.incrementPosition) {
          field.innerHTML = _key
        }

        _reindex(parent, level + 1, currentPrev, _key)
      })
    }

    _reindex(topLevelBlock, 0, {}, 1)
  }
}
