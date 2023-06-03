/* Charts */

export default (options = {}) => ({
  apexchartsInstance: null,
  init() {
    this.apexchartsInstance = new ApexCharts(this.$el, options)

    setTimeout(() => {
      this.apexchartsInstance.render()
    }, 300)
  },
})
