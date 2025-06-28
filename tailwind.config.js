/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors: {
        velocite: "#00aeef",
        redimportant: "#d40740",
      }
    },
  },
  plugins: [],
}
