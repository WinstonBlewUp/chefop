@props(['active', 'color' => 'indigo'])

@php
// Couleurs basées sur la route
$borderColor = match($color) {
    'green' => 'border-green-500',
    'purple' => 'border-purple-500',
    'orange' => 'border-orange-500',
    'blue' => 'border-blue-500',
    'yellow' => 'border-yellow-500',
    default => 'border-indigo-400',
};

$textColor = match($color) {
    'green' => 'text-green-700',
    'purple' => 'text-purple-700',
    'orange' => 'text-orange-700',
    'blue' => 'text-blue-700',
    'yellow' => 'text-yellow-700',
    default => 'text-indigo-700',
};

$bgColor = match($color) {
    'green' => 'bg-green-50',
    'purple' => 'bg-purple-50',
    'orange' => 'bg-orange-50',
    'blue' => 'bg-blue-50',
    'yellow' => 'bg-yellow-50',
    default => 'bg-indigo-50',
};

$focusTextColor = match($color) {
    'green' => 'focus:text-green-800',
    'purple' => 'focus:text-purple-800',
    'orange' => 'focus:text-orange-800',
    'blue' => 'focus:text-blue-800',
    'yellow' => 'focus:text-yellow-800',
    default => 'focus:text-indigo-800',
};

$focusBgColor = match($color) {
    'green' => 'focus:bg-green-100',
    'purple' => 'focus:bg-purple-100',
    'orange' => 'focus:bg-orange-100',
    'blue' => 'focus:bg-blue-100',
    'yellow' => 'focus:bg-yellow-100',
    default => 'focus:bg-indigo-100',
};

$focusBorderColor = match($color) {
    'green' => 'focus:border-green-700',
    'purple' => 'focus:border-purple-700',
    'orange' => 'focus:border-orange-700',
    'blue' => 'focus:border-blue-700',
    'yellow' => 'focus:border-yellow-700',
    default => 'focus:border-indigo-700',
};

$classes = ($active ?? false)
            ? "block w-full ps-3 pe-4 py-2 border-l-4 {$borderColor} text-start text-base font-medium {$textColor} {$bgColor} focus:outline-none {$focusTextColor} {$focusBgColor} {$focusBorderColor} transition duration-150 ease-in-out"
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
