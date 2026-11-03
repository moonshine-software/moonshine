import {listComponentRequest} from '../../Request/Sets.js'
import axios from 'axios'
import MockAdapter from 'axios-mock-adapter'
import {afterEach, beforeEach, describe, expect, it, jest} from '@jest/globals'

let mockAxios

beforeEach(() => {
  mockAxios = new MockAdapter(axios)
  mockAxios.onGet().reply(200, '<div><div id="list">new</div></div>')
  document.body.innerHTML = '<div id="list">old</div>'
  jest.clearAllMocks()
})

afterEach(() => {
  mockAxios.restore()
})

// Lets the mocked responses and their handlers run to the end
const settled = () => new Promise(resolve => setTimeout(resolve))

const listComponent = (asyncUrl = '/list') => ({
  $event: {preventDefault: jest.fn(), detail: {}},
  $el: {href: null},
  $root: document.getElementById('list'),
  asyncUrl: asyncUrl,
  loading: false,
  listRequestController: null,
})

it('should call preventDefault and set loading to true', () => {
  const list = listComponent('/example')

  listComponentRequest(list)

  expect(list.$event.preventDefault).toHaveBeenCalled()
  expect(list.loading).toBe(true)
})

describe('listComponentRequest race', () => {
  it('should abort the previous request of the same component', () => {
    const list = listComponent()

    listComponentRequest(list)
    const first = list.listRequestController

    list.$event.detail = {}
    listComponentRequest(list)

    expect(first.signal.aborted).toBe(true)
    expect(list.listRequestController.signal.aborted).toBe(false)
  })

  it('should render and push only the latest response when an earlier one is slower', async () => {
    let releaseSlow
    const slowResponse = new Promise(resolve => {
      releaseSlow = () => resolve([200, '<div><div id="list">slow</div></div>'])
    })

    mockAxios.reset()
    mockAxios.onGet(/query-tag=slow/).reply(() => slowResponse)
    mockAxios.onGet(/query-tag=fast/).reply(200, '<div><div id="list">fast</div></div>')

    history.replaceState({}, '', '/')

    const list = listComponent()
    const after = jest.fn()

    list.$event.detail = {queryTag: 'query-tag=slow'}
    listComponentRequest(list, true, after)

    list.$event.detail = {queryTag: 'query-tag=fast'}
    listComponentRequest(list, true, after)

    await settled()
    releaseSlow()
    await settled()

    expect(document.body.textContent).toBe('fast')
    expect(location.search).toBe('?query-tag=fast')
    expect(after).toHaveBeenCalledTimes(1)
    expect(MoonShine.ui.toast).not.toHaveBeenCalled()
  })
})
