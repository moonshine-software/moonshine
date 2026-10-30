import {afterEach, expect, jest, test} from '@jest/globals'
import belongsToMany from '../../Components/BelongsToMany.js'

afterEach(() => {
  document.body.innerHTML = ''
})

test.each([
  [undefined, false],
  ['true', false],
  ['false', true],
])('outputs selected search labels by the escaping preference (%s)', (escapeLabels, isHtml) => {
  const attribute = escapeLabels === undefined ? '' : ` data-escape-labels="${escapeLabels}"`

  document.body.innerHTML = `
    <div id="root">
      <div class="js-pivot-table" data-table-name="Categories"${attribute}>
        <table><tbody><tr>
          <td class="js-pivot-title"></td>
          <td><input type="checkbox" class="js-pivot-checker"></td>
        </tr></tbody></table>
      </div>
    </div>
  `

  const component = belongsToMany()
  component.$root = document.querySelector('#root')
  component.$dispatch = jest.fn()

  component.select({label: '<img src=x onerror=alert(1)>', value: 5})

  const title = document.querySelector('.js-pivot-title')

  expect(title.querySelector('img') !== null).toBe(isHtml)
  expect(title.textContent).toBe(isHtml ? '' : '<img src=x onerror=alert(1)>')
  expect(document.querySelector('.js-pivot-checker').value).toBe('5')
})
