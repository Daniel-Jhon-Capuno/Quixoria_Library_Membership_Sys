import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            fontWeight: {
                normal: '400',
                medium: '500', 
                semibold: '600',
                bold: '700',
                black: '900',
            },
            colors: {
                // Brand colors
                'primary': '#ec4899',
                'primary-dark': '#be185d',
                'secondary': '#06b6d4',
                'accent': '#3b82f6',
                'success': '#10b981',
                'warning': '#f59e0b',
                'danger': '#ef4444',
                
                // Semantic colors
                'bg-primary': 'rgb(var(--bg-primary) / <alpha-value>)',
                'bg-secondary': 'rgb(var(--bg-secondary) / <alpha-value>)',
                'surface-primary': 'rgb(var(--surface-primary) / <alpha-value>)',
                'surface-secondary': 'rgb(var(--surface-secondary) / <alpha-value>)',
                'border-primary': 'rgb(var(--border-primary) / <alpha-value>)',
                'text-primary': 'rgb(var(--text-primary) / <alpha-value>)',
                'text-secondary': 'rgb(var(--text-secondary) / <alpha-value>)',
            },
            boxShadow: {
                'glow': '0 0 20px rgba(236, 72, 153, 0.1)',
                'card': '0 4px 6px rgba(0, 0, 0, 0.3)',
                'card-light': '0 1px 3px rgba(0, 0, 0, 0.1)',
            },
            gridTemplateColumns: {
                'sidebar': '200px 1fr',
                'dashboard': 'repeat(auto-fit, minmax(300px, 1fr))',
            },
        },
    },

    plugins: [forms],
};
