import {afterEach, expect, test} from '@jest/globals'
import select from '../../Components/Select.ts'

let component

afterEach(() => {
  component?.destroy()
  document.body.innerHTML = ''
})

test.each([false, true])('form reset preserves option labels (multiple: %s)', async multiple => {
  document.body.innerHTML = `<form><select ${multiple ? 'multiple' : ''}>
    <option value="it" selected>Italy</option>
    <option value="fr">France</option>
  </select></form>`

  const ticks = []
  component = select()
  component.$el = document.querySelector('select')
  component.$nextTick = callback => ticks.push(callback)
  component.init()
  ticks.shift()()

  component.selectInstance.setValue('fr')
  expect(component.selectInstance.control.textContent).toContain('France')

  document.querySelector('form').reset()
  ticks.shift()()

  expect(component.selectInstance.items).toEqual(['it'])
  expect(component.selectInstance.control.textContent).toContain('Italy')
  expect(component.selectInstance.control.textContent).not.toContain('undefined')
})
