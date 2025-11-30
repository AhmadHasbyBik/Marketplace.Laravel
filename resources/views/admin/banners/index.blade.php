@extends('layouts.admin')

@section('title', 'Banner Landing Page')

@section('content')
    <div class="space-y-4">
        <h1 class="text-2xl font-semibold">Banner</h1>
        <div class="rounded-3xl border border-slate-800 bg-slate-900/50 p-4 space-y-3">
            @foreach($banners as $banner)
                <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold">{{ $banner->title }}</span>
                        <span class="text-xs text-slate-400">{{ $banner->type }}</span>
                    </div>
                    <p class="text-slate-400">{{ $banner->subtitle }}</p>
                </div>
            @endforeach
        </div>
    </div>
@endsection
