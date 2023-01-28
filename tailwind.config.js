module.exports = {
    darkMode: 'class',
    content: [
        './resources/views/**/*.blade.php',
    ],
    safelist: [
        {
            pattern: /./,
        },
    ],
    theme: {
        extend: {
            gridTemplateRows: {
                '[auto,auto,1fr]': 'auto auto 1fr',
            },
            colors: {
                black: "#3f3960",
                purple: "#7665FF",
                pink: "#E065EF",
                dark: "#130d52",
                darkblue: "#302c79",
                whiteblue: "#f6f4fe"
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
        require('@tailwindcss/aspect-ratio'),
    ],
}
