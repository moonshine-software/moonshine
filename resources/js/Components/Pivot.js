export default () => ({
  init() {
    this.$root.querySelectorAll('.pivotTitle')?.forEach(function (el) {
      el.addEventListener('click', event => {
        let tr = el.closest('tr')
        let checker = tr.querySelector('.pivotChecker')

        checker.checked = !checker.checked
      })
    })
  },
  checkAll() {
    this.$root.querySelectorAll('.pivotChecker')?.forEach(function (el) {
      el.checked = true
    })
  },
  uncheckAll() {
    this.$root.querySelectorAll('.pivotChecker')?.forEach(function (el) {
      el.checked = false
    })
  },
  autoCheck() {
    let fields = this.$root.querySelectorAll('.pivotField')

    fields.forEach(function (el, key) {
      el.addEventListener('change', event => {
        let tr = el.closest('tr')
        let checker = tr.querySelector('.pivotChecker')

        checker.checked = event.target.value
      })
    })
  },
})
