const plugin = require('tailwindcss/plugin')

const vendorSafeList = [
  {
    // usage: column.blade.php
    pattern: /col-span-\d/,
    variants: isDevelopment() ? ['xl'] : ['xs', 'sm', 'md', 'lg', 'xl', '2xl'],
  },
  {
    // usage: icons
    pattern: /text-(pink|purple)/,
    variants: isDevelopment() ? [] : ['xs', 'sm', 'md', 'lg', 'xl', '2xl'],
  },
  {
    // usage: icons
    pattern: /(w-|h-)[1-9]/,
  },
]
const clientSafeList = [
  'visible',
  'pointer-events-auto',
  'opacity-0',
  'opacity-100',
  {
    pattern: /text-dark-\d/,
    variants: ['xs', 'sm', 'md', 'lg', 'xl', '2xl'],
  },
  {
    pattern: /gap(-x|-y)-\d/,
    variants: ['xs', 'sm', 'md', 'lg', 'xl', '2xl'],
  },
  {
    pattern: /space(-x|-y)-\d/,
    variants: ['xs', 'sm', 'md', 'lg', 'xl', '2xl'],
  },
]

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./resources/js/**/*.js', './resources/views/**/*.blade.php'],
  safelist: isDevelopment() ? vendorSafeList : [...vendorSafeList, ...clientSafeList],
  darkMode: 'class',
  theme: {
    screens: {
      xs: '375px',
      sm: '640px',
      md: '768px',
      lg: '1024px',
      xl: '1280px',
      '2xl': '1536px',
    },
    container: {
      center: true,
      padding: '20px',
    },
    fontFamily: {
      sans: ['Gilroy', 'sans-serif'],
    },
    fontSize: {
      '3xs': ['0.875rem', '1.5em'],
      '2xs': ['0.9375rem', '1.5em'],
      xs: ['1rem', '1.5em'],
      sm: ['1.125rem', '1.5em'],
      md: ['1.25rem', '1.5em'],
      lg: ['1.625rem', '1.5em'],
      xl: ['2rem', '1.25em'],
      '2xl': ['3rem', '1.175em'],
      '3xl': ['4rem', '1.175em'],
    },
    extend: {
      colors: {
        primary: {
          DEFAULT: 'rgba(var(--primary), <alpha-value>)',
        },
        secondary: {
          DEFAULT: 'rgba(var(--secondary), <alpha-value>)',
        },
        body: 'rgba(var(--body), <alpha-value>)',
        dark: {
          DEFAULT: 'rgba(var(--dark-DEFAULT), <alpha-value>)',
          50: 'rgba(var(--dark-50), <alpha-value>)',
          100: 'rgba(var(--dark-100), <alpha-value>)',
          200: 'rgba(var(--dark-200), <alpha-value>)',
          300: 'rgba(var(--dark-300), <alpha-value>)',
          400: 'rgba(var(--dark-400), <alpha-value>)',
          500: 'rgba(var(--dark-500), <alpha-value>)',
          600: 'rgba(var(--dark-600), <alpha-value>)',
          700: 'rgba(var(--dark-700), <alpha-value>)',
          800: 'rgba(var(--dark-800), <alpha-value>)',
          900: 'rgba(var(--dark-900), <alpha-value>)',
        },
        info: {
          bg: 'rgba(var(--info-bg), <alpha-value>)',
          text: 'rgba(var(--info-text), <alpha-value>)',
        },
        success: {
          bg: 'rgba(var(--success-bg), <alpha-value>)',
          text: 'rgba(var(--success-text), <alpha-value>)',
        },
        warning: {
          bg: 'rgba(var(--warning-bg), <alpha-value>)',
          text: 'rgba(var(--warning-text), <alpha-value>)',
        },
        error: {
          bg: 'rgba(var(--error-bg), <alpha-value>)',
          text: 'rgba(var(--error-text), <alpha-value>)',
        },
        menu: {
          link: 'rgba(var(--menu-link-color, 255, 255, 255), <alpha-value>)',
          hover: 'rgba(var(--menu-hover-color, var(--secondary)), <alpha-value>)',
          active: {
            bg: 'rgba(var(--menu-active-bg, var(--primary)), <alpha-value>)',
            color: 'rgba(var(--menu-active-color, 255, 255, 255), <alpha-value>)',
          },
          current: {
            bg: 'rgba(var(--menu-current-bg, 248, 250, 252), <alpha-value>)',
            color: 'rgba(var(--menu-current-color, 0, 0, 0), <alpha-value>)',
          },
          dropdown: {
            bg: 'rgba(var(--menu-dropdown-bg, var(--dark-600)), <alpha-value>)',
          },
        },
      },
      borderWidth: {
        '3': '3px',
      },
      transitionProperty: {
        colors:
          'color, background-color, border-color, text-decoration-color, box-shadow, fill, stroke',
      },
      transitionDuration: {
        DEFAULT: '350ms',
      },
      zIndex: {
        '1': '1',
        '2': '2',
        '3': '3',
        '4': '4',
        '5': '5',
        modal: '1070',
        offcanvas: '1050',
        toast: '1500',
      },
      opacity: {
        '15': '.15',
      },
      keyframes: {
        wiggle: {
          '0%, 100%': {transform: 'rotate(-15deg)'},
          '50%': {transform: 'rotate(15deg)'},
        },
      },
      animation: {
        wiggle: 'wiggle 2.5s ease-in-out infinite',
      },
    },
  },
  variants: {
    extend: {},
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    require('@tailwindcss/aspect-ratio'),
    plugin(function ({addVariant}) {
      addVariant('current', '&._is-active')
    }),
  ],
}

function isDevelopment() {
  return process.env.NODE_ENV === 'development'
}
