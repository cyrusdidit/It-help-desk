/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        'soft-dove': '#C0CAB3',
        'spiced-hot-chocolate': '#92423D',
        'moon-rock': '#9D7D77',
        'dark-sienna': '#492324',
        'black-raspberry': '#1B0F0C',
      }
    },
  },
  plugins: [],
}
