import {HtmlMode} from './HtmlMode.js'

export function extractFromEventDetails(attributes, mapTo = undefined) {
  const data = mapTo !== undefined ? [] : {}

  if (attributes) {
    const selectors = attributes.split(',')

    selectors.forEach(function (selector) {
      let parts = selector.split('{->}')

      if (mapTo !== undefined) {
        data.push({[mapTo[0]]: parts[0], [mapTo[1]]: parts[1]})
      } else {
        data[parts[0]] = parts[1]
      }
    })
  }

  return data
}

export default function DOMUpdater(
  data = {
    htmlData: undefined, // array
    selectors: undefined, // array
    fields_values: undefined, // object
  },
) {
  if (data.htmlData !== undefined) {
    data.htmlData.forEach(function (htmlDataItem) {
      let selectors = data.selectors ?? htmlDataItem.selector

      if (selectors) {
        selectors = typeof selectors === 'string' ? selectors.split(',') : selectors

        selectors.forEach(function (selector) {
          let elements = document.querySelectorAll(selector)
          elements.forEach(element => {
            htmlReplace(
              htmlDataItem.html && typeof htmlDataItem.html === 'object'
                ? (htmlDataItem.html[selector] ?? htmlDataItem.html)
                : htmlDataItem.html,
              htmlDataItem.htmlMode,
              selector,
              element,
            )
          })
        })
      }
    })
  }

  if (data.fields_values !== undefined && typeof data.fields_values == 'object') {
    for (let [selector, value] of Object.entries(data.fields_values)) {
      let el = document.querySelector(selector)
      if (el === null) {
        continue
      }

      let changeEl = el
      let changeValue = el
      let changedValue = value
      let changed = false
      let needChangeEl = true

      if (el.getAttribute('type') === 'color') {
        let parent = el.closest('div.form-color')

        if (parent) {
          let inputEl = parent.querySelector(
            `input[name="${el.getAttribute('name')}"][data-color-input="data-color-input"]`,
          )
          if (inputEl) {
            inputEl.value = changedValue
            inputEl.dispatchEvent(new Event('change'))
            changed = true
          }
        }
      }

      if (el.getAttribute('data-checkbox-hidden')) {
        let parent = el.parentElement
        if (parent) {
          let inputEl = parent.querySelector(
            `input[name="${el.getAttribute('name')}"][type="checkbox"]`,
          )
          if (inputEl) {
            inputEl.checked = changedValue.toString() !== el.value.toString()
            inputEl.dispatchEvent(new Event('change'))
            changed = true
            needChangeEl = false
          }
        }
      }

      if (el.tagName.toLowerCase() === 'select' && el.classList.contains('tomselected')) {
        el.tomselect.setValue(changedValue.toString())
      }

      if (needChangeEl) {
        changeValue.value = changedValue
      }

      if (!changed) {
        changeEl.dispatchEvent(new Event('change'))
      }
    }
  }
}

function htmlReplace(html, mode, selector, element) {
  let htmlMode = HtmlMode.INNER_HTML
  if (mode !== undefined) {
    htmlMode = mode
  }
  if (htmlMode === HtmlMode.INNER_HTML) {
    element.innerHTML = html
  } else if (htmlMode === HtmlMode.OUTER_HTML) {
    element.outerHTML = html
  } else {
    element.insertAdjacentHTML(htmlMode, html)
  }
}
