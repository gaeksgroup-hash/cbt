import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    primary: '#012E34',
                    secondary: '#0F4C5C',
                    accent: '#0891B2',
                    surface: '#FFFFFF',
                    bg: '#F8FAFC',
                    dark: '#0B1F24',
                },
            },
            fontFamily: {
                sans: ['IBM Plex Sans', ...defaultTheme.fontFamily.sans],
                mono: ['IBM Plex Mono', ...defaultTheme.fontFamily.mono],
            },
        },
    },
    plugins: [],
};
