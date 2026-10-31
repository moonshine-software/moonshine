/* Toasts (notices) */

// Date.now() repeats for toasts added in the same millisecond
let lastId = 0

export default () => ({
  toasts: [],
  visible: [],

  add(toast) {
    toast.id = ++lastId
    this.toasts.push(toast)
    this.fire(toast.id)
  },

  fire(id) {
    const toast = this.toasts.find(toast => toast.id === id)
    this.visible.push(toast)
    const timeShown =
      toast.duration ?? MoonShine.config().getToastDuration() ?? 2000 * this.visible.length

    if (timeShown > 0) {
      setTimeout(() => {
        this.remove(id)
      }, timeShown)
    }
  },

  remove(id) {
    const toast = this.visible.find(toast => toast.id == id)
    const index = this.visible.indexOf(toast)

    // Already closed by a click: splice(-1) would remove the newest toast instead
    if (index === -1) {
      return
    }

    this.visible.splice(index, 1)
  },
})
