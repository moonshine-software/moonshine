/**
 * Classes for layout wrapper with a sidebar or topbar.
 * Workaround for Firefox (~12% usage).
 * Probably has selector coming in version 120.
 */
if (navigator.userAgent.match(/firefox|fxios/i)) {
  document.addEventListener('DOMContentLoaded', event => {
    const wrapperElement = document.querySelector('.layout-wrapper')
    if (!wrapperElement) {
      return
    }
    if (wrapperElement.querySelector(':scope > .layout-menu')) {
      wrapperElement.classList.add('layout-wrapper--sidebar')
    }
    if (wrapperElement.querySelector(':scope > .layout-menu-horizontal')) {
      wrapperElement.classList.add('layout-wrapper--top-menu')
    }
  })
}
