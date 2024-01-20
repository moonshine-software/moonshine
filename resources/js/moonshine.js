export class MoonShine {
  constructor() {
    this.callbacks = {}
  }

  onCallback(name, callback) {
    if (typeof callback === 'function') {
      this.callbacks[name] = callback
    }
  }
}
