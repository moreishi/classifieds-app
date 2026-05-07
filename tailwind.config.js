import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                neon: {
                    pink: '#ff2d95',
                    green: '#00ff88',
                    blue: '#00d4ff',
                    purple: '#a855f7',
                    yellow: '#ffd700',
                    orange: '#ff6b35',
                    lime: '#39ff14',
                    cyan: '#22d3ee',
                },
            },
            keyframes: {
                'glow-pulse': {
                    '0%, 100%': { boxShadow: '0 0 8px rgba(255, 45, 149, 0.4), 0 0 16px rgba(255, 45, 149, 0.2)' },
                    '50%': { boxShadow: '0 0 16px rgba(255, 45, 149, 0.7), 0 0 32px rgba(255, 45, 149, 0.4)' },
                },
                'glow-pulse-green': {
                    '0%, 100%': { boxShadow: '0 0 8px rgba(0, 255, 136, 0.4), 0 0 16px rgba(0, 255, 136, 0.2)' },
                    '50%': { boxShadow: '0 0 16px rgba(0, 255, 136, 0.7), 0 0 32px rgba(0, 255, 136, 0.4)' },
                },
                'glow-pulse-blue': {
                    '0%, 100%': { boxShadow: '0 0 8px rgba(0, 212, 255, 0.4), 0 0 16px rgba(0, 212, 255, 0.2)' },
                    '50%': { boxShadow: '0 0 16px rgba(0, 212, 255, 0.7), 0 0 32px rgba(0, 212, 255, 0.4)' },
                },
                'glow-pulse-purple': {
                    '0%, 100%': { boxShadow: '0 0 8px rgba(168, 85, 247, 0.4), 0 0 16px rgba(168, 85, 247, 0.2)' },
                    '50%': { boxShadow: '0 0 16px rgba(168, 85, 247, 0.7), 0 0 32px rgba(168, 85, 247, 0.4)' },
                },
                'fade-in-up': {
                    '0%': { opacity: '0', transform: 'translateY(16px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'scale-in': {
                    '0%': { opacity: '0', transform: 'scale(0.95)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
            },
            animation: {
                'glow-pulse': 'glow-pulse 2s ease-in-out infinite',
                'glow-pulse-green': 'glow-pulse-green 2s ease-in-out infinite',
                'glow-pulse-blue': 'glow-pulse-blue 2s ease-in-out infinite',
                'glow-pulse-purple': 'glow-pulse-purple 2s ease-in-out infinite',
                'fade-in-up': 'fade-in-up 0.4s ease-out',
                'scale-in': 'scale-in 0.3s ease-out',
            },
        },
    },

    plugins: [forms],
};
