export default function debounce(callback, delay, options) {
  let timeoutId

  return () => {
    clearTimeout(timeoutId)

    timeoutId = setTimeout(() => callback.apply(this, options), delay)
  }
}
