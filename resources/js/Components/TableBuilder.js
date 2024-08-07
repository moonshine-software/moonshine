import {crudFormQuery} from '../Support/Forms.js'
import sortableFunction from './Sortable.js'
import {listComponentRequest} from '../Request/Sets.js'
import {urlWithQuery} from '../Request/Core.js'

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
  async: async,
  asyncUrl: asyncUrl,
  reorderable: reorderable,
  creatable: creatable,
  reindex: reindex,
  loading: false,
  init() {
    this.table = this.$root.querySelector('table')
    const removeAfterClone = this.table?.dataset?.removeAfterClone
    const tbody = this.table?.querySelector('tbody')
    const tfoot = this.table?.querySelector('tfoot')

    if (tfoot !== null && tfoot !== undefined) {
      tfoot.classList.remove('hidden')
    }

    this.lastRow = tbody?.lastElementChild?.cloneNode(true)

    if (this.creatable || removeAfterClone) {
      tbody?.lastElementChild?.remove()
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
  },
  add(force = false) {
    if (!this.creatable && !force) {
      return
    }

    const total = this.table.querySelectorAll('tbody > tr').length
    const limit = this.table.dataset?.creatableLimit

    if (limit && parseInt(total) >= parseInt(limit)) {
      return
    }

    this.table.querySelector('tbody').appendChild(this.lastRow.cloneNode(true))

    const form = this.table.closest('[data-form-component]')
    if(form) {
      const formName = form.getAttribute('data-form-component')
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
      MoonShine.iterable.reindex(table, 'tr')
    })
  },
  initColumnSelection() {
    this.$root.querySelectorAll('[data-column-selection-checker]').forEach(el => {
      let stored = localStorage.getItem(this.getColumnSelectionStoreKey(el))

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

    this.table.querySelectorAll(`[data-column-selection="${el.dataset.column}"]`).forEach(e => {
      e.hidden = !el.checked
    })
  },
  asyncFormRequest() {
    this.asyncUrl = urlWithQuery(
      this.$el.getAttribute('action'),
      crudFormQuery(this.$el.querySelectorAll('[name]')),
    )

    this.asyncRequest()
  },
  asyncRequest() {
    listComponentRequest(this, this.$root?.dataset?.pushstate)
  },
  asyncRowRequest(key, index) {
    const t = this
    const tr = this.table.querySelector('[data-row-key="' + key + '"]')

    if (tr === null) {
      return
    }

    axios
      .get(t.asyncUrl + `&_key=${key}&_index=${index}`)
      .then(response => {
        tr.outerHTML = response.data
      })
      .catch(error => {})
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
          path instanceof HTMLLabelElement,
      )

    if (isIgnoredElement || window.getSelection()?.toString()) {
      return
    }

    const rowElement = this.$el.parentNode

    switch (this.table.dataset.clickAction) {
      case 'detail':
        rowElement.querySelector('.js-detail-button')?.click()
        break
      case 'edit':
        rowElement.querySelector('.js-edit-button')?.click()
        break
      case 'select':
        rowElement.querySelector('.js-table-action-row[type="checkbox"]')?.click()
        break
    }
  },
})
