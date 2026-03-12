export class UI {
  toast(text, type = 'default', duration = null) {
    dispatchEvent(
      new CustomEvent('toast', {
        detail: {
          type: type,
          text: text,
          duration: duration,
        },
      }),
    )
  }

  toggleModal(name, asyncUrl = null) {
    dispatchEvent(new CustomEvent(`modal_toggled:${name}`, {detail: asyncUrl ?? null}))
  }

  toggleOffCanvas(name) {
    dispatchEvent(new CustomEvent(`off_canvas_toggled:${name}`))
  }
}
