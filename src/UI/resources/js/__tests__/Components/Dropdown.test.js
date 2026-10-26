import {afterEach, beforeAll, expect, jest, test} from '@jest/globals'

const update = jest.fn()
const createPopper = jest.fn(() => ({update}))

jest.unstable_mockModule('@popperjs/core', () => ({createPopper}))

let dropdown

beforeAll(async () => {
  dropdown = (await import('../../Components/Dropdown.js')).default
})

afterEach(() => {
  document.body.innerHTML = ''
  jest.clearAllMocks()
})

test('creates the popper on first open and updates it afterwards', () => {
  document.body.innerHTML = `<div class="dropdown">
    <button class="dropdown-btn">Open</button>
    <div class="dropdown-body">Menu</div>
  </div>`

  const component = dropdown()
  component.$root = document.querySelector('.dropdown')
  component.$el = component.$root
  component.init()

  expect(createPopper).not.toHaveBeenCalled()

  component.toggleDropdown()

  expect(createPopper).toHaveBeenCalledTimes(1)
  expect(createPopper.mock.calls[0][0]).toBe(component.dropdownBtn)
  expect(createPopper.mock.calls[0][1]).toBe(component.dropdownBody)
  expect(component.dropdownBody.classList.contains('_is-active')).toBe(true)

  component.toggleDropdown()
  component.toggleDropdown()

  expect(createPopper).toHaveBeenCalledTimes(1)
  expect(update).toHaveBeenCalledTimes(2)
})
