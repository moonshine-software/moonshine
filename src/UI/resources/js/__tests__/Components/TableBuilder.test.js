import {afterEach, expect, test} from '@jest/globals'
import tableBuilder from '../../Components/TableBuilder.js'

afterEach(() => {
  document.body.innerHTML = ''
})

test('measures all list cells before setting their width classes', () => {
  document.body.innerHTML = `<table class="table-list">
    <thead><tr><th data-width="40">Title</th><th data-width="200">Text</th></tr></thead>
    <tbody><tr>
      <td data-width="40">First</td>
      <td data-width="200"><table><tbody><tr><td data-width="10">Nested</td></tr></tbody></table></td>
    </tr></tbody>
  </table>`

  const operations = []

  document.querySelectorAll('th, td').forEach(cell => {
    Object.defineProperty(cell, 'clientWidth', {get: () => 100})
    Object.defineProperty(cell, 'scrollWidth', {
      get: () => {
        operations.push('read')

        return Number(cell.dataset.width)
      },
    })

    const add = cell.classList.add.bind(cell.classList)
    cell.classList.add = (...classes) => {
      operations.push('write')
      add(...classes)
    }
  })

  const component = tableBuilder()
  component.table = document.querySelector('table')
  component.initCellWidth()

  const [title, text, first, textCell, nested] = document.querySelectorAll('th, td')

  expect(operations).toEqual(['read', 'read', 'read', 'read', 'write', 'write', 'write', 'write'])
  expect(title.className).toBe('fit-content')
  expect(text.className).toBe('min-content')
  expect(first.className).toBe('fit-content')
  expect(textCell.className).toBe('min-content')
  expect(nested.className).toBe('')
})
