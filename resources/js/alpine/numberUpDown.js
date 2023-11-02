export default () => ({
  value: 0,
  min: 0,
  max: 0,
  step: 0,

  init() {
    const ref = this.$refs.extensionInput
    this.value = Number(ref.value)
    this.min = Number(ref.min)
    this.max = Number(ref.max ?? 1e10)
    this.step = Number(ref.step ?? 1)
  },
  isDisabled() {
    return this.$refs.extensionInput.disabled || this.$refs.extensionInput.readOnly
  },
  toggleUp() {
    if (this.isDisabled() || this.value >= this.max) return
    this.value = this.value < this.max ? this.value + this.step : this.max
    this.$refs.extensionInput.value = this.value
    this.change()
  },
  toggleDown() {
    if (this.isDisabled() || this.value <= this.min) return
    this.value = this.value > this.min ? this.value - this.step : this.min
    this.$refs.extensionInput.value = this.value
    this.change()
  },
  change() {
    this.$refs.extensionInput.dispatchEvent(new Event('change'))
    this.$refs.extensionInput.dispatchEvent(new Event('input'))
  },
})
