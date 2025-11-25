import {afterEach, beforeEach, describe, expect, it, jest} from '@jest/globals'

import {
  getInputs,
  inputFieldName,
  inputGetValue,
  showWhenChange,
  showWhenVisibilityChange,
} from '../../Support/ShowWhen.js'

describe('getInputs', () => {
  let form

  beforeEach(() => {
    document.body.innerHTML = `
      <form id="test-form">
        <input type="text" name="username" value="john_doe">
        <input type="email" name="email" value="john@example.com">
        <input type="checkbox" name="subscribe" checked>
        <input type="radio" name="gender" value="male" checked>
        <input type="radio" name="gender" value="female">
        <input type="number" name="age" value="25">
        <select name="role">
          <option value="admin" selected>Admin</option>
          <option value="user">User</option>
        </select>
        <input type="text" data-show-when-field="dynamicField" value="visibleValue">
        <input type="text" data-show-when-column="columnField" value="columnValue">
      </form>
    `
    form = document.getElementById('test-form')
  })

  afterEach(() => {
    document.body.innerHTML = ''
  })

  it('should extract input values correctly', () => {
    const inputs = getInputs('test-form')

    expect(inputs).toEqual({
      username: {value: 'john_doe', type: 'text'},
      email: {value: 'john@example.com', type: 'email'},
      subscribe: {value: true, type: 'checkbox'},
      gender: {value: 'male', type: 'radio'},
      age: {value: '25', type: 'number'},
      role: {value: 'admin', type: null},
      dynamicField: {value: 'dynamicField', type: 'text'},
      columnField: {value: 'columnValue', type: 'text'},
    })
  })

  it('should handle empty form gracefully', () => {
    document.body.innerHTML = `<form id="empty-form"></form>`
    const inputs = getInputs('empty-form')
    expect(inputs).toEqual({})
  })

  it('should extract values from data-show-when-field attributes', () => {
    const inputs = getInputs('test-form')
    expect(inputs.dynamicField).toEqual({value: 'dynamicField', type: 'text'})
  })

  it('should extract values from data-show-when-column attributes', () => {
    const inputs = getInputs('test-form')
    expect(inputs.columnField).toEqual({value: 'columnValue', type: 'text'})
  })
})

describe('inputFieldName', () => {
  it('should remove brackets from array-like names', () => {
    expect(inputFieldName('user[]')).toBe('user')
  })

  it('should handle slide notation', () => {
    expect(inputFieldName('slide[5]')).toBe('5')
  })

  it('should return an empty string for null input', () => {
    expect(inputFieldName(null)).toBe('')
  })
})

describe('inputGetValue', () => {
  it('should return checked state for checkboxes', () => {
    document.body.innerHTML = `<input type="checkbox" id="test-check" checked>`
    const element = document.getElementById('test-check')
    expect(inputGetValue(element)).toBe(true)
  })

  it('should return selected value for radio buttons', () => {
    document.body.innerHTML = `
      <input type="radio" name="gender" value="male" checked>
      <input type="radio" name="gender" value="female">
    `
    const element = document.querySelector('[name="gender"]:checked')
    expect(inputGetValue(element)).toBe('male')
  })

  it('should return an array for multi-select elements', () => {
    document.body.innerHTML = `
      <select multiple>
        <option value="one" selected>One</option>
        <option value="two" selected>Two</option>
      </select>
    `
    const element = document.querySelector('select')
    expect(inputGetValue(element)).toEqual(['one', 'two'])
  })

  it('should return the value for text inputs', () => {
    document.body.innerHTML = `<input type="text" value="hello">`
    const element = document.querySelector('input')
    expect(inputGetValue(element)).toBe('hello')
  })
})

describe('showWhenChange', () => {
  let mockContext

  beforeEach(() => {
    document.body.innerHTML = `
      <form id="test-form">
        <input type="text" name="field1" value="test-value">
        <div class="moonshine-field" data-show-when-field="field2">Hidden Field</div>
      </form>
    `

    mockContext = {
      whenFields: [
        {changeField: 'field1', showField: 'field2', value: 'test-value', operator: '='},
      ],
      showWhenVisibilityChange: jest.fn(),
      getInputs: jest.fn().mockReturnValue({
        field1: {value: 'test-value', type: 'text'},
        field2: {value: '', type: 'text'},
      }),
    }
  })

  it('should call showWhenVisibilityChange with correct parameters', () => {
    showWhenChange.call(mockContext, 'field1', 'test-form')

    expect(mockContext.showWhenVisibilityChange).toHaveBeenCalledWith(
      [mockContext.whenFields[0]],
      'field2',
      mockContext.getInputs('test-form'),
      'test-form',
    )
  })

  it('should do nothing if the changed field is not in whenFields', () => {
    showWhenChange.call(mockContext, 'nonExistentField', 'test-form')
    expect(mockContext.showWhenVisibilityChange).not.toHaveBeenCalled()
  })
})

describe('showWhenVisibilityChange', () => {
  let form, inputElement, mockContext

  beforeEach(() => {
    document.body.innerHTML = `
      <form id="test-form">
        <input type="text" name="field1" value="test-value">
        <div class="moonshine-field" data-show-when-field="field2">Hidden Field</div>
      </form>
    `

    form = document.getElementById('test-form')
    inputElement = form.querySelector('[data-show-when-field="field2"]')

    mockContext = {
      getInputs: jest.fn().mockReturnValue({
        field1: {value: 'test-value', type: 'text'},
        field2: {value: '', type: 'text'},
      }),
    }
  })

  it('should hide the field if conditions are not met', () => {
    const mockShowWhenFields = [{changeField: 'field1', value: 'wrong-value', operator: '='}]

    showWhenVisibilityChange(
      mockShowWhenFields,
      'field2',
      mockContext.getInputs('test-form'),
      'test-form',
    )

    expect(inputElement.classList.contains('hidden')).toBe(true)
  })

  it('should show the field if conditions are met', () => {
    const mockShowWhenFields = [{changeField: 'field1', value: 'test-value', operator: '='}]

    showWhenVisibilityChange(
      mockShowWhenFields,
      'field2',
      mockContext.getInputs('test-form'),
      'test-form',
    )

    expect(inputElement.classList.contains('hidden')).toBe(false)
  })

  it('should do nothing if inputElement is null', () => {
    document.body.innerHTML = `<form id="test-form"></form>` // Без полей
    expect(() => {
      showWhenVisibilityChange([], 'field2', mockContext.getInputs('test-form'), 'test-form')
    }).not.toThrow()
  })
})
