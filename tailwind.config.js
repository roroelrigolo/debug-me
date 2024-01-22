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
        green:"#30C56C",
        greenborder:"18653780",
        greenlight:"#E5FAEC",
        graylight: "#C9C9C9"
      },
      gridTemplateColumns: {
        'admin-layout': " 394px 1fr"
      },
      boxShadow: {
        'ticket': '0px 1px 0px 0px rgba(0, 0, 0, 0.50)',
        'ticket-hover': '0px 4px 0px 0px rgba(0, 0, 0, 0.50)',
      }
    },
  },
  plugins: [],
}

