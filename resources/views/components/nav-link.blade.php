@props(['active' => false, 'href' => '#'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-4 py-2 bg-green-800 text-white text-sm font-medium rounded-lg focus:outline-none transition duration-150 ease-in-out'
            : 'inline-flex items-center px-4 py-2 text-white text-sm font-medium hover:bg-green-600 rounded-lg focus:outline-none transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['href' => $href, 'class' => $classes]) }}>
    {{ $slot }}
</a>