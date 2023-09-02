import {crudFormQuery} from './formFunctions'
import Sortable from 'sortablejs'

export default (
  creatable = false,
  sortable = false,
  reindex = false,
  async = false,
) => ({
  actionsOpen: false,
  lastRow: null,
  table: null,
  async: async,
  sortable: sortable,
  creatable: creatable,
  reindex: reindex,
  loading: false,
  init() {
    if (this.$refs.foot !== undefined) {
      this.$refs.foot.classList.remove('hidden')
    }

    this.table = this.$root.querySelector('table')
    const tbody = this.table?.querySelector('tbody')

    if(this.creatable) {
      this.lastRow = tbody.lastElementChild.cloneNode(true)
      tbody.lastElementChild.remove()
    }

    if(this.reindex) {
      this.resolveReindex()
    }

    if(this.sortable) {
      Sortable.create(tbody, {
        handle: 'tr',
        onSort: () => {
          if(this.reindex) {
            this.resolveReindex()
          }
        },
      })
    }
  },
  add() {
    if(!this.creatable) {
      return
    }

    this.table.querySelector('tbody').appendChild(this.lastRow.cloneNode(true))
    if(this.reindex) {
      this.resolveReindex()
    }
  },
  remove() {
    if(!this.creatable) {
      return
    }

    this.$el.closest('tr').remove()
    if(this.reindex) {
      this.resolveReindex()
    }
  },
  resolveReindex() {
    function reindexLevel(tr, level, prev) {
      tr.querySelectorAll(`[data-level="${level}"]`).forEach(function(input) {
        let row = input.closest('tr')
        let name = input.dataset.name
        prev['${index' + level + '}'] = row.dataset.key ?? row.rowIndex

        Object.entries(prev).forEach(function([key, value]) {
          name = name.replace(key, value)
        })

        input.setAttribute('name', name)

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
      table.querySelectorAll('tbody > tr:not(tr tr)').forEach(function(tr) {
        reindexLevel(tr, 0, {})
      })
    })
  },
  asyncQuery() {
    this.$event.preventDefault()

    const isForm = this.$el.tagName === 'FORM'

    console.log('isForm', isForm)
    console.log('action', this.$el.getAttribute('action'))
    console.log('crudFormQuery', crudFormQuery(this.$el.querySelectorAll('[name]')))
    console.log('OR href', this.$el.href)

    let url = this.$el.href;

    if(isForm) {
      const urlObject = new URL(this.$el.getAttribute('action'))
      let urlSeparator = urlObject.search === '' ? '?' : '&';
      url = urlObject.href + urlSeparator + crudFormQuery(this.$el.querySelectorAll('[name]'))
    }

    console.log('url', url)

    this.loading = true

    axios.get(url + "&_relation=" + (this.table.dataset?.name ?? 'crud-table'))
    .then(response => {
      this.$root.outerHTML = response.data
    }).catch(error => {
      //
    })
  },
  actions(type, id) {
    let all = this.$root.querySelector('.'+id+'-actionsAllChecked')

    if (all === null) {
      return
    }

    let checkboxes = this.$root.querySelectorAll('.'+id+'-tableActionRow')
    let ids = document.querySelectorAll('.actionsCheckedIds')

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
      ids[i].value = values.join(';')
    }

    this.actionsOpen = !!(all.checked || values.length)
  },
})
