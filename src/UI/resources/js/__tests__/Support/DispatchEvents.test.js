import {dispatchEvents} from '../../Support/DispatchEvents'
import {afterEach, beforeEach, describe, expect, it, jest} from '@jest/globals'

describe('dispatchEvents', () => {
  let component

  beforeEach(() => {
    jest.clearAllMocks()
    jest.useFakeTimers()

    jest.spyOn(global, 'CustomEvent').mockImplementation((type, eventInit) => {
      return {type, ...eventInit.detail}
    })

    component = {
      $el: {
        tagName: 'TR',
        closest: jest.fn().mockReturnValue({
          dataset: {
            rowKey: '123',
          },
        }),
      },
    }
  })

  afterEach(() => {
    jest.useRealTimers()
  })

  it('should return early if events is not provided', () => {
    dispatchEvents(null, 'eventType', component)
    expect(mockDispatchEvents()).not.toHaveBeenCalled()
  })

  it('should return early if events is not a string', () => {
    dispatchEvents([], 'eventType', component)
    expect(mockDispatchEvents()).not.toHaveBeenCalled()
  })

  it('delayed dispatch', () => {
    dispatchEvents('somevent|_delay~500', 'eventType', component)

    expect(mockDispatchEvents()).not.toHaveBeenCalled()

    jest.advanceTimersByTime(500)

    expect(mockDispatchEvents()).toHaveBeenCalled()
  })

  it('should dispatch events correctly', () => {
    const events = 'click|param1~value1;param2~value2,hover|param3~value3'
    dispatchEvents(events, 'eventType', component, {extraParam: 'extraValue'})

    jest.runAllTimers()

    expect(mockDispatchEvents()).toHaveBeenCalledTimes(2)
    expect(mockDispatchEvents()).toHaveBeenNthCalledWith(
      1,
      expectEvent('click', {
        extraParam: 'extraValue',
        param1: 'value1',
        param2: 'value2',
      }),
    )

    expect(mockDispatchEvents()).toHaveBeenNthCalledWith(
      2,
      expectEvent('hover', {
        extraParam: 'extraValue',
        param3: 'value3',
      }),
    )
  })

  it('should not dispatch events if type is error', () => {
    dispatchEvents('click', 'error', component)

    jest.runAllTimers()

    expect(mockDispatchEvents()).not.toHaveBeenCalled()
  })

  function expectEvent(type, detail = {}) {
    return {
      type,
      ...detail,
    }
  }

  function mockDispatchEvents() {
    return jest.spyOn(global, 'dispatchEvent').mockImplementation(() => true)
  }
})
