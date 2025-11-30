@props([
    'headers' => [],
])

<div class="rounded-3xl border border-slate-200 bg-white/70 p-4 shadow-sm shadow-slate-100">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left">
            <thead class="text-[11px] font-semibold uppercase tracking-[0.3em] text-slate-500">
                <tr>
                    @foreach($headers as $header)
                        <th class="px-4 py-3">{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="text-sm text-slate-700">
                {{ $body ?? '' }}
            </tbody>
        </table>
    </div>
</div>
