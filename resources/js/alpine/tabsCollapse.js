export default (tabs = {}, activeTab = "") => ({
    activeTab: activeTab,
    tabs: tabs,
    activeTitle: "",
    collapse: true,
    tabsCollapse: null,

    init() {
        this.tabsCollapse = this.$el.querySelector(".tabs-collapse");
        this.clickingTab(this.activeTab);
        this.checkCollapseState();
        window.addEventListener("resize", () => this.checkCollapseState());
    },

    getCollapseButtonVisible() {
        if (this.tabsCollapse) {
            const styles = window.getComputedStyle(this.tabsCollapse);
            const display = styles.getPropertyValue("display");
            return display !== "none";
        }
        return false;
    },

    checkCollapseState() {
        if (!this.collapse && !this.getCollapseButtonVisible()) {
            this.collapse = true;
            this.clickingTab(this.activeTab);
        }
    },

    toggle() {
        this.collapse = !this.collapse;
    },

    clickingTab(tabId) {
        this.activeTab = tabId ?? this.activeTab;
        this.activeTitle = this.tabs[this.activeTab] ?? "";
        if (this.getCollapseButtonVisible()) {
            this.toggle();
        }
    },
});
