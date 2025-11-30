@props([
    'title' => null,
    'eyebrow' => null,
    'meta' => null,
    'class' => '',
])

@php
    $classes = trim('rounded-3xl border border-slate-200 bg-white/80 p-5 shadow-sm shadow-slate-200 ' . $class);
@endphp

<article class="{{ $classes }}">
    @if($title)
        <header class="flex items-start justify-between gap-4">
            <div>
                @if($eyebrow)
                    <p class="text-xs uppercase tracking-[0.4em] text-slate-400">{{ $eyebrow }}</p>
                @endif
                <p class="text-lg font-semibold text-slate-900">{{ $title }}</p>
            </div>
            @if($meta)
                <span class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">{{ $meta }}</span>
            @endif
        </header>
    @endif
    <div class="mt-4 space-y-3">
        {{ $slot }}
    </div>
</article>
