@props(['type' => 'submit', 'color' => 'blue'])

@php
    $colorClasses = [
        'blue' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
        'green' => 'bg-green-600 hover:bg-green-700 focus:ring-green-500',
        'red' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
        'gray' => 'bg-gray-600 hover:bg-gray-700 focus:ring-gray-500',
    ];
    
    $classes = $colorClasses[$color] ?? $colorClasses['blue'];
@endphp

<button 
    type="{{ $type }}"
    x-data="{ loading: false }"
    x-on:click="if (!loading) { loading = true; $el.form.submit(); }"
    :disabled="loading"
    :class="{ 'opacity-50 cursor-not-allowed': loading }"
    {{ $attributes->merge(['class' => "inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 {$classes}"]) }}>
    
    <!-- Loading Spinner -->
    <svg x-show="loading" x-cloak class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
    
    <!-- Button Text -->
    <span x-show="!loading">{{ $slot }}</span>
    <span x-show="loading" x-cloak>Processing...</span>
</button>