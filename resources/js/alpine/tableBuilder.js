import {crudFormQuery} from './formFunctions'
import Sortable from 'sortablejs'

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
      Sortable.create(tbody, {
        handle: 'tr',
        onSort: () => {
          if (this.reindex) {
            this.resolveReindex()
          }
        },
      })
    }
  },
  add(force = false) {
    if (!this.creatable && !force) {
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
    this.$event.preventDefault()

    let url = this.$el.href ? this.$el.href : this.asyncUrl

    this.loading = true

    if (this.$event.detail && this.$event.detail.filters) {
      url = this.prepareUrl(url)

      const urlWithFilters = new URL(url)

      let separator = urlWithFilters.searchParams.size ? '&' : '?'

      url = urlWithFilters.toString() + separator + this.$event.detail.filters
    }

    if (this.$event.detail && this.$event.detail.queryTag) {
      url = this.prepareUrl(url)

      if (this.$event.detail.queryTag !== 'query-tag=null') {
        const urlWithQueryTags = new URL(url)

        let separator = urlWithQueryTags.searchParams.size ? '&' : '?'

        url = urlWithQueryTags.toString() + separator + this.$event.detail.queryTag
      }
    }

    const t = this

    axios
      .get(url)
      .then(response => {
        if (
          t.$root.getAttribute('data-pushstate') !== null &&
          t.$root.getAttribute('data-pushstate')
        ) {
          const query = url.slice(url.indexOf('?') + 1)
          history.pushState({}, '', query ? '?' + query : location.pathname)
        }
        this.$root.outerHTML = response.data
      })
      .catch(error => {
        //
      })
  },
  actions(type, id) {
    let all = this.$root.querySelector('.' + id + '-actionsAllChecked')

    if (all === null) {
      return
    }

    let checkboxes = this.$root.querySelectorAll('.' + id + '-tableActionRow')
    let ids = document.querySelectorAll('.hidden-ids')

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

    this.actionsOpen = !!(all.checked || values.length)
  },
  prepareUrl(url) {
    const resultUrl = new URL(url)

    if (resultUrl.searchParams.get('query-tag')) {
      resultUrl.searchParams.delete('query-tag')
    }

    Array.from(resultUrl.searchParams).map(function (values) {
      let [index] = values
      if (index.indexOf('filters[') === 0) {
        resultUrl.searchParams.delete(index)
      }
    })

    return resultUrl.toString()
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
