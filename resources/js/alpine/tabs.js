export default (activeTab = '', isVertical = false) => ({
  activeTab: activeTab,
  isVertical: isVertical,
  activationVerticalWidth: 480,

  async init() {
    await this.$nextTick()

    if (this.isVertical) {
      this.activationVerticalWidth = this.$el.dataset.tabsVerticalMinWidth ?? 480
      this.toggleVerticalClass(true)
      this.checkWidthElement()
      window.addEventListener('resize', () => this.checkWidthElement())
    }
  },

  toggleVerticalClass(shouldBeVertical) {
    this.$el.classList[shouldBeVertical ? 'add' : 'remove']('tabs-vertical')
  },

  checkWidthElement() {
    const shouldBeVertical = this.$el.offsetWidth >= this.activationVerticalWidth
    this.toggleVerticalClass(shouldBeVertical)
  },

  setActiveTab(tabId) {
    this.activeTab = tabId ?? this.activeTab
  },
})
