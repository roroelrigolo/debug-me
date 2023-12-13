/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors: {
        primary: "#6817FF",
        secondary: "#231942",
        tertiary:"#F71735",
        dark:"#131313",
        darklight:"rgba(19,19,19,0.49)",
        white:"#FCFAF9",
        green:"#17F771",
        greenborder:"rgba(23,247,113,0.3)",
        greenlight:"#E5FAEC",
      }
    },
  },
  plugins: [],
}

