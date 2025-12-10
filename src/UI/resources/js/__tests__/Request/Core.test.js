import request, {urlWithQuery} from '../../Request/Core.js'
import {ComponentRequestData} from '../../DTOs/ComponentRequestData.js'
import axios from 'axios'
import MockAdapter from 'axios-mock-adapter'
import {afterEach, beforeEach, describe, expect, jest, it} from '@jest/globals'

// Mock DOM API
document.querySelectorAll = jest.fn()
document.querySelector = jest.fn()

describe('request function', () => {
  let mockAxios // For mocking axios requests
  let t

  beforeEach(() => {
    mockAxios = new MockAdapter(axios)

    jest.clearAllMocks()
    axios.get = jest.fn()

    t = {
      $el: {},
      loading: true,
    }
  })

  afterEach(() => {
    mockAxios.reset()
  })

  it('should return if url is not provided', () => {
    request(t, '')
    expect(t.loading).toBe(false)
    expect(MoonShine.ui.toast).toHaveBeenCalledWith('Request URL not set', 'error')
  })

  it('should instantiate ComponentRequestData if not provided', () => {
    const componentRequestData = null
    request(t, '/test-url', 'get', {}, {}, componentRequestData)
    expect(MoonShine.ui.toast).not.toHaveBeenCalled() // No error toast
  })

  it('should call beforeRequest if specified', () => {
    const componentRequestData = new ComponentRequestData().withBeforeRequest('testCallback')
    jest.spyOn(componentRequestData, 'hasBeforeRequest').mockReturnValueOnce(true)
    MoonShine.callbacks.testCallback = jest.fn()

    request(t, '/test-url', 'get', {}, {}, componentRequestData)

    expect(componentRequestData.hasBeforeRequest).toHaveBeenCalled()
    expect(MoonShine.callbacks.testCallback).toHaveBeenCalledWith(t.$el, t)
  })

  it('should handle successful axios response', async () => {
    const componentRequestData = new ComponentRequestData()
    mockAxios.onGet('/test-url').reply(200, {message: 'Success'})

    await request(t, '/test-url', 'get', {}, {}, componentRequestData)

    expect(t.loading).toBe(false) // Loading should be false after response
    expect(MoonShine.ui.toast).toHaveBeenCalledWith('Success', 'success', null) // Show success toast
  })

  it('should handle fields_values in response', async () => {
    const componentRequestData = new ComponentRequestData()
    mockAxios.onGet('/test-url').reply(200, {
      fields_values: {'#input': 'value'},
    })

    document.querySelector.mockReturnValueOnce({value: '', dispatchEvent: jest.fn()})

    await request(t, '/test-url', 'get', {}, {}, componentRequestData)

    expect(document.querySelector).toHaveBeenCalledWith('#input')
  })

  it('should handle redirects in response', async () => {
    const origWindowLocation = window.location
    delete window.location
    window.location = {assign: jest.fn()}

    // Mock $nextTick to execute callback immediately
    const nextTickMock = jest.fn(callback => {
      callback()
      return Promise.resolve()
    })

    t.$nextTick = nextTickMock

    const componentRequestData = new ComponentRequestData()
    mockAxios.onGet('/test-url').reply(200, {redirect: '/new-location'})

    await request(t, '/test-url', 'get', {}, {}, componentRequestData)

    expect(nextTickMock).toHaveBeenCalled()
    expect(window.location.assign).toHaveBeenCalledWith('/new-location')
    window.location = origWindowLocation
  })

  it('should handle attachments in response', async () => {
    global.URL.createObjectURL = jest.fn()
    global.URL.revokeObjectURL = jest.fn()

    const filename = 'file.txt'
    const data = 'File content'
    const createObjectURLSpy = jest.spyOn(window.URL, 'createObjectURL').mockReturnValue('mock-url')

    const createElementSpy = jest.spyOn(document, 'createElement').mockReturnValue({
      style: {},
      href: '',
      download: '',
      click: jest.fn(),
    })

    jest.spyOn(document.body, 'appendChild').mockReturnValue()

    mockAxios.onGet('/test-url').reply(200, data, {
      'content-disposition': `attachment; filename=${filename}`,
    })

    const componentRequestData = new ComponentRequestData()

    await request(t, '/test-url', 'get', {}, {}, componentRequestData.withResponseType('blob'))

    const anchorElement = createElementSpy.mock.results[0].value
    expect(createObjectURLSpy).toHaveBeenCalledWith(new Blob([data]))

    expect(createElementSpy).toHaveBeenCalledWith('a')
    expect(anchorElement.style.display).toBe('none')
    expect(anchorElement.href).toBe('mock-url')
    expect(anchorElement.download).toBe(filename)
    expect(createElementSpy).toHaveBeenCalledWith('a')
  })

  it('should handle errors in axios response with blob non 200', async () => {
    const componentRequestData = new ComponentRequestData()
    mockAxios.onGet('/test-url').reply(500, {message: 'Error'})

    await request(t, '/test-url', 'get', {}, {}, componentRequestData.withResponseType('blob'))

    expect(t.loading).toBe(false)
    expect(MoonShine.ui.toast).toHaveBeenCalledWith('Error', 'error')
  })

  it('should handle errors in axios response with blob', async () => {
    const errorData = JSON.stringify({message: 'Error'})
    const blob = new Blob([errorData], {type: 'application/json'})

    const componentRequestData = new ComponentRequestData()
    mockAxios.onGet('/test-url').reply(200, {message: 'Error', messageType: 'error'})

    await request(t, '/test-url', 'get', blob, {}, componentRequestData.withResponseType('blob'))

    expect(t.loading).toBe(false)
    expect(MoonShine.ui.toast).toHaveBeenCalledWith('Error', 'error', null)
  })

  it('should handle errors in axios response', async () => {
    const componentRequestData = new ComponentRequestData()
    mockAxios.onGet('/test-url').reply(500, {message: 'Error'})

    await request(t, '/test-url', 'get', {}, {}, componentRequestData)

    expect(t.loading).toBe(false)
    expect(MoonShine.ui.toast).toHaveBeenCalledWith('Error', 'error')
  })

  it('should display "Unknown Error" if no error message is present', async () => {
    mockAxios.onGet('/test-url').networkError()

    await request(t, '/test-url')

    expect(MoonShine.ui.toast).toHaveBeenCalledWith('Unknown Error', 'error')
  })

  it('should call beforeHandleResponse if specified', async () => {
    const beforeHandleResponseMock = jest.fn()
    const componentRequestData = new ComponentRequestData().withBeforeHandleResponse(
      beforeHandleResponseMock,
    )
    jest.spyOn(componentRequestData, 'hasBeforeHandleResponse').mockReturnValueOnce(true)
    mockAxios.onGet('/test-url').reply(200, {message: 'Success'})

    await request(t, '/test-url', 'get', {}, {}, componentRequestData)

    expect(componentRequestData.hasBeforeHandleResponse).toHaveBeenCalled()
    expect(beforeHandleResponseMock).toHaveBeenCalledWith({message: 'Success'}, t)
  })

  it('should handle successful response with responseHandler', async () => {
    const componentRequestData = new ComponentRequestData().withResponseHandler(
      'testResponseHandler',
    )
    jest.spyOn(componentRequestData, 'hasResponseHandler').mockReturnValueOnce(true)
    MoonShine.callbacks.testResponseHandler = jest.fn()
    mockAxios.onGet('/test-url').reply(200, {message: 'Success'})

    await request(t, '/test-url', 'get', {}, {}, componentRequestData)

    expect(MoonShine.callbacks.testResponseHandler).toHaveBeenCalledWith(
      expect.objectContaining({
        config: expect.any(Object),
        data: {message: 'Success'},
        headers: expect.any(Object),
        request: expect.objectContaining({
          responseURL: '/test-url',
        }),
        status: 200,
      }),
      {},
      componentRequestData.events,
      t,
    )
  })

  it('should handle error response with responseHandler', async () => {
    const componentRequestData = new ComponentRequestData().withResponseHandler(
      'testResponseHandler',
    )
    jest.spyOn(componentRequestData, 'hasResponseHandler').mockReturnValueOnce(true)
    MoonShine.callbacks.testResponseHandler = jest.fn()
    mockAxios.onGet('/test-url').reply(500, {error: 'error'})

    await request(t, '/test-url', 'get', {}, {}, componentRequestData)

    expect(MoonShine.callbacks.testResponseHandler).toHaveBeenCalledWith(
      expect.any(Object),
      {},
      componentRequestData.events,
      t,
    )
  })

  it('should update elements based on response', async () => {
    const content = '<div>New Content</div>'
    const selector = '.test'
    const componentRequestData = new ComponentRequestData().withSelector(selector)
    mockAxios.onGet('/test-url').reply(200, {html: content})

    document.querySelectorAll = jest.fn().mockReturnValue([{innerHTML: ''}])
    await request(t, '/test-url', 'get', {}, {}, componentRequestData)

    expect(document.querySelectorAll).toHaveBeenCalledWith(selector)
    expect(document.querySelectorAll(selector)[0].innerHTML).toBe(content)
  })

  it('should update elements based on response with selector in data', async () => {
    const content = '<div>New Content</div>'

    const selector = '.test'
    mockAxios.onGet('/test-url').reply(200, {htmlData: [{html: content, selector: selector}]})

    document.querySelectorAll = jest.fn().mockReturnValue([{innerHTML: ''}])
    await request(t, '/test-url', 'get')

    expect(document.querySelectorAll).toHaveBeenCalledWith(selector)
    expect(document.querySelectorAll(selector)[0].innerHTML).toBe(content)
  })

  it('should update elements based on response with empty selector', async () => {
    jest.spyOn(console, 'error').mockImplementation(() => {})

    const content = '<div>New Content</div>'

    const selector = ''
    mockAxios.onGet('/test-url').reply(200, {htmlData: [{html: content, selector: selector}]})

    document.querySelectorAll = jest.fn().mockReturnValue([{innerHTML: ''}])
    await request(t, '/test-url', 'get')

    expect(console.error).not.toHaveBeenCalled()
  })

  it('should handle messages in response', async () => {
    const componentRequestData = new ComponentRequestData()
    mockAxios
      .onGet('/test-url')
      .reply(200, {message: 'Test Message', messageType: 'info', messageDuration: 2000})

    await request(t, '/test-url', 'get', {}, {}, componentRequestData)

    expect(MoonShine.ui.toast).toHaveBeenCalledWith('Test Message', 'info', 2000)
  })

  it('should call afterResponse if specified', async () => {
    const testFn = jest.fn()
    const componentRequestData = new ComponentRequestData().withAfterResponse(testFn)
    mockAxios.onGet('/test-url').reply(200, {message: 'Success'})

    await request(t, '/test-url', 'get', {}, {}, componentRequestData)

    expect(componentRequestData.hasAfterResponse()).toBe(true)
    expect(testFn).toHaveBeenCalledWith({message: 'Success'}, 'success', t)
  })

  it('should handle errors correctly', async () => {
    const componentRequestData = new ComponentRequestData()
    mockAxios.onGet('/test-url').reply(500, {message: 'Server Error'})

    await request(t, '/test-url', 'get', {}, {}, componentRequestData)

    expect(t.loading).toBe(false)
    expect(MoonShine.ui.toast).toHaveBeenCalledWith('Server Error', 'error')
  })

  it('should call errorCallback if specified', async () => {
    const testFn = jest.fn()
    const componentRequestData = new ComponentRequestData().withErrorCallback(testFn)
    jest.spyOn(componentRequestData, 'hasErrorCallback').mockReturnValueOnce(true)
    mockAxios.onGet('/test-url').reply(500, {message: 'Server Error'})

    await request(t, '/test-url', 'get', {}, {}, componentRequestData)

    expect(componentRequestData.hasErrorCallback()).toBe(true)
    expect(testFn).toHaveBeenCalledWith({message: 'Server Error'}, t)
  })

  it('should handle unknown errors', async () => {
    const componentRequestData = new ComponentRequestData()
    mockAxios.onGet('/test-url').reply(500)
    const consoleErrorSpy = jest.spyOn(console, 'error').mockImplementation(() => {})

    await request(t, '/test-url', 'get', {}, {}, componentRequestData)

    expect(t.loading).toBe(false)
    expect(consoleErrorSpy).toHaveBeenCalled()
    expect(MoonShine.ui.toast).toHaveBeenCalledWith('Unknown Error', 'error')
  })

  it('should set loading to false after completion', async () => {
    const componentRequestData = new ComponentRequestData()
    mockAxios.onGet('/test-url').reply(200, {message: 'Success'})

    await request(t, '/test-url', 'get', {}, {}, componentRequestData)

    expect(t.loading).toBe(false)
  })

  it('should dispatch events if present', async () => {
    jest.useFakeTimers()

    const componentRequestData = new ComponentRequestData()
    const events = 'testEvent'

    componentRequestData.withEvents(events)

    mockAxios.onGet('/test-url').reply(200, {message: 'Success'})

    jest.spyOn(global, 'CustomEvent').mockImplementation((type, eventInit) => {
      return {type, ...eventInit.detail}
    })

    const dispatchEventsSpy = jest.spyOn(global, 'dispatchEvent').mockImplementation(() => true)

    await request(t, '/test-url', 'get', {}, {}, componentRequestData)

    jest.runAllTimers()

    expect(dispatchEventsSpy).toHaveBeenCalled()
    dispatchEventsSpy.mockRestore()

    jest.useRealTimers()
  })
})

describe('url function', () => {
  it('should append query parameters to the URL', () => {
    const baseUrl = 'https://example.com/api'
    const append = 'param1=value1&param2=value2'

    const result = urlWithQuery(baseUrl, append)

    expect(result).toBe('https://example.com/api?param1=value1&param2=value2')
  })

  it('should append query parameters to the URL, when starts with /', () => {
    const baseUrl = '/api'
    const append = 'param1=value1&param2=value2'
    const originalLocation = window.location
    delete window.location
    window.location = {...originalLocation, origin: 'https://example.com'}

    const result = urlWithQuery(baseUrl, append)

    expect(result).toBe('https://example.com/api?param1=value1&param2=value2')
    window.location = originalLocation
  })

  it('should call the callback if provided', () => {
    const baseUrl = 'https://example.com/api'
    const append = 'param1=value1'
    const callback = jest.fn()

    const result = urlWithQuery(baseUrl, append, callback)

    expect(callback).toHaveBeenCalled()
    expect(result).toBe('https://example.com/api?param1=value1')
  })
})
