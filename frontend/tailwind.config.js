/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{vue,js,ts,jsx,tsx}'],
  theme: {
    extend: {
      fontFamily: {
        sans: ['"Inter"', '"Segoe UI"', 'system-ui', 'sans-serif'],
      },
      colors: {
        brand: {
          50: '#f2f6fb',
          100: '#e2ebf5',
          200: '#c3d6ea',
          300: '#96b7d8',
          400: '#6291bf',
          500: '#3f70a3',
          600: '#2f5786',
          700: '#28466c',
          800: '#243b59',
          900: '#22334c',
          950: '#161f31',
        },
        passenger: {
          50: '#fef2f2',
          100: '#fde3e3',
          400: '#f3706f',
          500: '#e33e3e',
          600: '#cc2a2a',
          700: '#a92121',
        },
        driver: {
          50: '#eff6ff',
          100: '#dbeafe',
          400: '#5b9cf6',
          500: '#2f74e0',
          600: '#215bbb',
          700: '#1c4996',
        },
        'neutral-status': {
          50: '#f7f7f8',
          100: '#eeeef0',
          400: '#a0a3ab',
          500: '#7d818a',
          600: '#5f6470',
        },
      },
      boxShadow: {
        card: '0 1px 2px 0 rgb(15 23 42 / 0.06), 0 1px 3px 0 rgb(15 23 42 / 0.08)',
      },
    },
  },
  plugins: [],
}
