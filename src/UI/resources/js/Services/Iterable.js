import sortableFunction from '../Components/Sortable.js'

export class Iterable {
  sortable(element, url = null, group = null, events = null, attributes = {}, onSort = null) {
    sortableFunction(url ?? null, group ?? null, element, events ?? null, attributes).init(onSort)
  }

  async reindex(block, itemSelector, closestSelector = null) {
    closestSelector = closestSelector ?? itemSelector

    let topLevelBlock = block.hasAttribute('data-top-level')
      ? block
      : block.closest(`[data-top-level]`)

    if (topLevelBlock === null) {
      topLevelBlock = block
      block.setAttribute('data-top-level', true)
    }

    block.setAttribute('data-r-block', true)

    if (!topLevelBlock.hasAttribute('data-r-item-selector')) {
      topLevelBlock.setAttribute('data-r-item-selector', itemSelector)
    }

    if (!block.hasAttribute('data-r-closest-selector')) {
      block.setAttribute('data-r-closest-selector', closestSelector)
    }

    function reindexProcess(el, level, prev, index = null) {
      let levelFields = el.querySelectorAll(`[data-level="${level}"]`)

      if (levelFields.length === 0) {
        return
      }

      levelFields.forEach(function (fieldOrBlock) {
        // return skip already processed block
        if (fieldOrBlock.hasAttribute('data-r-done')) {
          return
        }

        fieldOrBlock.setAttribute('data-r-done', true)

        if (fieldOrBlock.hasAttribute('data-r-block')) {
          let currentPrev = {...prev}
          currentPrev['${index' + (level + 1) + '}'] = 1

          reindexProcess(fieldOrBlock, level + 1, currentPrev, 1)
          return
        }

        let name = fieldOrBlock.dataset.name
        let closestBlock = fieldOrBlock.closest(`[data-r-block]`)
        let parent = fieldOrBlock.closest(closestBlock.dataset.rClosestSelector)
        let _key = parseInt(parent.dataset.key ?? parent.rowIndex ?? index)

        prev['${index' + level + '}'] = _key

        Object.entries(prev).forEach(function ([key, value]) {
          name = name.replace(key, value)
        })

        fieldOrBlock.setAttribute('name', name)
        fieldOrBlock.setAttribute('data-r-index', _key)

        if (fieldOrBlock.dataset?.incrementPosition) {
          fieldOrBlock.innerHTML = _key
        }
      })
    }

    // return skip already processed blocks
    await this.$nextTick

    if (block.hasAttribute('data-r-done')) {
      return
    }

    topLevelBlock
      .querySelectorAll(topLevelBlock.dataset.rItemSelector)
      .forEach(function (element, index) {
        const i = parseInt(index) + 1

        reindexProcess(
          element,
          0,
          {
            '${index0}': i,
          },
          i,
        )
      })

    // return skipped to reindex process
    await this.$nextTick

    topLevelBlock.querySelectorAll('[data-r-done]').forEach(function (element) {
      element.removeAttribute('data-r-done')
    })
  }
}
