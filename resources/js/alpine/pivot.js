/* Pivot */

export default () => ({
  autoCheck() {
    let checker = this.$root.querySelector('.pivotChecker')
    let fields = this.$root.querySelectorAll('.pivotFields')

    fields.forEach(function (value, key) {
      value.addEventListener('input', event => {
        checker.checked = event.target.value
      })
    })
  },
})
