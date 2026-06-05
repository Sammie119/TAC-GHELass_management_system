/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
    ],
    safelist: [
        'lg:grid-cols-3',
        'lg:col-span-2',
        'lg:col-span-1',
        'sm:grid-cols-2',
        'lg:grid-cols-4',
        'bg-blue-50', 'text-blue-600', 'text-blue-700',
        'bg-green-50', 'bg-green-100', 'text-green-600', 'text-green-700', 'text-green-800',
        'bg-red-50', 'text-red-500', 'text-red-700',
        'bg-purple-50', 'text-purple-600',
        'bg-amber-50', 'text-amber-600',
        'bg-yellow-50', 'bg-yellow-100', 'text-yellow-600', 'text-yellow-700', 'text-yellow-800',
        'bg-orange-500',
        'bg-gray-100', 'text-gray-500',
        'border-blue-600', 'border-transparent',
        'border-green-200', 'border-red-200', 'border-yellow-200',
        'font-medium', 'animate-pulse',
        'group',
        'group-hover:opacity-100',
        'opacity-0',
    ],
    theme: {
        extend: {},
    },
    plugins: [],
}
