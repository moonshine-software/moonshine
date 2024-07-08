/**
 * Classes for layout wrapper with a sidebar or topbar.
 * Workaround for browsers that do not support the :has selector (Firefox).
 * Probably :has selector coming in Firefox 120.
 * @see https://bugzilla.mozilla.org/show_bug.cgi?id=418039
 */
if (!CSS.supports('selector(:has(*))')) {
  document.addEventListener('DOMContentLoaded', () => {
    const wrapperElement = document.querySelector('.layout-wrapper')

    if (wrapperElement && wrapperElement.querySelector(':scope > .layout-menu')) {
      wrapperElement.classList.add('layout-wrapper--sidebar')
    }

    if (wrapperElement && wrapperElement.querySelector(':scope > .layout-menu-horizontal')) {
      wrapperElement.classList.add('layout-wrapper--top-menu')
    }

    if (wrapperElement && wrapperElement.querySelector(':scope > .layout-menu-mobile')) {
      wrapperElement.classList.add('layout-wrapper--mobilebar')
    }
  })
}
