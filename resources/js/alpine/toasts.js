/* Toasts (notices) */

export default () => ({
  toasts: [],
  visible: [],

  add(toast) {
    toast.id = Date.now()
    this.toasts.push(toast)
    this.fire(toast.id)
  },

  fire(id) {
    const toast = this.toasts.find(toast => toast.id == id);
    this.visible.push(toast);
    const baseTime = 2000; // Базовое время показа в миллисекундах 2 секунды
    const timePerCharacter = 50; // Время на каждый символ 50 миллисекунд
    const timeShown = baseTime + (toast.text.length * timePerCharacter);
    setTimeout(() => {
      this.remove(id)
    }, timeShown)
  },

  remove(id) {
    const toast = this.visible.find(toast => toast.id == id)
    const index = this.visible.indexOf(toast)
    this.visible.splice(index, 1)
  },
})
