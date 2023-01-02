/* eslint-env node */

const colors = require('tailwindcss/colors')

/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  important: true,
  content: [
      "./resources/**/*.blade.php",
      "./resources/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    colors: {
      inherit: colors.inherit,
      current: colors.current,
      transparent: colors.transparent,
      white: colors.white,
      brand: { DEFAULT: colors.violet['500'], ...colors.violet },
      secondary: { DEFAULT: colors.slate['500'], ...colors.slate },
      decoration: { DEFAULT: colors.indigo['500'], ...colors.indigo },
      warning: { DEFAULT: colors.amber['500'], ...colors.amber },
      danger: { DEFAULT: colors.rose['500'], ...colors.rose },
      success: { DEFAULT: colors.teal['500'], ...colors.teal },
    },
    extend: {},
  },
  plugins: [require('@tailwindcss/forms')],
}
