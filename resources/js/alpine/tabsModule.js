export default (
    tabs = {},
    activeTab = "",
    vertical = {}
) => ({
    tabs: tabs,
    activeTab: activeTab,
    isVertical: false,
    activationWidth: activationWidth,

    init() {
        if (vertical) {
            this.isVertical = vertical.vertical;
            this.activationWidth = vertical.activationWidth;
        }
        this.clickingTab(this.activeTab);
    },

    initTabs() {
        if (this.isVertical) {
            this.addClassVertical();
            this.checkWidthElement();
            window.addEventListener("resize", () => this.checkWidthElement());
        }
    },

    addClassVertical() {
        this.$el.classList.add("tabs-vertical");
    },
    removeClassVertical() {
        this.$el.classList.remove("tabs-vertical");
    },

    checkWidthElement() {
        this.$el.offsetWidth < this.activationWidth
            ? this.removeClassVertical()
            : this.addClassVertical();
    },

    clickingTab(tabId) {
        this.activeTab = tabId ?? this.activeTab;
    },
});
