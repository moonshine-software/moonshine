export default (action) => ({
  action: action,
  query: '',
  groups: [],
  init() {
    const t = this

    t.$watch('query', async function(value) {
      const loader = document.querySelector('.search-loading')
      loader.style.display = 'none'

      if(value.length < 2) {
        return;
      }

      loader.style.display = 'block'

      let response = await  axios.get(t.action + '?query=' + value)

      loader.style.display = 'none'

      t.groups = response.data
    })
  },
  modal() {
    const t = this

    t.$dispatch('modal-toggled-global-search')
    t.$nextTick(function() {
      document.querySelector('.search-input').focus()
    })
  },
  group(label, items) {
    let itemsTemplate = ``
    const t = this
    items.forEach(function(item) {
      itemsTemplate += t.item(item)
    })

    return `
      <li class="font-bold">
          <div class="divider">${label}</div>
      </li>
      ${itemsTemplate}
    `
  },
  item(data) {
    return `
      <a href="${data.url}" class="flex items-center justify-start gap-4">
          ${
            data?.image
            ? '<div class="zoom-in h-10 w-10 overflow-hidden rounded-md">' +
            '<img class="h-full w-full object-cover" src="' + data.image + '" alt="">' +
            '</div>'
            : ''
          }
          <div>
            <span class="font-bold text-sm">${data.title}</span>
            <p>${data.preview}</p>
          </div>
      </a>
      `
  }
})
