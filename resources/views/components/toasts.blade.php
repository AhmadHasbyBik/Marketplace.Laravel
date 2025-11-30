@php
    $toastMessages = [];
    $sessionToasts = [
        'success' => session('success'),
        'error' => session('error'),
        'warning' => session('warning'),
        'info' => session('info'),
    ];
    $seenMessages = [];

    foreach ($sessionToasts as $type => $payload) {
        if (blank($payload)) {
            continue;
        }

        $message = is_string($payload) ? $payload : json_encode($payload);

        if (in_array($message, $seenMessages, true)) {
            continue;
        }

        $toastMessages[] = ['type' => $type, 'message' => $message];
        $seenMessages[] = $message;
    }

    foreach (['status' => 'success', 'message' => 'info'] as $key => $type) {
        $payload = session($key);

        if (blank($payload)) {
            continue;
        }

        $message = is_string($payload) ? $payload : json_encode($payload);

        if (in_array($message, $seenMessages, true)) {
            continue;
        }

        $toastMessages[] = ['type' => $type, 'message' => $message];
        $seenMessages[] = $message;
    }

    if ($errors->any()) {
        $firstError = $errors->first();

        if (!in_array($firstError, $seenMessages, true)) {
            $toastMessages[] = ['type' => 'error', 'message' => $firstError];
        }
    }
@endphp

@if (!empty($toastMessages))
    <div aria-live="assertive" class="pointer-events-none fixed inset-0 z-50 flex items-start justify-end px-4 py-6 sm:px-6">
        <div class="flex w-full max-w-sm flex-col gap-3">
            @foreach ($toastMessages as $toast)
                @php
                    $palette = [
                        'error' => [
                            'border' => 'border-rose-500/80',
                            'iconBg' => 'bg-rose-500/10',
                            'iconText' => 'text-rose-600',
                        ],
                        'warning' => [
                            'border' => 'border-amber-500/80',
                            'iconBg' => 'bg-amber-500/10',
                            'iconText' => 'text-amber-600',
                        ],
                        'info' => [
                            'border' => 'border-sky-500/80',
                            'iconBg' => 'bg-sky-500/10',
                            'iconText' => 'text-sky-600',
                        ],
                        'success' => [
                            'border' => 'border-emerald-500/80',
                            'iconBg' => 'bg-emerald-500/10',
                            'iconText' => 'text-emerald-600',
                        ],
                    ];

                    $variant = $palette[$toast['type']] ?? $palette['success'];
                @endphp

                <div
                    x-data="toast()"
                    x-init="init()"
                    x-show="visible"
                    x-transition:enter="transform duration-300"
                    x-transition:enter-start="translate-y-6 opacity-0"
                    x-transition:enter-end="translate-y-0 opacity-100"
                    x-transition:leave="transition duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="pointer-events-auto flex w-full items-start gap-3 rounded-2xl border-l-4 bg-white/95 px-5 py-4 shadow-[0_25px_45px_-20px_rgba(15,23,42,0.95)] text-slate-900 {{ $variant['border'] }}"
                >
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl {{ $variant['iconBg'] }} {{ $variant['iconText'] }}">
                        @switch($toast['type'])
                            @case('error')
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 8v4" />
                                    <path d="M12 16h.01" />
                                </svg>
                                @break
                            @case('warning')
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 8v4" />
                                    <path d="M12 16h.01" />
                                </svg>
                                @break
                            @case('info')
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 7h.01" />
                                    <path d="M11 11h2" />
                                    <path d="M12 14h.01" />
                                </svg>
                                @break
                            @default
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12l4 4L19 7" />
                                </svg>
                        @endswitch
                    </span>

                    <div class="flex flex-1 items-center text-sm leading-relaxed">
                        <p class="font-semibold text-slate-900">{{ $toast['message'] }}</p>
                    </div>

                    <button
                        type="button"
                        class="text-slate-500 transition hover:text-slate-700"
                        @click="close()"
                        aria-label="Dismiss toast"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>
            @endforeach
        </div>
    </div>
@endif
