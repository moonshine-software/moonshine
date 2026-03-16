import {formToJSON} from 'axios'

export default function formDataParams(selector, el) {
  let form = null

  if (selector !== undefined && selector !== null && selector !== '') {
    form = document.querySelector(selector)
  } else if (el) {
    form = el.closest('form')
  }

  if (!form) {
    return {}
  }

  return formToJSON(new FormData(form))
}
