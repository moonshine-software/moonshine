import {
  containsAttribute,
  asyncExtraQuery,
  filterAttributeStartsWith,
  getAncestorsUntil,
  getQueryString,
  isTextInput,
  limitFormDataParams,
  validationInHiddenBlocks,
} from './../../Support/Forms.js'

import {expect, jest} from '@jest/globals'

jest.useFakeTimers()

describe('FormUtils', () => {
  test('filterAttributeStartsWith should filter keys correctly', () => {
    const data = {testKey: 'value', column: 'id', ignoreKey: 'ignore'}
    const result = filterAttributeStartsWith(data, 'test')
    expect(result).toEqual({ignoreKey: 'ignore'})
  })

  test('validationInHiddenBlocks should add invalid listeners to fields', () => {
    document.body.innerHTML = '<input type="text" />'
    const input = document.querySelector('input')
    const spy = jest.spyOn(input, 'addEventListener')
    validationInHiddenBlocks()
    expect(spy).toHaveBeenCalledWith('invalid', expect.any(Function))
  })

  test('getAncestorsUntil should return ancestors correctly', () => {
    document.body.innerHTML = '<div id="parent"><div id="child"></div></div>'
    const child = document.getElementById('child')
    const parent = document.getElementById('parent')
    const result = getAncestorsUntil(child, document.body)
    expect(result).toContain(parent)
  })

  test('containsAttribute should return true if attribute exists', () => {
    const element = document.createElement('div')
    element.setAttribute('data-test', 'value')
    expect(containsAttribute(element, 'data-test')).toBe(true)
  })

  test('isTextInput should return true for text input types', () => {
    const input = document.createElement('input')
    input.type = 'text'
    expect(isTextInput(input)).toBe(true)
  })

  test('getQueryString should serialize object to query string', () => {
    const obj = {key1: 'value1', key2: 'value2'}
    expect(getQueryString(obj)).toBe('key1=value1&key2=value2')
  })

  test('asyncExtraQuery should serialize async extra key and value from element dataset', () => {
    const select = document.createElement('select')
    select.dataset.asyncExtraKey = 'imageable_type'
    select.dataset.asyncExtra = 'MoonShine\\Tests\\Fixtures\\Models\\Item'

    expect(asyncExtraQuery(select)).toBe(
      'imageable_type=MoonShine%5CTests%5CFixtures%5CModels%5CItem',
    )
  })

  test('limitFormDataParams should limit input lengths', () => {
    const formData = new FormData()
    formData.append('short', '12345')
    formData.append('long', 'a'.repeat(60))
    const result = limitFormDataParams(formData)
    expect(Array.from(result.keys())).toContain('short')
    expect(Array.from(result.keys())).not.toContain('long')
  })
})
