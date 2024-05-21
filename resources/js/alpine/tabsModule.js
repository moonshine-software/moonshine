export default (activeTab = "", isVertical = false) => ({
    activeTab: activeTab,
    isVertical: isVertical,
    activationWidth: 480,

    async init() {
        await this.$nextTick();

        if (this.isVertical) {
            this.activationWidth = this.$el.dataset.tabsVerticalMinWidth ?? 480;
            this.toggleVerticalClass(true);
            this.checkWidthElement();
            window.addEventListener("resize", () => this.checkWidthElement());
        }
    },

    toggleVerticalClass(shouldBeVertical) {
        this.$el.classList[shouldBeVertical ? "add" : "remove"](
            "tabs-vertical",
        );
    },

    checkWidthElement() {
        const shouldBeVertical = this.$el.offsetWidth >= this.activationWidth;
        this.toggleVerticalClass(shouldBeVertical);
    },

    clickingTab(tabId) {
        this.activeTab = tabId ?? this.activeTab;
    },
});
