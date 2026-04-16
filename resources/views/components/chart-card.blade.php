@props(['title', 'subtitle' => null, 'chartId' => 'chart'])

<div class="rounded-xl p-6 shadow-card"
     style="background-color: rgb(var(--surface-primary)); border: 1px solid rgb(var(--border-primary));">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="font-semibold" style="color: rgb(var(--text-primary));">{{ $title }}</h3>
            @if($subtitle)
                <p class="text-sm" style="color: rgb(var(--text-secondary));">{{ $subtitle }}</p>
            @endif
        </div>
        <button class="p-2 rounded transition" style="color: rgb(var(--text-secondary));" class="hover:bg-dark-border">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 6a2 2 0 11-4 0 2 2 0 014 0zM10 12a2 2 0 11-4 0 2 2 0 014 0zM10 18a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
        </button>
    </div>
    <div class="relative h-64">
        <canvas id="{{ $chartId }}"></canvas>
    </div>
    {{ $slot }}
</div>
