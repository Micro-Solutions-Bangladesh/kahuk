const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    content: ["./*.tpl", "./**/*.tpl", "./assets/sass/*.scss", "./assets/js/custom.js"],
    theme: {
        screens: { // 
            'xs': '475px',



            // 'md': '768px',

            // 'lg': '1024px',

            // 'mxl': '1150px',

            // 'xl': '1280px',

            // '2xl': '1536px',

            ...defaultTheme.screens,
        },

        container: {
            center: true,

            padding: {
                DEFAULT: "1rem",
                'xl': "1.5rem",
            },

            screens: {
                xl: "1280px",
            },
        },

        extend: {
            fontFamily: {                
                body: [
                    "Roboto",
                    "Inter",
                    "Helvetica",
                    "Arial",
                    "sans-serif",
                    '"Apple Color Emoji"',
                    '"Segoe UI Emoji"',
                    '"Segoe UI Symbol"',
                    '"Noto Color Emoji"',
                ],

                htag: [
                    '"Roboto Condensed"',
                    "Inter",
                    "Helvetica",
                    "Arial",
                    "sans-serif",
                ],
            },

            colors: {
                deep: {
                    110: "#0f151a",
                    105: "#18232b",
                    100: "#21303b", // base color
                    95: "#2a3d4b",
                    90: "#334b5c",
                    80: "#46657c",
                    70: "#58809d",
                    60: "#7699b2",
                    50: "#96b1c5",
                    40: "#b7c9d7",
                    30: "#d8e2e9",
                },

                light: {
                    180: "#01141e",
                    170: "#023550",
                    160: "#045681",
                    150: "#0577b3",
                    140: "#0798e4",
                    130: "#25b0f9",
                    120: "#57c2fa",
                    110: "#88d4fc",
                    100: "#bae6fd", //base color
                    95: "#d3effe",
                    90: "#ecf8fe",
                },
            },

            gap: {
                'base': '1.5rem',
                'base-half': '0.75rem',
            },

            margin: {
                'base-double': '3rem',
                'base': '1.5rem',
                'base-half': '0.75rem',
            },

            padding: {
                'base-double': '3rem',
                'base': '1.5rem',
                'base-half': '0.75rem',
            }
        },
    },

    plugins: [
        require("tw-elements/dist/plugin"),
    ],
};
