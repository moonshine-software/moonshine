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

  const formData = new FormData(form)
  const data = {}

  formData.forEach(function (value, key) {
    if (key in data) {
      if (!Array.isArray(data[key])) {
        data[key] = [data[key]]
      }
      data[key].push(value)
    } else {
      data[key] = value
    }
  })

  return data
}
