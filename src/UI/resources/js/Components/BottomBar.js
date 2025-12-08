/**
 * BottomBar Alpine.js Component
 * Mobile-only navigation bar with nested menu support
 */
export default () => ({
  menuStack: [],
  currentLevel: 0,

  // Alpine.js directive attributes to remove from cloned elements
  ALPINE_DIRECTIVES: ['x-data', 'x-show', 'x-if', 'x-for'],

  init() {
    const rootMenu = this.$el.querySelector('[data-bottom-menu-source]')

    if (rootMenu) {
      this.menuStack[0] = this.extractMenuItems(rootMenu)
    }
  },

  /**
   * Find menu items with fallback selectors
   * @param {HTMLElement} container - Container to search within
   * @returns {NodeList} Menu items found
   */
  findMenuItems(container) {
    const selectors = [
      ':scope > .menu-list > .menu-item',
      ':scope > .menu > .menu-list > .menu-item',
      '.menu-list > .menu-item',
    ]

    for (const selector of selectors) {
      const items = container.querySelectorAll(selector)
      if (items.length) return items
    }

    return []
  },

  /**
   * Check if menu item or its children are active
   * @param {HTMLElement} item - Menu item element
   * @param {HTMLElement|null} submenu - Submenu element
   * @returns {boolean} True if active
   */
  isItemActive(item, submenu) {
    if (item.classList.contains('_is-active')) {
      return true
    }

    // Check if any child is active (for parent groups)
    if (submenu) {
      return !!submenu.querySelector('.menu-item._is-active')
    }

    return false
  },

  /**
   * Extract and sanitize icon HTML from menu item
   * Removes Alpine.js directives and duplicate icon wrappers
   * @param {HTMLElement} iconElement - Icon container element
   * @returns {string} Sanitized icon HTML
   */
  extractIconHtml(iconElement) {
    if (!iconElement) return ''

    // Clone to avoid modifying original DOM
    const iconClone = iconElement.cloneNode(true)

    // Keep only the first visible icon wrapper (removes dropdown arrows)
    const visibleIcon = iconClone.querySelector('.icon-wrapper:not([style*="display: none"])')
    if (visibleIcon) {
      iconClone.querySelectorAll('.icon-wrapper').forEach(wrapper => {
        if (wrapper !== visibleIcon) {
          wrapper.remove()
        }
      })
    }

    // Remove Alpine.js directives to avoid context errors
    this.ALPINE_DIRECTIVES.forEach(directive => {
      iconClone.querySelectorAll(`[${directive}]`).forEach(el => {
        el.removeAttribute(directive)
      })
    })

    return iconClone.innerHTML
  },

  /**
   * Extract menu item data from DOM element
   * @param {HTMLElement} item - Menu item element
   * @returns {Object|null} Menu item data or null
   */
  extractItemData(item) {
    const link = item.querySelector('.menu-link, .menu-button')
    if (!link) return null

    const submenu = item.querySelector('.menu-submenu')
    const iconElement = link.querySelector('.menu-icon')

    return {
      icon: this.extractIconHtml(iconElement),
      text: link.querySelector('.menu-text')?.textContent.trim() || '',
      badge: link.querySelector('.menu-badge')?.textContent.trim() || '',
      url: link.getAttribute('href') || '#',
      isActive: this.isItemActive(item, submenu),
      hasChildren: !!submenu,
      submenu,
    }
  },

  /**
   * Extract menu items from container
   * @param {HTMLElement} container - Container with menu items
   * @returns {Array} Array of menu item objects
   */
  extractMenuItems(container) {
    const menuItems = this.findMenuItems(container)

    return Array.from(menuItems)
      .map(item => this.extractItemData(item))
      .filter(Boolean)
  },

  /**
   * Navigate into submenu
   * @param {HTMLElement} submenu - Submenu element to navigate to
   */
  navigateToSubmenu(submenu) {
    if (!submenu) return

    // Use direct selector for submenu items (more specific than findMenuItems)
    const menuItems = submenu.querySelectorAll(':scope > .menu-item')
    const items = Array.from(menuItems)
      .map(item => this.extractItemData(item))
      .filter(Boolean)

    this.currentLevel++
    this.menuStack[this.currentLevel] = items
  },

  /**
   * Navigate back to previous menu level
   */
  navigateBack() {
    if (this.currentLevel > 0) {
      this.menuStack.splice(this.currentLevel, 1)
      this.currentLevel--
    }
  },

  /**
   * Handle menu item click
   * @param {Object} item - Menu item data
   */
  handleItemClick(item) {
    if (item.hasChildren) {
      this.navigateToSubmenu(item.submenu)
    } else if (this.isValidUrl(item.url)) {
      window.location.href = item.url
    }
  },

  /**
   * Check if URL is valid for navigation
   * @param {string} url - URL to check
   * @returns {boolean} True if valid
   */
  isValidUrl(url) {
    return url && url !== '#' && url !== 'javascript:void(0);'
  },

  /**
   * Get current menu items
   * @returns {Array} Current level menu items
   */
  get currentItems() {
    return this.menuStack[this.currentLevel] || []
  },

  /**
   * Check if back button should be shown
   * @returns {boolean} True if not at root level
   */
  get showBackButton() {
    return this.currentLevel > 0
  },
})
