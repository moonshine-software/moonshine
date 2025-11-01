import {crudFormQuery} from '../Support/Forms.js'
import sortableFunction from './Sortable.js'
import {listComponentRequest, listRowRequest} from '../Request/Sets.js'
import {urlWithQuery, prepareUrl} from '../Request/Core.js'
import {dispatchEvents} from '../Support/DispatchEvents.js'

export default (
  creatable = false,
  reorderable = false,
  reindex = false,
  async = false,
  asyncUrl = '',
) => ({
  actionsOpen: false,
  lastRow: null,
  table: null,
  skeleton: null,
  container: null,
  block: null,
  async: async,
  asyncUrl: asyncUrl,
  reorderable: reorderable,
  creatable: creatable,
  reindex: reindex,
  loading: false,
  stickyColClass: 'sticky-col',
  init() {
    this.block = this.$root
    this.table = this.$root.querySelector('table:not([data-skeleton])')
    this.skeleton = this.$root.querySelector('table[data-skeleton]')
    this.container = this.$root.closest('.js-table-builder-container')

    const removeAfterClone = this.table?.dataset?.removeAfterClone
    const thead = this.table?.querySelector('thead')
    const tbody = this.table?.querySelector('tbody')
    const tfoot = this.table?.querySelector('tfoot')

    if (tfoot !== null && tfoot !== undefined) {
      tfoot.classList.remove('hidden')
    }

    this.lastRow = tbody?.lastElementChild?.cloneNode(true)

    if (removeAfterClone) {
      tbody?.lastElementChild?.remove()
    }

    const stayEmpty = (this.creatable || removeAfterClone) && tbody?.childElementCount === 0

    if (stayEmpty) {
      thead.style.display = 'none'
    }

    if (this.reindex && this.table) {
      this.resolveReindex()
    }

    if (this.reorderable && this.table) {
      sortableFunction(
        this.table?.dataset?.sortableUrl ?? null,
        this.table?.dataset?.sortableGroup ?? null,
        tbody,
        this.table?.dataset?.sortableEvents ?? null,
        this.table?.dataset,
      ).init(() => {
        if (this.reindex) {
          this.resolveReindex()
        }
      })
    }

    this.initColumnSelection()

    if (this.table) {
      this.actions('row', this.table.id)

      if (this.table.querySelectorAll(`.${this.stickyColClass}`)?.length) {
        this.$nextTick().then(() => {
          this.initStickyColumns()
        })
      }

      this.$nextTick().then(() => this.initCellWidth())
    }

    if (this.container?.dataset?.lazy) {
      const event = this.container?.dataset?.lazy
      this.container.removeAttribute('data-lazy')

      this.$nextTick(() => dispatchEvents(event, 'success', this))
    }
  },
  initCellWidth() {
    if (!this.table || !this.table.classList.contains('table-list')) return

    const cells = this.table.querySelectorAll('th, td')

    cells.forEach(cell => {
      if (cell.closest('table') === this.table) {
        this.updateCellWidth(cell)
      }
    })
  },
  updateCellWidth(cell) {
    if (!cell) return

    if (cell.scrollWidth <= cell.clientWidth) {
      cell.classList.add('fit-content')
    } else {
      cell.classList.add('min-content')
    }
  },
  add(force = false) {
    if (!this.creatable && !force) {
      return
    }

    if (!this.table) {
      return
    }

    this.table.querySelector('thead').style.display = 'table-header-group'

    const total = this.table.querySelectorAll('tbody > tr').length
    const limit = this.table.dataset?.creatableLimit

    if (limit && parseInt(total) >= parseInt(limit)) {
      return
    }

    this.table.querySelector('tbody').appendChild(this.lastRow.cloneNode(true))

    const form = this.table.closest('form[data-component]')
    if (form) {
      const formName = form.getAttribute('data-component')
      this.$dispatch('show_when_refresh:' + formName)
    }

    if (!force && this.reindex) {
      this.resolveReindex()
    }
  },
  remove() {
    this.$el.closest('tr').remove()
    if (this.reindex) {
      this.resolveReindex()
    }
  },
  resolveReindex() {
    if (!this.table) {
      return
    }

    let table = this.table

    this.$nextTick(() => {
      MoonShine.iterable.reindex(table, 'tbody > tr:not(tr tr)', 'tr')
    })
  },
  initColumnSelection() {
    if (!this.table) {
      return
    }

    if (!this.block) {
      return
    }

    this.block.querySelectorAll('[data-column-selection-checker]').forEach(el => {
      let stored = localStorage.getItem(this.getColumnSelectionStoreKey(el))
      let hideOnInit = this.table?.querySelector(`[data-column-selection="${el.dataset?.column}"]`)
        ?.dataset.columnSelectionHideOnInit

      if (stored === null && hideOnInit) {
        stored = 'false'
      }

      el.checked = stored === null || stored === 'true'
      this.columnSelection(el)
    })
  },
  getColumnSelectionStoreKey(el) {
    return `${this.table.dataset.name}-column-selection:${el.dataset.column}`
  },
  columnSelection(element = null) {
    const el = element ?? this.$el
    localStorage.setItem(this.getColumnSelectionStoreKey(el), el.checked)

    if (!this.table) {
      return
    }

    this.table.querySelectorAll(`[data-column-selection="${el.dataset.column}"]`).forEach(e => {
      e.hidden = !el.checked
    })

    if (this.skeleton) {
      this.skeleton
        .querySelectorAll(`[data-column-selection="${el.dataset.column}"]`)
        .forEach(e => {
          e.hidden = !el.checked
        })
    }
  },
  asyncFormRequest() {
    this.asyncUrl = urlWithQuery(
      this.$el.getAttribute('action'),
      crudFormQuery(this.$el.querySelectorAll('[name]')),
    )

    this.asyncRequest()
  },
  asyncRequest() {
    listComponentRequest(
      this,
      this.$root?.dataset?.pushState,
      () => {
        this.init()
      },
      this.table?.dataset?.queryParamPrefix,
    )
  },
  asyncRowRequest(key = null, index = null) {
    let eventData = this.$event.detail

    key = key || eventData.key
    index = index || key || 0

    if (key === null && this.$el.tagName.toLowerCase() === 'form') {
      key = new URL(this.$el.action).searchParams.get('resourceItem') ?? index
    } else if (key === null) {
      const tr = this.$el.closest('tr')

      key = tr?.dataset?.rowKey ?? index
    }

    listRowRequest(this, key, index, eventData.type)
  },
  actions(type, id) {
    let all = this.$root.querySelector(`.${id}-actions-all-checked`)

    if (all === null) {
      return
    }

    let checkboxes = this.$root.querySelectorAll(`.${id}-table-action-row`)

    let ids = document.querySelectorAll(
      '.hidden-ids[data-for-component=' + this.table.getAttribute('data-name') + ']',
    )

    let bulkButtons = document.querySelectorAll(
      '[data-button-type=bulk-button][data-for-component=' +
        this.table.getAttribute('data-name') +
        ']',
    )

    ids.forEach(function (value) {
      value.innerHTML = ''
    })

    let values = []

    for (let i = 0, n = checkboxes.length; i < n; i++) {
      if (type === 'all') {
        checkboxes[i].checked = all.checked
      }

      if (checkboxes[i].checked && checkboxes[i].value) {
        values.push(checkboxes[i].value)
      }
    }

    for (let i = 0, n = ids.length; i < n; i++) {
      values.forEach(function (value) {
        ids[i].insertAdjacentHTML(
          'beforeend',
          `<input type="hidden" name="ids[]" value="${value}"/>`,
        )
      })
    }

    for (let i = 0, n = bulkButtons.length; i < n; i++) {
      let url = bulkButtons[i].getAttribute('href')

      if (url === '#') {
        url = ''
      }

      if (!url) {
        continue
      }

      const addIds = []
      values.forEach(value => addIds.push('ids[]=' + value))

      url = urlWithQuery(url, addIds.join('&'), urlObject => urlObject.searchParams.delete('ids[]'))
      bulkButtons[i].setAttribute('href', url)
    }

    all.checked = checkboxes.length === values.length

    this.actionsOpen = !!(all.checked || values.length)
  },
  rowClickAction(event) {
    const isIgnoredElement = event
      .composedPath()
      .some(
        path =>
          path instanceof HTMLAnchorElement ||
          path instanceof HTMLButtonElement ||
          path instanceof HTMLInputElement ||
          path instanceof HTMLSelectElement ||
          path instanceof HTMLLabelElement,
      )

    if (isIgnoredElement || window.getSelection()?.toString()) {
      return
    }

    event.stopPropagation()

    const rowElement = this.$el.parentNode

    switch (this.table.dataset.clickAction) {
      case 'detail':
        rowElement
          .querySelector(this.table.dataset.clickActionSelector ?? '.js-detail-button')
          ?.click()
        break
      case 'edit':
        rowElement
          .querySelector(this.table.dataset.clickActionSelector ?? '.js-edit-button')
          ?.click()
        break
      case 'select':
        rowElement
          .querySelector(
            this.table.dataset.clickActionSelector ?? '.js-table-action-row[type="checkbox"]',
          )
          ?.click()
        break
    }
  },

  /**
   * Initializes sticky columns and event listeners for updating sticky columns.
   *
   * Sets up event listeners to update the positions of sticky columns
   * when the window is resized or when the content of the table changes.
   *
   */
  initStickyColumns() {
    this.updateStickyColumns()

    const observer = new MutationObserver(this.updateStickyColumns.bind(this))
    observer.observe(this.table, {
      childList: true,
      subtree: true,
      attributes: true,
      characterData: true,
    })
  },

  /**
   * Updates the positions of sticky columns.
   *
   * Calculates the left offset for each sticky column and updates their
   * positions accordingly. It is called whenever the window is resized
   * or the content of the table changes.
   *
   */
  updateStickyColumns() {
    const trs = []
    if (this.table) {
      if (this.table.tBodies.length > 0) {
        trs.push(...Array.from(this.table.tBodies[0].rows))
      }
      if (this.table.tHead) {
        trs.push(...Array.from(this.table.tHead.rows))
      }
      if (this.table.tFoot) {
        trs.push(...Array.from(this.table.tFoot.rows))
      }
    }
    const trsWithStickyCol = trs.filter(tr => tr.querySelector(`.${this.stickyColClass}`))
    if (trsWithStickyCol.length < 1) return

    const refRow = trsWithStickyCol[0]
    const refRowCells = Array.from(refRow.children).filter(
      child => child.tagName === 'TD' || child.tagName === 'TH',
    )
    const otherRows = trs.filter(tr => tr !== refRow)

    const stickyCells = refRowCells.filter(cell => cell.classList.contains(this.stickyColClass))
    const centerIndex = Math.floor(refRowCells.length / 2)

    let leftOffset = 0
    let rightOffset = 0

    // Calculate left offsets
    stickyCells.forEach(header => {
      const index = refRowCells.indexOf(header)

      if (index <= centerIndex) {
        header.style.left = `${leftOffset}px`
        leftOffset += header.offsetWidth
      }
    })

    // Calculate right offsets
    for (let i = stickyCells.length - 1; i >= 0; i--) {
      const ref = stickyCells[i]
      const index = refRowCells.indexOf(ref)
      if (index > centerIndex) {
        ref.style.right = `${rightOffset}px`
        rightOffset += ref.offsetWidth
      }
    }

    // Apply the same values to TD cells
    otherRows.forEach(row => {
      const cells = Array.from(row.children).filter(
        child => child.tagName === 'TD' || child.tagName === 'TH',
      )
      stickyCells.forEach(stCell => {
        const index = refRowCells.indexOf(stCell)
        const cell = cells[index]
        if (cell) {
          if (index < centerIndex) {
            cell.style.left = stCell.style.left
          } else {
            cell.style.right = stCell.style.right
          }
        }
      })
    })
  },
})
