import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                parchment: {
                    DEFAULT: '#FDFBF7',
                    soft: '#F7F1E8',
                    muted: '#EFE6D8',
                },
                obsidian: {
                    DEFAULT: '#1A1412',
                    soft: '#241C19',
                    card: '#2A211D',
                },
                espresso: {
                    DEFAULT: '#1C120C',
                    soft: '#2A1A12',
                },
                terracotta: {
                    DEFAULT: '#C45C3E',
                    soft: '#D9785A',
                    deep: '#9A3F28',
                },
                amber: {
                    DEFAULT: '#D4A017',
                    soft: '#E8C15A',
                    warm: '#C9A24A',
                },
                highland: {
                    DEFAULT: '#1F5C3A',
                    light: '#2F7A4D',
                },
                gold: {
                    DEFAULT: '#C9A24A',
                    soft: '#E0C57A',
                },
                bean: {
                    DEFAULT: '#6B3E1F',
                    soft: '#8A5A2A',
                },
                ivory: {
                    DEFAULT: '#FDFBF7',
                    muted: '#EFE6D8',
                },
                clay: {
                    DEFAULT: '#C45C3E',
                },
            },
            fontFamily: {
                display: ['Fraunces', 'Noto Serif Ethiopic', ...defaultTheme.fontFamily.serif],
                sans: ['DM Sans', 'Noto Sans Ethiopic', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                soft: '0 12px 40px -20px rgba(26, 20, 18, 0.35)',
                modal: '0 24px 80px -24px rgba(26, 20, 18, 0.55)',
            },
            backgroundImage: {
                'tibeb-edge': 'repeating-linear-gradient(90deg, #D4A017 0 8px, transparent 8px 16px, #C45C3E 16px 24px, transparent 24px 32px)',
                'habesha-fade': 'radial-gradient(circle at 20% 20%, rgba(212,160,23,0.12), transparent 35%), radial-gradient(circle at 80% 0%, rgba(196,92,62,0.10), transparent 30%)',
            },
            keyframes: {
                'fade-rise': {
                    '0%': { opacity: '0', transform: 'translateY(8px) scale(0.97)' },
                    '100%': { opacity: '1', transform: 'translateY(0) scale(1)' },
                },
            },
            animation: {
                'fade-rise': 'fade-rise 0.28s ease-out',
            },
        },
    },

    plugins: [forms],
};
