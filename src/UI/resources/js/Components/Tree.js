export default (checked = {}) => ({
  init() {
    checked.forEach(value => {
      this.$el
        .querySelectorAll('input[value="' + value + '"]')
        .forEach(input => (input.checked = true))
    })
  },
})
