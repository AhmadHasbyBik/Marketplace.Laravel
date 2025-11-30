@props([
    'id',
    'title' => '',
    'description' => null,
    'size' => 'max-w-2xl',
])

<div id="{{ $id }}" data-modal class="hidden fixed inset-0 z-40 flex items-center justify-center px-4 py-8" aria-hidden="true">
    <div data-modal-close class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm h-full"></div>
    <div class="relative w-full {{ $size }}">
        <div class="rounded-3xl bg-white p-6 shadow-2xl shadow-slate-200">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">{{ $title }}</h3>
                    @if($description)
                        <p class="text-sm text-slate-500">{{ $description }}</p>
                    @endif
                </div>
                <button type="button" data-modal-close class="rounded-full border border-slate-200 bg-white p-2 text-slate-500 transition hover:border-slate-300 hover:text-slate-900">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>
            <div class="mt-5 space-y-4">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
