@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'class' => '',
    'attrs' => '',
])

@php
    $variantClasses = [
        'primary' => 'bg-[#0EA5E9] text-white border-transparent shadow-lg shadow-[#0ea5e980] hover:bg-sky-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-300',
        'secondary' => 'border border-slate-200 bg-white text-slate-700 hover:border-slate-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-300',
        'danger' => 'bg-[#EF4444] text-white border-transparent shadow-sm shadow-[#ef4444c7] hover:bg-[#dc2626] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-300',
    ];
    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-5 py-3 text-sm',
    ];
    $classes = trim('inline-flex items-center justify-center gap-2 rounded-2xl font-semibold transition ' . ($variantClasses[$variant] ?? $variantClasses['primary']) . ' ' . ($sizeClasses[$size] ?? $sizeClasses['md']) . ' ' . $class);
    $additionalAttributes = $attrs ? trim($attrs) . ' ' : '';
@endphp

<button type="{{ $type }}" {!! $additionalAttributes !!}class="{{ $classes }}">
    {{ $slot ?? 'Button' }}
</button>
