import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
  ],
  darkMode: 'class',
  theme: {
    extend: {
      fontFamily: {
        display: ['Bebas Neue', 'system-ui', 'sans-serif'],
        body: ['Space Grotesk', 'system-ui', 'sans-serif'],
      },
      colors: {
        black: {
          DEFAULT: '#0B0B0B',
          soft: '#111111',
          card: '#161616',
        },
        orange: {
          DEFAULT: '#FF7A00',
          dark: '#cc6200',
          glow: 'rgba(255, 122, 0, 0.25)',
        },
        violet: {
          DEFAULT: '#7B2EFF',
          dark: '#5a1fd4',
          glow: 'rgba(123, 46, 255, 0.25)',
        },
        white: {
          DEFAULT: '#F5F5F5',
          dim: 'rgba(245, 245, 245, 0.7)',
          faint: 'rgba(245, 245, 245, 0.12)',
        },
      },
      borderRadius: {
        sm: '6px',
        md: '12px',
        lg: '20px',
        xl: '32px',
      },
      transitionTimingFunction: {
        premium: 'cubic-bezier(0.4, 0, 0.2, 1)',
      },
      transitionDuration: {
        slow: '600ms',
      },
      animation: {
        'fade-in-up': 'fadeInUp 0.7s ease forwards',
        'pulse-glow': 'pulseGlow 2s ease-in-out infinite',
        'float': 'float 8s ease-in-out infinite',
        'spin-slow': 'spinSlow 20s linear infinite',
        'shimmer': 'shimmer 2s infinite',
      },
      keyframes: {
        fadeInUp: {
          '0%': { opacity: '0', transform: 'translateY(30px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        pulseGlow: {
          '0%, 100%': { boxShadow: '0 0 20px rgba(255, 122, 0, 0.25)' },
          '50%': { boxShadow: '0 0 40px rgba(255, 122, 0, 0.5)' },
        },
        float: {
          '0%, 100%': { transform: 'translateY(0px)' },
          '50%': { transform: 'translateY(-8px)' },
        },
        spinSlow: {
          '0%': { transform: 'rotate(0deg)' },
          '100%': { transform: 'rotate(360deg)' },
        },
        shimmer: {
          '0%': { backgroundPosition: '-200% center' },
          '100%': { backgroundPosition: '200% center' },
        },
      },
    },
  },
  plugins: [forms],
}