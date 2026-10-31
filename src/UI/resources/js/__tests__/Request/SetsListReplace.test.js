import {afterEach, beforeAll, expect, jest, test} from '@jest/globals'

const axios = jest.fn()

jest.unstable_mockModule('axios', () => ({default: axios}))

let listComponentRequest

beforeAll(async () => {
  listComponentRequest = (await import('../../Request/Sets.js')).listComponentRequest
})

afterEach(() => {
  document.body.innerHTML = ''
  jest.restoreAllMocks()
})

test('replaces the list with the parsed response nodes without parsing them again', async () => {
  document.body.innerHTML = `<div class="container">
    <div class="js-table-builder-wrapper"><table><tbody><tr data-row-key="1"><td>Old</td></tr></tbody></table></div>
  </div>`

  axios.mockResolvedValue({
    status: 200,
    headers: {},
    data: `<div class="js-table-builder-container"><div class="js-table-builder-wrapper" data-page="2"><table><tbody><tr data-row-key="26"><td>New</td></tr></tbody></table><template><span>Row</span></template></div></div>`,
  })

  const outerHTML = Object.getOwnPropertyDescriptor(Element.prototype, 'outerHTML')
  const outerHTMLSetter = jest.fn()
  jest.spyOn(Element.prototype, 'outerHTML', 'set').mockImplementation(function (value) {
    outerHTMLSetter(value)
    outerHTML.set.call(this, value)
  })

  const root = document.querySelector('.js-table-builder-wrapper')
  const component = {
    $event: {preventDefault: jest.fn(), detail: {}},
    $el: root,
    $root: root,
    asyncUrl: '/admin/component/index-page?page=2',
    loading: false,
  }

  await listComponentRequest(component)
  await new Promise(resolve => setTimeout(resolve, 0))

  const wrappers = document.querySelectorAll('.js-table-builder-wrapper')

  expect(wrappers).toHaveLength(1)
  expect(wrappers[0].dataset.page).toBe('2')
  expect(wrappers[0].querySelector('tr').dataset.rowKey).toBe('26')
  expect(wrappers[0].querySelector('template').content.textContent).toBe('Row')
  expect(root.isConnected).toBe(false)
  expect(outerHTMLSetter).not.toHaveBeenCalled()
  expect(component.loading).toBe(false)
})

test('a newer list request cancels the one in flight and its result is applied', async () => {
  document.body.innerHTML = `<div class="container">
    <div class="js-table-builder-wrapper"><table><tbody><tr data-row-key="1"><td>Old</td></tr></tbody></table></div>
  </div>`

  let firstSignal
  axios.mockImplementationOnce(
    ({signal}) =>
      new Promise((resolve, reject) => {
        firstSignal = signal
        signal.addEventListener('abort', () => reject(new Error('canceled')))
      }),
  )
  axios.mockResolvedValueOnce({
    status: 200,
    headers: {},
    data: `<div class="js-table-builder-container"><div class="js-table-builder-wrapper" data-filtered="1"><table><tbody><tr data-row-key="3"><td>Filtered</td></tr></tbody></table></div></div>`,
  })

  const toast = jest.fn()
  const originalMoonShine = globalThis.MoonShine
  globalThis.MoonShine = {
    ...originalMoonShine,
    ui: {toast},
    config: () => ({isForceRelativeUrls: () => false}),
  }

  const root = document.querySelector('.js-table-builder-wrapper')
  const component = {
    $event: {preventDefault: jest.fn(), detail: {}},
    $el: root,
    $root: root,
    asyncUrl: 'https://admin.test/admin/component/index-page',
    loading: false,
  }
  const staleAfter = jest.fn()
  const latestAfter = jest.fn()

  listComponentRequest(component, false, staleAfter)
  listComponentRequest(component, false, latestAfter)
  await new Promise(resolve => setTimeout(resolve, 0))

  globalThis.MoonShine = originalMoonShine

  expect(firstSignal.aborted).toBe(true)
  expect(document.querySelector('.js-table-builder-wrapper').dataset.filtered).toBe('1')
  expect(document.querySelector('tr').dataset.rowKey).toBe('3')
  expect(component.loading).toBe(false)
  expect(toast).not.toHaveBeenCalled()
  expect(staleAfter).not.toHaveBeenCalled()
  expect(latestAfter).toHaveBeenCalledTimes(1)
})
