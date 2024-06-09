export default (open = false) => ({
  open: open,

  init() {
    const t = this

    this.$el.addEventListener('collapse-open', function(event) {
      t.open = true
    })
  },

  toggle() {
    this.open = !this.open
  },
})
