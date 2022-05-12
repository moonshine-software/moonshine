module.exports = {
  darkMode: 'class',
  content: [
    './src/views/**/*.blade.php',
  ],
  theme: {
    extend: {
      gridTemplateRows: {
        '[auto,auto,1fr]': 'auto auto 1fr',
      },
      colors: {
        purple: "#7843E9",
        pink: "#EC4176",
        dark: "#222",
        darkblue: "#1E1F43",
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    require('@tailwindcss/aspect-ratio'),
  ],
}
