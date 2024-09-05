export default (slides = []) => ({
  activeSlide: 0,
  slides: [],
  init() {
    this.slides = slides
  },
  next() {
    if (this.activeSlide < this.slides.length - 1) {
      this.activeSlide++
    }
  },
  previous() {
    if (this.activeSlide !== 0) {
      this.activeSlide--
    }
  },
})
