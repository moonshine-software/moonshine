export default () => ({
  init() {
    this.$root.querySelectorAll('.js-pivot-title')?.forEach(function (el) {
      el.addEventListener('click', event => {
        let tr = el.closest('tr')
        let checker = tr.querySelector('.js-pivot-checker')

        checker.checked = !checker.checked
      })
    })
  },
  checkAll() {
    this.$root.querySelectorAll('.js-pivot-checker')?.forEach(function (el) {
      el.checked = true
    })
  },
  uncheckAll() {
    this.$root.querySelectorAll('.js-pivot-checker')?.forEach(function (el) {
      el.checked = false
    })
  },
  autoCheck() {
    let fields = this.$root.querySelectorAll('.js-pivot-field')

    fields.forEach(function (el, key) {
      el.addEventListener('change', event => {
        let tr = el.closest('tr')
        let checker = tr.querySelector('.js-pivot-checker')

        checker.checked = event.target.value
      })
    })
  },
})
