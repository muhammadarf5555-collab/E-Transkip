@props(['active'])

@php
if ($active ?? false) {
    $classes = 'flex items-center gap-3 w-full px-3.5 py-2.5 text-start text-sm font-semibold text-white bg-white/15 border border-white/20 rounded-xl shadow-sm transition duration-150 ease-in-out';
} else {
    $classes = 'flex items-center gap-3 w-full px-3.5 py-2.5 text-start text-sm font-medium text-emerald-100 hover:text-white hover:bg-white/10 rounded-xl transition duration-150 ease-in-out';
}
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
