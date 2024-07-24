export class UI {
  toast(text, type = 'default') {
    dispatchEvent(
      new CustomEvent('toast', {
        detail: {
          type: type,
          text: text,
        },
      }),
    )
  }

  toggleModal(name) {
    dispatchEvent(new CustomEvent(`modal_toggled-${name}`))
  }
}
