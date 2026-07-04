export default function jsonField(
  initialRows = [],
  fields = [],
  removable = true,
  reorderable = false,
  keyValue = false,
  onlyValue = false,
  objectMode = false,
  filterEmpty = true,
  creatable = true,
  creatableLimit = null,
) {
  return {
    rows: [],
    fields: fields,
    removable: removable,
    creatable: creatable,
    creatableLimit: creatableLimit,
    reorderable: reorderable,
    keyValue: keyValue,
    onlyValue: onlyValue,
    objectMode: objectMode,
    filterEmpty: filterEmpty,
    draggingIndex: null,
    draggingRows: null,
    dropIndex: null,
    dragPointerId: null,
    dragContainer: null,
    draggingHeight: null,
    pendingClientY: null,
    pointerMoveFrame: null,
    dragAnimations: [],
    pointerMoveHandler: null,
    pointerUpHandler: null,
    pointerCancelHandler: null,
    payload: '[]',
    init() {
      this.rows = this.normalizeRows(initialRows)

      this.sync()
      this.$nextTick(() => this.syncControls())
    },
    makeKey() {
      if (window.crypto?.randomUUID) {
        return window.crypto.randomUUID()
      }

      return `json-${Date.now()}-${Math.random().toString(36).slice(2)}`
    },
    emptyRow() {
      return this.emptyRowForFields(this.fields)
    },
    fieldByColumn(column, fields = this.fields) {
      return (fields || []).find(field => field.column === column)
    },
    hasDefault(field) {
      return Object.prototype.hasOwnProperty.call(field, 'default') && field.default !== null
    },
    emptyRowForFields(fields) {
      return fields.reduce(
        (row, field) => {
          row[field.column] = this.emptyValue(field)

          return row
        },
        {_key: this.makeKey()},
      )
    },
    emptyValue(field) {
      if (this.hasDefault(field)) {
        return field.default
      }

      if (field.multiple) {
        return []
      }

      if (['checkbox', 'switcher'].includes(field.type)) {
        return false
      }

      if (field.type === 'number') {
        return null
      }

      if (
        field.type === 'select' &&
        !field.nullable &&
        Array.isArray(field.options) &&
        field.options.length > 0
      ) {
        return String(field.options[0].value)
      }

      if (field.type === 'json') {
        return []
      }

      return ''
    },
    normalizeRows(value) {
      if (typeof value === 'string' && value !== '') {
        try {
          value = JSON.parse(value)
        } catch (error) {
          value = []
        }
      }

      if (this.keyValue && value && !Array.isArray(value) && typeof value === 'object') {
        const keyField = this.fields[0]
        const valueField = this.fields[1]

        if (!keyField || !valueField) {
          return []
        }

        return Object.entries(value).map(([key, itemValue]) =>
          this.normalizeRow({
            [keyField.column]: key,
            [valueField.column]: itemValue,
          }),
        )
      }

      if (this.onlyValue && Array.isArray(value)) {
        const valueField = this.fields[0]

        if (!valueField) {
          return []
        }

        return value.map(itemValue => {
          const row =
            itemValue && !Array.isArray(itemValue) && typeof itemValue === 'object'
              ? itemValue
              : {[valueField.column]: itemValue}

          return this.normalizeRow(row)
        })
      }

      if (this.objectMode && value && !Array.isArray(value) && typeof value === 'object') {
        return [this.normalizeRow(value)]
      }

      if (this.objectMode && Array.isArray(value)) {
        return value.map(row => this.normalizeRow(row))
      }

      return (Array.isArray(value) ? value : []).map(row => this.normalizeRow(row))
    },
    normalizeRow(row = {}) {
      const source = row && typeof row === 'object' ? row : {}

      return this.normalizeRowForFields(source, this.fields)
    },
    normalizeRowForFields(row = {}, fields = []) {
      const source = row && typeof row === 'object' ? row : {}

      return fields.reduce(
        (normalized, field) => {
          normalized[field.column] = this.normalizeValue(source[field.column], field)

          return normalized
        },
        {_key: source._key || this.makeKey()},
      )
    },
    normalizeValue(value, field) {
      if ((value === undefined || value === null) && this.hasDefault(field)) {
        value = field.default
      }

      if (field.type === 'json') {
        return this.normalizeJsonValue(value, field)
      }

      if (field.multiple) {
        return Array.isArray(value) ? value.map(item => String(item)) : []
      }

      if (['checkbox', 'switcher'].includes(field.type)) {
        return Boolean(value)
      }

      if (field.type === 'select') {
        return this.normalizeSelectValue(value, field)
      }

      if (field.type === 'number') {
        return value === undefined || value === null || value === '' ? null : Number(value)
      }

      return value === undefined || value === null ? '' : String(value)
    },
    normalizeSelectValue(value, field) {
      if ((value === undefined || value === null || value === '') && this.hasDefault(field)) {
        return String(field.default)
      }

      if (
        !field.nullable &&
        (value === undefined || value === null || value === '') &&
        Array.isArray(field.options) &&
        field.options.length > 0
      ) {
        return String(field.options[0].value)
      }

      return value === undefined || value === null ? '' : String(value)
    },
    normalizeJsonValue(value, field) {
      if (typeof value === 'string' && value !== '') {
        try {
          value = JSON.parse(value)
        } catch (error) {
          value = []
        }
      }

      if (field.keyValue && value && !Array.isArray(value) && typeof value === 'object') {
        const keyField = field.fields?.[0]
        const valueField = field.fields?.[1]

        if (!keyField || !valueField) {
          return []
        }

        return Object.entries(value).map(([key, itemValue]) =>
          this.normalizeRowForFields(
            {
              [keyField.column]: key,
              [valueField.column]: itemValue,
            },
            field.fields || [],
          ),
        )
      }

      if (field.onlyValue && Array.isArray(value)) {
        const valueField = field.fields?.[0]

        if (!valueField) {
          return []
        }

        return value.map(itemValue => {
          const row =
            itemValue && !Array.isArray(itemValue) && typeof itemValue === 'object'
              ? itemValue
              : {[valueField.column]: itemValue}

          return this.normalizeRowForFields(row, field.fields || [])
        })
      }

      if (field.objectMode && value && !Array.isArray(value) && typeof value === 'object') {
        return [this.normalizeRowForFields(value, field.fields || [])]
      }

      if (field.objectMode && Array.isArray(value)) {
        return value.map(row => this.normalizeRowForFields(row, field.fields || []))
      }

      return (Array.isArray(value) ? value : []).map(row =>
        this.normalizeRowForFields(row, field.fields || []),
      )
    },
    serialize() {
      if (this.keyValue) {
        return this.rows.reduce((data, row) => {
          const keyField = this.fields[0]
          const valueField = this.fields[1]

          if (!keyField || !valueField) {
            return data
          }

          const key = this.normalizeValue(row[keyField.column], keyField)

          if (key === '' && this.shouldFilterEmpty()) {
            return data
          }

          data[key] = this.serializeValue(row[valueField.column], valueField)

          return data
        }, {})
      }

      if (this.onlyValue) {
        const valueField = this.fields[0]

        if (!valueField) {
          return []
        }

        const values = this.rows.map(row => this.serializeValue(row[valueField.column], valueField))

        return this.filterSerializedValues(values)
      }

      if (this.objectMode && this.rows.length <= 1) {
        const row = this.rows[0]

        if (!row) {
          return {}
        }

        const data = this.fields.reduce((data, field) => {
          data[field.column] = this.serializeValue(row[field.column], field)

          return data
        }, {})

        return this.shouldFilterEmpty() && this.isEmptyValue(data) ? {} : data
      }

      return this.filterSerializedRows(
        this.rows.map(row => {
          return this.fields.reduce((data, field) => {
            data[field.column] = this.serializeValue(row[field.column], field)

            return data
          }, {})
        }),
      )
    },
    serializeValue(value, field) {
      if (field.type !== 'json') {
        return this.normalizeValue(value, field)
      }

      return this.serializeJsonValue(value, field)
    },
    serializeJsonValue(value, field) {
      const rows = this.normalizeJsonValue(value, field)

      if (field.keyValue) {
        const keyField = field.fields?.[0]
        const valueField = field.fields?.[1]

        if (!keyField || !valueField) {
          return {}
        }

        return rows.reduce((data, row) => {
          const key = this.normalizeValue(row[keyField.column], keyField)

          if (key === '' && this.shouldFilterEmpty(field)) {
            return data
          }

          data[key] = this.serializeValue(row[valueField.column], valueField)

          return data
        }, {})
      }

      if (field.onlyValue) {
        const valueField = field.fields?.[0]

        if (!valueField) {
          return []
        }

        const values = rows.map(row => this.serializeValue(row[valueField.column], valueField))

        return this.filterSerializedValues(values, field)
      }

      if (field.objectMode && rows.length <= 1) {
        const row = rows[0]

        if (!row) {
          return {}
        }

        const data = (field.fields || []).reduce((data, nestedField) => {
          data[nestedField.column] = this.serializeValue(row[nestedField.column], nestedField)

          return data
        }, {})

        return this.shouldFilterEmpty(field) && this.isEmptyValue(data) ? {} : data
      }

      return this.filterSerializedRows(
        rows.map(row => {
          return (field.fields || []).reduce((data, nestedField) => {
            data[nestedField.column] = this.serializeValue(row[nestedField.column], nestedField)

            return data
          }, {})
        }),
        field,
      )
    },
    filterSerializedRows(rows, field = null) {
      if (!this.shouldFilterEmpty(field)) {
        return rows
      }

      return rows.filter(row => !this.isEmptyValue(row))
    },
    filterSerializedValues(values, field = null) {
      if (!this.shouldFilterEmpty(field)) {
        return values
      }

      return values.filter(value => !this.isEmptyValue(value))
    },
    shouldFilterEmpty(field = null) {
      return field === null ? this.filterEmpty : field.filterEmpty !== false
    },
    isEmptyValue(value) {
      if (value === undefined || value === null || value === '') {
        return true
      }

      if (Array.isArray(value)) {
        return value.length === 0 || value.every(item => this.isEmptyValue(item))
      }

      if (typeof value === 'object') {
        const entries = Object.entries(value).filter(([key]) => key !== '_key')

        return entries.length === 0 || entries.every(([, item]) => this.isEmptyValue(item))
      }

      return false
    },
    sync() {
      this.payload = JSON.stringify(this.serialize())
    },
    syncControls() {
      this.$root.querySelectorAll('[data-json-row-path]').forEach(control => {
        const value = this.valueByPath(control.dataset.jsonRowPath)

        if (value === undefined) {
          return
        }

        this.syncControl(control, value)
      })
    },
    valueByPath(path) {
      return (path || '').split('.').reduce((value, segment) => {
        if (value === undefined || value === null) {
          return undefined
        }

        return value[segment]
      }, this.rows)
    },
    syncControl(control, value) {
      if (control.tomselect) {
        control.tomselect.setValue(value, true)

        return
      }

      if (control.type === 'checkbox') {
        control.checked = Boolean(value)

        return
      }

      control.value = Array.isArray(value) ? value.map(item => String(item)) : String(value ?? '')
    },
    canAdd(rows = this.rows, field = null) {
      const limit = field === null ? this.creatableLimit : field.creatableLimit

      return limit === null || limit === undefined || rows.length < limit
    },
    add() {
      if (!this.creatable || !this.canAdd()) {
        return
      }

      this.rows.push(this.emptyRow())
      this.sync()
      this.$nextTick(() => this.syncControls())
    },
    remove(index) {
      if (!this.removable) {
        return
      }

      this.rows.splice(index, 1)
      this.sync()
    },
    addNested(row, field) {
      const rows = this.nestedRows(row, field)

      if (field.creatable === false || !this.canAdd(rows, field)) {
        return
      }

      rows.push(this.emptyRowForFields(field.fields || []))
      this.sync()
      this.$nextTick(() => this.syncControls())
    },
    removeNested(row, field, index) {
      if (!field.removable) {
        return
      }

      this.nestedRows(row, field).splice(index, 1)
      this.sync()
    },
    nestedRows(row, field) {
      if (!Array.isArray(row[field.column])) {
        row[field.column] = this.normalizeJsonValue(row[field.column], field)
      }

      return row[field.column]
    },
    isDraggingRows(rows) {
      return this.draggingRows === rows
    },
    ghostStyle() {
      if (!this.draggingHeight) {
        return {}
      }

      return {
        height: `${this.draggingHeight}px`,
        minHeight: `${this.draggingHeight}px`,
      }
    },
    dragStart(event, index, rows = this.rows, reorderable = this.reorderable) {
      if (!reorderable || event.button !== 0) {
        return
      }

      const draggedRow = event.currentTarget.closest('.json-field__row')

      this.draggingIndex = index
      this.draggingRows = rows
      this.dropIndex = index
      this.dragPointerId = event.pointerId
      this.dragContainer = event.currentTarget.closest('.json-field__rows')
      this.draggingHeight = draggedRow ? Math.ceil(draggedRow.getBoundingClientRect().height) : null
      this.pointerMoveHandler = pointerEvent => this.handlePointerMove(pointerEvent)
      this.pointerUpHandler = pointerEvent => this.handlePointerUp(pointerEvent)
      this.pointerCancelHandler = pointerEvent => this.handlePointerCancel(pointerEvent)

      event.currentTarget.setPointerCapture?.(event.pointerId)
      window.addEventListener('pointermove', this.pointerMoveHandler)
      window.addEventListener('pointerup', this.pointerUpHandler, {once: true})
      window.addEventListener('pointercancel', this.pointerCancelHandler, {once: true})
    },
    dragStartNested(event, row, field, index) {
      if (!field.reorderable) {
        return
      }

      this.dragStart(event, index, this.nestedRows(row, field), true)
    },
    handlePointerMove(event) {
      if (this.draggingIndex === null || event.pointerId !== this.dragPointerId) {
        return
      }

      event.preventDefault()
      this.pendingClientY = event.clientY

      if (this.pointerMoveFrame !== null) {
        return
      }

      this.pointerMoveFrame = requestAnimationFrame(() => {
        this.updateDropIndex(this.pendingClientY)
        this.pointerMoveFrame = null
      })
    },
    handlePointerUp(event) {
      if (event.pointerId !== this.dragPointerId) {
        return
      }

      this.drop(this.dropIndex)
    },
    handlePointerCancel(event) {
      if (event.pointerId !== this.dragPointerId) {
        return
      }

      this.dragEnd()
    },
    updateDropIndex(clientY) {
      if (!this.dragContainer || this.draggingIndex === null) {
        return
      }

      const rows = [
        ...this.dragContainer.querySelectorAll(
          ':scope > .json-field__item > .json-field__row[data-json-row-index]',
        ),
      ]
      let nextDropIndex = this.draggingRows?.length || 0

      for (const row of rows) {
        const index = Number(row.dataset.jsonRowIndex)

        if (index === this.draggingIndex) {
          continue
        }

        const rect = row.getBoundingClientRect()

        if (clientY < rect.top + rect.height / 2) {
          nextDropIndex = index

          break
        }
      }

      this.moveDropIndex(nextDropIndex)
    },
    moveDropIndex(index) {
      if (index === this.dropIndex) {
        return
      }

      const previousRects = this.dragItemRects()

      this.dropIndex = index

      this.$nextTick(() => {
        if (!this.dragContainer || this.dropIndex !== index) {
          return
        }

        this.animateDragItems(previousRects)
      })
    },
    dragItemRects() {
      return this.dragItemElements().reduce((rects, element) => {
        rects.set(element.dataset.jsonRowIndex, element.getBoundingClientRect())

        return rects
      }, new Map())
    },
    dragItemElements() {
      if (!this.dragContainer) {
        return []
      }

      return [
        ...this.dragContainer.querySelectorAll(
          ':scope > .json-field__item > .json-field__row[data-json-row-index]:not(.json-field__row--dragging)',
        ),
      ]
    },
    animateDragItems(previousRects) {
      this.dragAnimations.forEach(animation => animation.cancel())
      this.dragAnimations = []

      this.dragItemElements().forEach(element => {
        const previousRect = previousRects.get(element.dataset.jsonRowIndex)

        if (!previousRect) {
          return
        }

        const currentRect = element.getBoundingClientRect()
        const deltaY = previousRect.top - currentRect.top

        if (Math.abs(deltaY) < 1) {
          return
        }

        const animation = element.animate(
          [{transform: `translateY(${deltaY}px)`}, {transform: 'translateY(0)'}],
          {
            duration: 180,
            easing: 'cubic-bezier(0.2, 0, 0, 1)',
          },
        )

        this.dragAnimations.push(animation)
      })
    },
    drop(index = this.dropIndex) {
      if (!this.draggingRows || this.draggingIndex === null || index === null) {
        return
      }

      const fromIndex = this.draggingIndex
      let targetIndex = index

      if (targetIndex > fromIndex) {
        targetIndex -= 1
      }

      if (targetIndex === fromIndex) {
        this.dragEnd()

        return
      }

      const [row] = this.draggingRows.splice(fromIndex, 1)
      this.draggingRows.splice(targetIndex, 0, row)

      this.dragEnd()
      this.sync()
    },
    dragEnd() {
      if (this.pointerMoveHandler) {
        window.removeEventListener('pointermove', this.pointerMoveHandler)
      }

      if (this.pointerUpHandler) {
        window.removeEventListener('pointerup', this.pointerUpHandler)
      }

      if (this.pointerCancelHandler) {
        window.removeEventListener('pointercancel', this.pointerCancelHandler)
      }

      if (this.pointerMoveFrame !== null) {
        cancelAnimationFrame(this.pointerMoveFrame)
      }

      this.dragAnimations.forEach(animation => animation.cancel())

      this.draggingIndex = null
      this.draggingRows = null
      this.dropIndex = null
      this.dragPointerId = null
      this.dragContainer = null
      this.draggingHeight = null
      this.pendingClientY = null
      this.pointerMoveFrame = null
      this.dragAnimations = []
      this.pointerMoveHandler = null
      this.pointerUpHandler = null
      this.pointerCancelHandler = null
    },
  }
}
