# WooCommerce + Tailwind CSS Style Guide

This document outlines the design system and component styles used in the WooCommerce child theme with Tailwind CSS integration.

## Colors

### Primary
- Green: `#059669` (text-green-600, bg-green-600)
- Green Dark: `#047857` (hover states, text-green-700, bg-green-700)
- Green Light: `#D1FAE5` (backgrounds, bg-green-50)

### Neutrals
- Black: `#111827` (text-gray-900)
- Dark Gray: `#4B5563` (text-gray-600)
- Medium Gray: `#9CA3AF` (text-gray-400, borders)
- Light Gray: `#F3F4F6` (backgrounds, bg-gray-50)
- White: `#FFFFFF` (backgrounds, text-white)

### Status
- Success: `#10B981` (text-green-500)
- Warning: `#F59E0B` (text-yellow-500)
- Error: `#EF4444` (text-red-500)
- Info: `#3B82F6` (text-blue-500)

## Typography

### Font Family
- Primary: System UI, sans-serif
- Monospace: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace

### Font Sizes & Weights
- Headings: `text-2xl font-bold` (24px)
- Subheadings: `text-xl font-semibold` (20px)
- Body: `text-base` (16px)
- Small: `text-sm` (14px)
- Extra Small: `text-xs` (12px)

### Line Heights
- Normal: `leading-normal` (1.5)
- Relaxed: `leading-relaxed` (1.625)
- Tight: `leading-tight` (1.25)

## Spacing

### Padding & Margin
- Base: `p-4`, `m-4` (16px)
- Small: `p-2`, `m-2` (8px)
- Large: `p-6`, `m-6` (24px)
- X-Large: `p-8`, `m-8` (32px)

### Container Widths
- Full: `w-full`
- Container: `max-w-7xl mx-auto px-4 sm:px-6 lg:px-8`
- Narrow: `max-w-3xl mx-auto`

## Buttons

### Primary Button
```html
<button class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
  Button Text
</button>
```

### Secondary Button
```html
<button class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2 px-4 rounded-md transition-colors duration-200">
  Button Text
</button>
```

### Text Button
```html
<button class="text-green-600 hover:text-green-700 font-medium hover:underline transition-colors duration-200">
  Button Text
</button>
```

## Form Elements

### Text Input
```html
<div class="mb-4">
  <label for="input-id" class="block text-sm font-medium text-gray-700 mb-1">
    Label
  </label>
  <input type="text" id="input-id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
</div>
```

### Select Dropdown
```html
<div class="mb-4">
  <label for="select-id" class="block text-sm font-medium text-gray-700 mb-1">
    Label
  </label>
  <select id="select-id" class="block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-green-500 focus:outline-none focus:ring-green-500 sm:text-sm">
    <option>Option 1</option>
    <option>Option 2</option>
  </select>
</div>
```

### Checkbox
```html
<div class="flex items-center">
  <input id="checkbox-id" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
  <label for="checkbox-id" class="ml-2 block text-sm text-gray-700">
    Checkbox label
  </label>
</div>
```

## Cards

### Product Card
```html
<div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200">
  <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-t-lg bg-gray-100">
    <img src="product-image.jpg" alt="Product" class="h-full w-full object-cover object-center">
  </div>
  <div class="p-4">
    <h3 class="text-sm font-medium text-gray-900">Product Name</h3>
    <p class="mt-1 text-sm text-gray-500">Category</p>
    <p class="mt-2 text-sm font-medium text-gray-900">$99.99</p>
  </div>
</div>
```

### Info Card
```html
<div class="bg-white overflow-hidden shadow rounded-lg">
  <div class="px-4 py-5 sm:p-6">
    <h3 class="text-lg font-medium leading-6 text-gray-900">Card Title</h3>
    <div class="mt-2 max-w-xl text-sm text-gray-500">
      <p>Card content goes here.</p>
    </div>
    <div class="mt-4">
      <button type="button" class="inline-flex items-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
        Button
      </button>
    </div>
  </div>
</div>
```

## Alerts & Notifications

### Success Alert
```html
<div class="rounded-md bg-green-50 p-4">
  <div class="flex">
    <div class="flex-shrink-0">
      <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
      </svg>
    </div>
    <div class="ml-3">
      <p class="text-sm font-medium text-green-800">Success message goes here</p>
    </div>
  </div>
</div>
```

### Error Alert
```html
<div class="rounded-md bg-red-50 p-4">
  <div class="flex">
    <div class="flex-shrink-0">
      <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
      </svg>
    </div>
    <div class="ml-3">
      <p class="text-sm font-medium text-red-800">Error message goes here</p>
    </div>
  </div>
</div>
```

## WooCommerce Specific Components

### Product Grid
```html
<div class="grid grid-cols-1 gap-y-10 gap-x-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 xl:gap-x-8">
  <!-- Product items go here -->
</div>
```

### Product Price
```html
<div class="mt-1 flex items-center justify-between">
  <p class="text-sm font-medium text-gray-900">$99.99</p>
  <p class="text-sm font-medium text-gray-500 line-through">$129.99</p>
  <p class="ml-2 text-sm font-medium text-green-600">Save 20%</p>
</div>
```

### Star Rating
```html
<div class="mt-1 flex items-center">
  <div class="flex items-center">
    <!-- Full stars -->
    <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
    </svg>
    <!-- Half star (example) -->
    <div class="relative">
      <div class="absolute inset-0 w-2 overflow-hidden">
        <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
        </svg>
      </div>
      <svg class="h-4 w-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
      </svg>
    </div>
    <!-- Empty stars -->
    <svg class="h-4 w-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
    </svg>
  </div>
  <p class="ml-2 text-sm text-gray-500">(24 reviews)</p>
</div>
```

## Responsive Design

### Breakpoints
- `sm`: 640px
- `md`: 768px
- `lg`: 1024px
- `xl`: 1280px
- `2xl`: 1536px

### Utility Classes
- Hide on mobile: `hidden sm:block`
- Stack on mobile, row on desktop: `flex flex-col md:flex-row`
- Responsive grid: `grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3`
- Responsive padding: `p-4 md:p-6 lg:p-8`

## Animations & Transitions

### Hover Effects
```html
<button class="transition-colors duration-200 hover:bg-green-700">
  Hover me
</button>
```

### Focus States
```html
<input class="focus:ring-2 focus:ring-green-500 focus:border-green-500">
```

## Accessibility

### Screen Reader Only
```html
<span class="sr-only">Screen reader only text</span>
```

### Focus Visible
```html
<button class="focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
  Accessible button
</button>
```

## Best Practices

1. **Consistency**: Use the same spacing, colors, and components throughout the site.
2. **Responsive**: Ensure all components work well on all screen sizes.
3. **Accessibility**: Follow WCAG guidelines for color contrast, keyboard navigation, and ARIA attributes.
4. **Performance**: Optimize images and use Tailwind's purge feature to reduce CSS file size.
5. **Documentation**: Keep this style guide updated with new components and patterns.
