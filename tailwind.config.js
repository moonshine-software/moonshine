const plugin = require('tailwindcss/plugin');

module.exports = {
    content: [
        './resources/views/**/*.blade.php',
    ],
    safelist: [
        'visible',
        'pointer-events-auto',
        'opacity-0',
        'opacity-100',
        {
            pattern: /(w-|h-)[1-9]/,
        },
        {
            pattern: /text-(pink|purple)/,
            variants: ['xs', 'sm', 'md', 'lg', 'xl', '2xl'],
        },
        {
            pattern: /text-dark-\d/,
            variants: ['xs', 'sm', 'md', 'lg', 'xl', '2xl'],
        },
        {
            pattern: /col-span-\d/,
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
    ],
    darkMode: 'class',
    theme: {
        screens: {
            'xs': '375px',
            'sm': '640px',
            'md': '768px',
            'lg': '1024px',
            'xl': '1280px',
            '2xl': '1536px',
        },
        container: {
            center: true,
            padding: '20px',
        },
        fontFamily: {
            'sans': ['Gilroy', 'sans-serif'],
        },
        fontSize: {
            '3xs': ['0.875rem', '1.5em'],
            '2xs': ['0.9375rem', '1.5em'],
            'xs': ['1rem', '1.5em'],
            'sm': ['1.125rem', '1.5em'],
            'md': ['1.25rem', '1.5em'],
            'lg': ['1.625rem', '1.5em'],
            'xl': ['2rem', '1.25em'],
            '2xl': ['3rem', '1.175em'],
            '3xl': ['4rem', '1.175em'],
        },
        extend: {
            colors: {
                purple: {
                    DEFAULT: "#7843E9",
                },
                pink: {
                    DEFAULT: "#EC4176",
                },
                body: "#1b253b",
                darkblue: "#1E1F43",
                dark: {
                    50: '#576784',
                    100: '#4a5a79',
                    200: '#415172',
                    300: '#354567',
                    400: '#303d5d',
                    500: '#293552',
                    600: '#28334e',
                    700: '#232d45',
                    800: '#1b253b',
                    900: '#0f172a',
                },
            },
            borderWidth: {
                '3': '3px',
            },
            transitionProperty: {
                'colors': 'color, background-color, border-color, text-decoration-color, box-shadow, fill, stroke',
            },
            transitionDuration: {
                DEFAULT: '350ms'
            },
            zIndex: {
                '1': '1',
                '2': '2',
                '3': '3',
                '4': '4',
                '5': '5',
                'modal': '1070',
                'offcanvas': '1050',
                'toast': '1500',
            },
            opacity: {
                '15': '.15',
            },
            keyframes: {
                wiggle: {
                    '0%, 100%': { transform: 'rotate(-15deg)' },
                    '50%': { transform: 'rotate(15deg)' },
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
        plugin(function ({ addVariant }) {
            addVariant('current', '&._is-active');
        }),
    ],
}
