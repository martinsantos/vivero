// Initialize Tailwind CSS with configuration
if (typeof tailwind !== 'undefined') {
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          primary: '#10B981',
          'primary-dark': '#059669',
          'text-primary': '#1F2937',
          'text-secondary': '#6B7280',
          'border-color': '#E5E7EB',
        },
        fontFamily: {
          sans: ['Inter', 'sans-serif'],
          display: ['Poppins', 'sans-serif'],
        },
        boxShadow: {
          'sm': '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
          'md': '0 4px 6px -1px rgba(0, 0, 0, 0.1)',
          'lg': '0 10px 15px -3px rgba(0, 0, 0, 0.1)',
        },
      },
    },
    // Note: Plugins require build step
  };
}
