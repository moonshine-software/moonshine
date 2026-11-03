import {afterEach, beforeEach, expect, jest, test} from '@jest/globals'
import toasts from '../../Components/Toast.js'

let component

beforeEach(() => {
  jest.useFakeTimers()
  component = toasts()
})

afterEach(() => {
  jest.useRealTimers()
})

test('closing a toast by hand keeps the others when its timer fires', () => {
  component.add({type: 'success', text: 'A', duration: 1500})
  jest.advanceTimersByTime(1)
  component.add({type: 'success', text: 'B', duration: 8000})
  const [a] = component.toasts

  component.remove(a.id)
  jest.advanceTimersByTime(1600)

  expect(component.visible.map(toast => toast.text)).toEqual(['B'])
})

test('toasts added in the same millisecond get distinct ids', () => {
  const now = jest.spyOn(Date, 'now').mockReturnValue(1700000000000)

  component.add({type: 'success', text: 'A', duration: 0})
  component.add({type: 'error', text: 'B', duration: 0})

  now.mockRestore()

  const [a, b] = component.toasts
  expect(a.id).not.toBe(b.id)
  expect(component.visible.map(toast => toast.text)).toEqual(['A', 'B'])

  component.remove(b.id)
  expect(component.visible.map(toast => toast.text)).toEqual(['A'])
})
