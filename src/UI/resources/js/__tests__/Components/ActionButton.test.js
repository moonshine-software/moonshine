import axios from 'axios'
import MockAdapter from 'axios-mock-adapter'
import {afterEach, beforeEach, expect, jest, test} from '@jest/globals'
import actionButton from '../../Components/ActionButton.js'

let mockAxios
let toggled

const onToggle = () => toggled++
// dispatchEvents() fires response events on a zero-delay timer, after request() settles
const dispatched = () => new Promise(resolve => setTimeout(resolve))

beforeEach(() => {
  mockAxios = new MockAdapter(axios)
  toggled = 0
  window.addEventListener('modal_toggled:resource-edit-modal', onToggle)

  document.body.innerHTML = `
    <div id="resource-edit-modal"></div>
    <a href="http://localhost/resource/item/1/edit"
       data-async-method="get"
       data-async-selector="#resource-edit-modal"
       data-async-events="modal_toggled:resource-edit-modal"
       data-without-loading="1">Edit</a>`
})

afterEach(() => {
  window.removeEventListener('modal_toggled:resource-edit-modal', onToggle)
  mockAxios.restore()
  document.body.innerHTML = ''
})

function makeButton() {
  const component = actionButton()
  component.$el = document.querySelector('a')
  component.$watch = jest.fn()
  component.init()

  return component
}

test('a double click on an async modal button sends one request and toggles the modal once', async () => {
  mockAxios.onGet('http://localhost/resource/item/1/edit').reply(200, '<form>Item 1</form>')
  const button = makeButton()

  await Promise.all([button.request(), button.request()])
  await dispatched()

  expect(mockAxios.history.get.length).toBe(1)
  expect(toggled).toBe(1)
  expect(document.querySelector('#resource-edit-modal').innerHTML).toBe('<form>Item 1</form>')
})

test('an async button accepts a new click once its request has finished', async () => {
  mockAxios.onGet('http://localhost/resource/item/1/edit').reply(200, '<form>Item 1</form>')
  const button = makeButton()

  await button.request()
  await button.request()
  await dispatched()

  expect(mockAxios.history.get.length).toBe(2)
  expect(toggled).toBe(2)
})

test('an async button accepts a new click after a failed request', async () => {
  mockAxios.onGet('http://localhost/resource/item/1/edit').networkErrorOnce()
  const button = makeButton()

  await button.request()

  mockAxios.onGet('http://localhost/resource/item/1/edit').reply(200, '<form>Item 1</form>')
  await button.request()
  await dispatched()

  expect(mockAxios.history.get.length).toBe(2)
  expect(toggled).toBe(1)
})
