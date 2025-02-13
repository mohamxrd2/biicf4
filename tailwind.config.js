const defaultTheme = require("tailwindcss/defaultTheme");
/** @type {import('tailwindcss').Config} */

export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "node_modules/preline/dist/*.js",
        "./node_modules/flowbite/**/*.js",
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
    ],
    safelist: [
        'peer-checked:bg-purple-600',
        'peer-checked:border-purple-600',
        'peer-checked:text-white',
        'peer-checked:shadow-md',
        'hover:border-purple-300',
        'hover:shadow-sm',
        'text-gray-700',
        'bg-white',
        'from-indigo-500',
        'to-purple-500'
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ["Inter var", ...defaultTheme.fontFamily.sans],
            },
            screens: {
                xs: "360px", // Nouveau breakpoint pour 360px
            },
        },
    },
    plugins: [require("preline/plugin"), require("flowbite/plugin")],
};
