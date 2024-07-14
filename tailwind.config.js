const defaultTheme = require("tailwindcss/defaultTheme");
module.exports = {
  content: [
    "./assets/**/*.{vue,js,ts,jsx,tsx}",
    "./templates/**/*.{html,twig}",
    "assets/js/**/*.jsx",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ["Inter var", ...defaultTheme.fontFamily.sans],
      },
    },
  },
  plugins: [],
};
