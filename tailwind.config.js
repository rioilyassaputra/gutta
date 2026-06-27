import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Livewire/**/*.php',
        './app/Http/Controllers/**/*.php',
        './app/Models/**/*.php',
    ],

    theme: {
        extend: {
            colors: {
                gutta: {
                    black: '#000000',
                    purple: '#6d28d9',
                    dark: '#4c1d95',
                    light: '#ede9fe',
                    white: '#ffffff',
                    gray: '#111111',
                    border: '#1f1f1f',
                }
            },
            fontFamily: {
                sans: ['Inter', 'Space Grotesk', ...defaultTheme.fontFamily.sans],
                display: ['Space Grotesk', 'Inter', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
