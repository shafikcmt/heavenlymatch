/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.{ts,tsx}',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
      },
      colors: {
        // HeavenlyMatch brand palette — deep Islamic green (#006847)
        primary: {
          50:  '#edfdf5',
          100: '#d1f7e6',
          200: '#a5efce',
          300: '#6de0b0',
          400: '#2dc98e',
          500: '#0aad75',
          600: '#008b5c',
          700: '#006847',   // main brand green
          800: '#005237',
          900: '#003d29',
          950: '#00251a',
        },
      },
      borderRadius: {
        '2xl': '1rem',
        '3xl': '1.5rem',
      },
      boxShadow: {
        card: '0 1px 3px 0 rgb(0 0 0 / 0.07), 0 1px 2px -1px rgb(0 0 0 / 0.07)',
        modal: '0 20px 60px -10px rgb(0 0 0 / 0.25)',
      },
      keyframes: {
        'ring-draw': {
          '0%':   { 'stroke-dashoffset': '251' },
          '100%': { 'stroke-dashoffset': 'var(--target-offset)' },
        },
        'slide-in-right': {
          '0%':   { transform: 'translateX(100%)', opacity: '0' },
          '100%': { transform: 'translateX(0)',    opacity: '1' },
        },
        'fade-in': {
          '0%':   { opacity: '0', transform: 'translateY(8px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
      },
      animation: {
        'ring-draw':       'ring-draw 1s ease-out forwards',
        'slide-in-right':  'slide-in-right 0.3s ease-out',
        'fade-in':         'fade-in 0.2s ease-out',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}
