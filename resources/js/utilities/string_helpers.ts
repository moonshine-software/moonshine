export const camelize = (s: string) => {
    s = s.replace(/-./g, x => x[1].toUpperCase())
    return capitalize(s)
}

export const slug = (s: string) => {
    return s?.toLowerCase()
        .replace(/[^\w ]+/g, '')
        .replace(/ +/g, '-');
}

export const capitalize = (s: string) => {
    return s[0].toUpperCase() + s.slice(1)
}
