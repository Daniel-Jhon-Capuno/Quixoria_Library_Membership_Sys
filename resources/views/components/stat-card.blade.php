@props(['title', 'value', 'subtitle' => null, 'icon' => null, 'color' => 'primary', 'trend' => null])

<div class="rounded-xl p-6 shadow-card hover:shadow-glow transition"
     style="background-color: rgb(var(--surface-primary)); border: 1px solid rgb(var(--border-primary));">
    <div class="flex items-start justify-between mb-4">
        <div>
            <p class="text-sm font-medium mb-1" style="color: rgb(var(--text-secondary));">{{ $title }}</p>
            <p class="text-3xl font-bold" style="color: rgb(var(--text-primary));">{{ $value }}</p>
            @if($subtitle)
                <p class="text-xs mt-2" style="color: rgb(var(--text-secondary));">{{ $subtitle }}</p>
            @endif
        </div>
        @if($icon)
            <div class="p-3 rounded-lg" style="background: linear-gradient(to bottom right, rgba(100, 200, 255, 0.1), rgba(0, 255, 200, 0.1));">
                {!! $icon !!}
            </div>
        @endif
    </div>

    @if($trend)
        <div class="flex items-center gap-2 text-sm">
            @if($trend['direction'] === 'up')
                <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414-1.414L13.586 7H12z" clip-rule="evenodd" />
                </svg>
                <span class="text-green-400">{{ $trend['value'] }}% increase</span>
            @else
                <svg class="w-4 h-4 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12 13a1 1 0 110 2H7a1 1 0 01-1-1V9a1 1 0 112 0v3.586l4.293-4.293a1 1 0 011.414 1.414L9.414 13H12z" clip-rule="evenodd" />
                </svg>
                <span class="text-red-400">{{ $trend['value'] }}% decrease</span>
            @endif
            <span style="color: rgb(var(--text-secondary));">vs last month</span>
        </div>
    @endif

    {{ $slot }}
</div>
