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

$focusBorderColor = match($color) {
    'green' => 'focus:border-green-700',
    'purple' => 'focus:border-purple-700',
    'orange' => 'focus:border-orange-700',
    'blue' => 'focus:border-blue-700',
    'yellow' => 'focus:border-yellow-700',
    default => 'focus:border-indigo-700',
};

$classes = ($active ?? false)
            ? "inline-flex items-center px-1 pt-1 border-b-2 {$borderColor} text-sm font-medium leading-5 text-gray-900 focus:outline-none {$focusBorderColor} transition duration-150 ease-in-out"
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
