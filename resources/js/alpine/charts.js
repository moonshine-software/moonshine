/* Charts */

const darkModeOptions = {
  chart: {
    foreColor: '#6a778f',
  },
  grid: {
    borderColor: '#535A6C',
  },
  theme: {
    mode: 'dark',
  },
  tooltip: {
    theme: 'dark',
  },
}

const lightModeOptions = {
  chart: {
    foreColor: '#64748b',
  },
  grid: {
    borderColor: '#c2c2c2',
  },
  theme: {
    mode: 'light',
  },
  tooltip: {
    theme: 'light',
  },
}

export default (options = {}) => ({
  apexchartsInstance: null,
  init() {
    this.apexchartsInstance = new ApexCharts(this.$el, options)

    const updateThemeOptions = () =>
      this.apexchartsInstance.updateOptions(
        Alpine.store('darkMode').on ? darkModeOptions : lightModeOptions,
      )

    setTimeout(() => {
      this.apexchartsInstance.render()
      updateThemeOptions()
    }, 300)

    window.addEventListener('darkMode:toggle', updateThemeOptions)
  },
})
