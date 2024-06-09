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
    dispatchEvent(new CustomEvent(`modal-toggled-${name}`))
  }

  openCollepse(name) {
    dispatchEvent(new CustomEvent(`collapse-open-${name}`))
  }
}
