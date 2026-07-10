import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Inter", "Figtree", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                "sidebar-bg": "#111827",
                "sidebar-link": "#d1d5db",
                "sidebar-link-active": "#ffffff",
                "sidebar-link-active-bg": "#1f2937",
                "sidebar-section": "#94a3b8",
                "sidebar-logo": "#f8fafc",
                "sidebar-pro": "#f8fafc",
                "header-bg": "#111827",
                "header-text": "#fff",
                "body-bg": "oklch(0.967 0.001 286.375)",
                "content-bg": "#fff",
                "content-border": "oklch(0.92 0.004 286.32)",
                "text-primary": "oklch(0.37 0.013 285.805)",
                "text-muted": "oklch(0.61 0.008 286)",
                "text-heading": "oklch(0.37 0.013 285.805)",
                primary: "oklch(0.457 0.24 277.023)",
                "primary-hover": "oklch(0.4 0.22 277.023)",
                "primary-soft": "oklch(0.457 0.24 277.023 / 0.12)",
                "primary-foreground": "#fff",
                brand: "oklch(0.457 0.24 277.023)",
                "brand-hover": "oklch(0.4 0.22 277.023)",
                "brand-soft": "oklch(0.457 0.24 277.023 / 0.12)",
                "brand-foreground": "#fff",
                success: "oklch(0.792 0.209 151.711)",
                danger: "oklch(0.577 0.245 27.325)",
                accent: "oklch(0.707 0.165 254.624)",
                "panel-bg": "oklch(0.934 0.001 286)",
                border: "oklch(0.92 0.004 286.32)",
            },
        },
    },

    plugins: [forms],
};
