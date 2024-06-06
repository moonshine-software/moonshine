export default function selectorsParams(params, root = null) {
  let data = {}

  if (params !== undefined && params) {
    const selectors = params.split(',')

    selectors.forEach(function (selector) {
      let parts = selector.split('/')

      let paramName = parts[1] ?? parts[0]

      const el = (root ?? document).querySelector(parts[0])
      if (el != null) {
        data[paramName] = el.value
      }
    })
  }

  return data
}
