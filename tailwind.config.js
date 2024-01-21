/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./components/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors: {
        primary: "#6817FF",
        primarylight: "rgba(104,23,255,0.2)",
        secondary: "#231942",
        tertiary:"#F71735",
        quartiary:"#940e20",
        dark:"#131313",
        darklight:"rgba(19,19,19,0.49)",
        white:"#FCFAF9",
        green:"#17F771",
        greenborder:"rgba(23,247,113,0.3)",
        greenlight:"#E5FAEC",
        graylight: "#C9C9C9"
      },
      gridTemplateColumns: {
        'admin-layout': " 394px 1fr"
      }
    },
  },
  plugins: [],
}

