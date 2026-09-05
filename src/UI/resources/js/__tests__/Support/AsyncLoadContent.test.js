import axios from 'axios'
import load from '../../Support/AsyncLoadContent.js' // Adjust the path as necessary
import {beforeEach, afterEach, describe, expect, it, jest} from '@jest/globals'

jest.mock('axios')

describe('load function', () => {
  let containerElement

  beforeEach(() => {
    // Set up a mock container element
    axios.get = jest.fn()

    containerElement = document.createElement('div')
    containerElement.id = 'test-container'
    document.body.appendChild(containerElement)
  })

  afterEach(() => {
    // Clean up the DOM after each test
    document.body.removeChild(containerElement)
    jest.clearAllMocks()
  })

  it('should load content successfully and insert it into the DOM', async () => {
    const mockData = '<div>Loaded Content</div>'
    axios.get.mockResolvedValue({data: mockData, status: 200})

    await load('https://example.com/test-url', 'test-container')

    expect(containerElement.innerHTML).toBe(mockData)
  })

  it('should handle script elements correctly', async () => {
    const mockData = `
      <div>Loaded Content</div>
      <script id="test-script" src="https://example.com/test.js">console.log('Test');</script>
    `
    axios.get.mockResolvedValue({data: mockData, status: 200})

    await load('https://example.com/test-url', 'test-container')

    const scriptElement = containerElement.querySelector('script')
    expect(scriptElement).not.toBeNull()
    expect(scriptElement.src).toBe('https://example.com/test.js')
    expect(scriptElement.text).toBe("console.log('Test');")
  })

  it('should not insert content if the request fails', async () => {
    axios.get.mockResolvedValue({status: 500})

    await load('https://example.com/test-url', 'test-container')

    expect(containerElement.innerHTML).toBe('')
  })
})
