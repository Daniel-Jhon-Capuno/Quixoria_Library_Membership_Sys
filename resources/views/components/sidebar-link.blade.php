@props(['href', 'icon', 'label', 'active' => false])

@php
    $isActive = request()->url() === url($href);
@endphp

<a href="{{ $href }}"
   class="flex items-center gap-3 px-4 py-3 rounded-lg transition @if($isActive) font-semibold @endif"
   style="@if($isActive) background: linear-gradient(to right, rgba(100, 200, 255, 0.2), rgba(0, 255, 200, 0.1)); color: rgb(var(--accent-primary)); border: 1px solid rgba(100, 200, 255, 0.3); @else background: none; color: rgb(var(--text-secondary)); border: 1px solid transparent; @endif"
   @if(!$isActive) onmouseover="this.style.backgroundColor = 'rgba(100, 200, 255, 0.08)'" @endif
   @if(!$isActive) onmouseout="this.style.backgroundColor = 'transparent'" @endif>
    @switch($icon)
        @case('chart-bar')
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
            </svg>
            @break
        @case('users')
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM9 12a6 6 0 11-12 0 6 6 0 0112 0z"></path>
            </svg>
            @break
        @case('book-open')
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.669 0-3.218.51-4.5 1.385A7.968 7.968 0 009 4.804z"></path>
            </svg>
            @break
        @case('star')
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
            </svg>
            @break
        @case('shopping-cart')
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042L5.960 9.541a1 1 0 00.894.559h7.955a1 1 0 00.894-.559l1.955-6.986A1 1 0 0016 2H3.77L3.553 1.447A1 1 0 002.82 1H1z"></path>
                <path fill-rule="evenodd" d="M16 16a1 1 0 11-2 0 1 1 0 012 0zM6 16a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"></path>
            </svg>
            @break
        @case('document-chart-bar')
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.414l4 4A2 2 0 0116 6.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H7a1 1 0 01-1-1v-6z" clip-rule="evenodd"></path>
            </svg>
            @break
        @case('inbox')
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
            </svg>
            @break
        @case('calendar')
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
            </svg>
            @break
    @endswitch
    <span class="text-sm font-medium sidebar-label">{{ $label }}</span>
</a>
