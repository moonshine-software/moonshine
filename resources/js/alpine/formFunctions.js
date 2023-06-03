export function crudFormQuery() {
    const form = document.querySelector('#moonshine-form')

    if (form === null) {
        return ''
    }

    const formData = new FormData()
    const data = [...formData.entries()]

    return data.map(
        x => `${encodeURIComponent(x[0])}=${encodeURIComponent(x[1])}`).
        join('&')
}
