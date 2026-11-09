@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3.5 pe-4 py-2.5 border-l-4 border-emerald-600 dark:border-emerald-400 text-start text-sm font-bold text-emerald-800 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 rounded-r-xl transition duration-150 ease-in-out'
            : 'block w-full ps-3.5 pe-4 py-2.5 border-l-4 border-transparent text-start text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-emerald-700 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/60 rounded-r-xl transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
