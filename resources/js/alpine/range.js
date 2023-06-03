/* Range */

export default (from = 0, to = 0) => ({
  minValue: 0,
  maxValue: 0,
  min: 0,
  max: 0,
  step: 1,
  minthumb: 0,
  maxthumb: 0,

  init() {
    this.minValue = parseInt(from)
    this.maxValue = parseInt(to)
    this.min = parseInt(this.$el.dataset.min) ?? 0
    this.max = parseInt(this.$el.dataset.max) ?? 1000
    this.step = parseInt(this.$el.dataset.step) ?? 1
  },

  mintrigger() {
    this.minValue = Math.min(this.minValue, this.maxValue - this.step)
    if (this.minValue < this.min) {
      this.minValue = this.min
    }
    this.minthumb = ((this.minValue - this.min) / (this.max - this.min)) * 100
  },

  maxtrigger() {
    this.maxValue = Math.max(this.maxValue, this.minValue + this.step)
    if (this.maxValue > this.max) {
      this.maxValue = this.max
    }
    this.maxthumb = 100 - ((this.maxValue - this.min) / (this.max - this.min)) * 100
  },
})
