/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './**/*.php',
    './template-parts/**/*.php',
    './woocommerce/**/*.php',
    './assets/js/**/*.js',
  ],
  safelist: [
    'font-display',
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#1B4D3E', // Deep Forest Green
          dark: '#143A2F',
          light: '#2C7A63',
        },
        secondary: {
          DEFAULT: '#8FBC8F', // Sage
          light: '#E8F5E9',   // Very light sage/white
        },
        accent: {
          DEFAULT: '#E07A5F', // Terracotta
          hover: '#D06950',
          light: '#F4DDD8',   // Light terracotta
        },
        cream: {
          DEFAULT: '#FAF8F3', // Cream
          light: '#FDFCFA',   // Very light cream
        },
        neutral: {
          DEFAULT: '#F5F5DC', // Warm Beige
          light: '#FAFAF5',   // Off-white
          medium: '#6B7280',  // Medium gray for text
          dark: '#3D4035',    // Dark text
        }
      },
      fontFamily: {
        sans: ['Inter', 'Outfit', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        display: ['Fraunces', 'Georgia', 'serif'],
        serif: ['Merriweather', 'Georgia', 'serif'],
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms')({
      strategy: 'class', // Only generate classes instead of base styles
    }),
    require('@tailwindcss/typography'),
    require('@tailwindcss/aspect-ratio'),
  ],
  corePlugins: {
    // Disable unused core plugins to reduce CSS size
    float: false,
    clear: false,
    skew: false,
    caretColor: false,
    sepia: false,
  },
}
