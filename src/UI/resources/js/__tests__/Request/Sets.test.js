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

const component = (asyncUrl = '/list') => ({
  $event: {preventDefault: jest.fn(), detail: {}},
  $el: {href: null},
  $root: document.getElementById('list'),
  asyncUrl: asyncUrl,
  loading: false,
})

it('should call preventDefault and set loading to true', () => {
  const c = component('/example')

  listComponentRequest(c)

  expect(c.$event.preventDefault).toHaveBeenCalled()
  expect(c.loading).toBe(true)
})

describe('listComponentRequest race', () => {
  it('should abort the previous request of the same component', () => {
    const c = component()

    listComponentRequest(c)
    const first = c.listRequestController

    c.$event.detail = {}
    listComponentRequest(c)

    expect(first.signal.aborted).toBe(true)
    expect(c.listRequestController.signal.aborted).toBe(false)
  })

  it('should render only the latest response when an earlier one is slower', async () => {
    mockAxios.reset()
    mockAxios
      .onGet(/query-tag=slow/)
      .reply(
        () =>
          new Promise(resolve =>
            setTimeout(() => resolve([200, '<div><div id="list">slow</div></div>']), 50),
          ),
      )
    mockAxios.onGet(/query-tag=fast/).reply(200, '<div><div id="list">fast</div></div>')

    const c = component()
    const after = jest.fn()

    c.$event.detail = {queryTag: 'query-tag=slow'}
    listComponentRequest(c, false, after)

    c.$event.detail = {queryTag: 'query-tag=fast'}
    listComponentRequest(c, false, after)

    await new Promise(resolve => setTimeout(resolve, 100))

    expect(document.body.textContent).toBe('fast')
    expect(after).toHaveBeenCalledTimes(1)
    expect(MoonShine.ui.toast).not.toHaveBeenCalled()
  })
})
