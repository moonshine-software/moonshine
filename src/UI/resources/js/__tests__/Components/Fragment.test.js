import {afterEach, expect, test} from '@jest/globals'
import axios from 'axios'
import MockAdapter from 'axios-mock-adapter'
import fragment from '../../Components/Fragment.js'

let mockAxios

afterEach(() => {
  mockAxios?.restore()
  document.body.innerHTML = ''
})

test.each([undefined, {detail: {page: 2}}])(
  'updates the fragment with or without an Alpine event (%j)',
  async event => {
    document.body.innerHTML = '<div id="fragment">Original content</div>'
    const component = fragment('/fragment')
    component.$el = document.querySelector('#fragment')
    component.$root = component.$el
    component.$event = event
    component.init()

    mockAxios = new MockAdapter(axios)
    const url = event ? '/fragment?page=2' : '/fragment'
    mockAxios.onGet(url).reply(200, '<div id="fragment">Updated content</div>')

    await component.fragmentUpdate()
    await new Promise(resolve => setTimeout(resolve, 0))

    expect(mockAxios.history.get).toHaveLength(1)
    expect(mockAxios.history.get[0].url).toBe(url)
    expect(document.querySelector('#fragment').textContent).toBe('Updated content')
    expect(component.loading).toBe(false)
  },
)
