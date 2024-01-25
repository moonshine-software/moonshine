import {crudFormQuery} from './formFunctions'
import sortableFunction from './sortable'
import {listComponentRequest} from './asyncFunctions'

export default (
  creatable = false,
  sortable = false,
  reindex = false,
  async = false,
  asyncUrl = '',
) => ({
  actionsOpen: false,
  lastRow: null,
  table: null,
  async: async,
  asyncUrl: asyncUrl,
  sortable: sortable,
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

    if (this.reindex) {
      this.resolveReindex()
    }

    if (this.sortable) {
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
    function reindexLevel(tr, level, prev) {
      tr.querySelectorAll(`[data-level="${level}"]`).forEach(function (field) {
        let row = field.closest('tr')
        let name = field.dataset.name
        prev['${index' + level + '}'] = row.dataset.key ?? row.rowIndex

        Object.entries(prev).forEach(function ([key, value]) {
          name = name.replace(key, value)
        })

        field.setAttribute('name', name)

        if (field.dataset?.incrementPosition) {
          field.innerHTML = row.rowIndex
        }

        reindexLevel(row, level + 1, prev)
      })
    }

    function findRoot(element) {
      let parent = element.parentNode.closest('table')

      if (parent) {
        return findRoot(parent)
      }

      return element
    }

    let table = findRoot(this.table)

    this.$nextTick(() => {
      table.querySelectorAll('tbody > tr:not(tr tr)').forEach(function (tr) {
        reindexLevel(tr, 0, {})
      })
    })
  },
  asyncFormRequest() {
    const urlObject = new URL(this.$el.getAttribute('action'))
    let urlSeparator = urlObject.search === '' ? '?' : '&'

    this.asyncUrl =
      urlObject.href + urlSeparator + crudFormQuery(this.$el.querySelectorAll('[name]'))

    this.asyncRequest()
  },
  asyncRequest() {
    listComponentRequest(this)
  },
  actions(type, id) {
    let all = this.$root.querySelector('.' + id + '-actionsAllChecked')

    if (all === null) {
      return
    }

    let checkboxes = this.$root.querySelectorAll('.' + id + '-tableActionRow')
    let ids = document.querySelectorAll('.hidden-ids')
    let bulkButtons = this.$root.querySelectorAll('[data-button-type=bulk-button]')

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
      if(! url) {
        continue
      }

      const urlObject = new URL(url)
      let urlSeparator = urlObject.search === '' ? '?' : '&'
      urlObject.searchParams.delete('ids[]')

      const addIds = []
      values.forEach(function (value) {
        addIds.push('ids[]=' + value)
      })

      url = urlObject.href + urlSeparator + addIds.join('&')
      bulkButtons[i].setAttribute('href', url)
    }

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
        rowElement.querySelector('.detail-button')?.click()
        break
      case 'edit':
        rowElement.querySelector('.edit-button')?.click()
        break
      case 'select':
        rowElement.querySelector('.tableActionRow[type="checkbox"]')?.click()
        break
    }
  },
})
