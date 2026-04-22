<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">Book Catalog</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search and Filters -->
            <div class="bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('student.book-catalog.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <!-- Search -->
                        <div class="lg:col-span-2">
                            <label for="search" class="block text-sm font-medium text-gray-200">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                placeholder="Title, author, or ISBN"
                                class="mt-1 block w-full bg-slate-900 border border-slate-700 text-gray-100 rounded-md shadow-sm">
                        </div>

                        <!-- Genre Filter -->
                        <div>
                            <label for="genre" class="block text-sm font-medium text-gray-200">Genre</label>
                            <select name="genre" id="genre" class="mt-1 block w-full bg-slate-900 border border-slate-700 text-gray-100 rounded-md shadow-sm">
                                <option value="">All Genres</option>
                                @foreach($genres as $genreOption)
                                    <option value="{{ $genreOption }}" {{ request('genre') == $genreOption ? 'selected' : '' }}>
                                        {{ $genreOption }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-200">Category</label>
                            <select name="category" id="category" class="mt-1 block w-full bg-slate-900 border border-slate-700 text-gray-100 rounded-md shadow-sm">
                                <option value="">All Categories</option>
                                @foreach($categories as $categoryOption)
                                    <option value="{{ $categoryOption }}" {{ request('category') == $categoryOption ? 'selected' : '' }}>
                                        {{ $categoryOption }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Availability Filter -->
                        <div>
                            <label for="availability" class="block text-sm font-medium text-gray-200">Availability</label>
                            <select name="availability" id="availability" class="mt-1 block w-full bg-slate-900 border border-slate-700 text-gray-100 rounded-md shadow-sm">
                                <option value="">All Books</option>
                                <option value="available" {{ request('availability') == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="unavailable" {{ request('availability') == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-end space-x-2 lg:col-span-5">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-cyan-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-cyan-500">
                                Search
                            </button>
                            <a href="{{ route('student.book-catalog.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-700 border border-transparent rounded-md font-semibold text-xs text-gray-100 uppercase tracking-widest hover:bg-slate-600">
                                Clear Filters
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Books Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($books as $book)
                <div class="bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="aspect-w-3 aspect-h-4 bg-slate-600">
                        @if($book->cover_image)
                            <img src="{{ asset('storage/' . $book->cover_image) }}"
                                 alt="{{ $book->title }}"
                                 class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-slate-700 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-50 mb-1 line-clamp-2">
                            <a href="{{ route('student.book-catalog.show', $book) }}" class="hover:text-blue-600">
                                {{ $book->title }}
                            </a>
                        </h3>

                        @if(isset($weeklyBorrows, $tierLimit))
                            @php $atWeeklyLimit = $weeklyBorrows >= $tierLimit; @endphp
                            <div class="text-xs mb-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $atWeeklyLimit ? 'bg-red-800 text-red-200' : 'bg-slate-600 text-gray-100' }}">
                                    Weekly: {{ $weeklyBorrows }}/{{ $tierLimit }}
                                </span>
                            </div>
                        @endif

                        <p class="text-sm text-gray-300 mb-2">by {{ $book->author }}</p>

                            @if($book->genre)
                            <p class="text-xs text-gray-300 mb-1">
                                <span class="font-medium">Genre:</span> {{ $book->genre }}
                            </p>
                        @endif

                            @if($book->category)
                            <p class="text-xs text-gray-300 mb-3">
                                <span class="font-medium">Category:</span> {{ $book->category }}
                            </p>
                        @endif

                        <div class="flex items-center justify-between">
                            @php
                                $activeBorrows = \App\Models\BorrowRequest::where('book_id', $book->id)
                                    ->whereIn('status', ['active', 'overdue'])
                                    ->count();
                                $availableCopies = $book->total_copies - $activeBorrows;
                            @endphp

                            <div class="flex items-center">
                                @if($availableCopies > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-600 text-gray-100">
                                        Available ({{ $availableCopies }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-800 text-red-200">
                                        Unavailable
                                    </span>
                                @endif
                            </div>

                            @php $atWeeklyLimit = isset($weeklyBorrows, $tierLimit) && $weeklyBorrows >= $tierLimit; @endphp
                            <div class="flex items-center gap-2">
                                    @if($atWeeklyLimit)
                                        <button type="button" disabled title="Reached weekly limit" class="inline-flex items-center px-3 py-1 bg-slate-700 border border-transparent rounded-md text-xs font-medium text-gray-300 cursor-not-allowed">
                                        View Details
                                    </button>
                                @else
                                    <a href="{{ route('student.book-catalog.show', $book) }}"
                                       class="inline-flex items-center px-3 py-1 bg-gray-700 border border-transparent rounded-md text-xs font-medium text-gray-100 hover:bg-gray-600 view-details-link"
                                       data-book-id="{{ $book->id }}">
                                        View Details
                                    </a>
                                @endif
                                {{-- Weekly usage badge and progress bar --}}
                                    @if(isset($weeklyBorrows, $tierLimit))
                                    @php $pct = $tierLimit ? min(100, intval(($weeklyBorrows / $tierLimit) * 100)) : 0; @endphp
                                    <div class="ml-2 w-28">
                                        <div class="flex items-center justify-between text-xs mb-1">
                                            <span class="text-gray-300">{{ $weeklyBorrows }}/{{ $tierLimit }}</span>
                                            <span class="text-gray-300">{{ $pct }}%</span>
                                        </div>
                                        <div class="w-full bg-slate-700/60 rounded h-2 overflow-hidden">
                                            <div class="bg-cyan-400 h-2" style="width: {{ $pct }}%" data-weekly-progress="{{ $book->id }}"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full">
                    <div class="bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-100">No books found</h3>
                            <p class="mt-1 text-sm text-gray-400">Try adjusting your search or filter criteria.</p>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($books->hasPages())
            <div class="mt-8">
                {{ $books->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>

<script>
    (function(){
        const usageUrl = '{{ route('student.borrow-usage') }}';

        async function fetchUsage() {
                try {
                const res = await fetch(usageUrl, { credentials: 'same-origin' });
                if (!res.ok) return;
                const data = await res.json();
                const weeklyBorrows = data.weeklyBorrows;
                const tierLimit = data.tierLimit;
                const atLimit = data.atLimit;
                const monthlyBorrows = data.monthlyBorrows;
                const monthlyLimit = data.monthlyLimit;
                const atMonthlyLimit = data.atMonthlyLimit;

                // Update all badges and progress bars
                document.querySelectorAll('[data-weekly-progress]').forEach(el => {
                    const pct = tierLimit ? Math.min(100, Math.floor((weeklyBorrows / tierLimit) * 100)) : 0;
                    el.style.width = pct + '%';
                    // update nearby text if present
                    const parent = el.closest('.w-28');
                    if (parent) {
                        const text = parent.querySelector('.flex.items-center.justify-between.text-xs');
                        if (text) {
                            // first child span contains X/Y
                            text.querySelector('span') && (text.querySelector('span').textContent = weeklyBorrows + '/' + tierLimit);
                        }
                    }
                });

                // Enable/disable view details links (disable if either weekly or monthly limit reached)
                const disableForLimit = atLimit || atMonthlyLimit;
                document.querySelectorAll('.view-details-link').forEach(a => {
                    if (disableForLimit) {
                        // replace link with disabled button
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.disabled = true;
                        btn.title = atMonthlyLimit ? 'Reached monthly limit' : 'Reached weekly limit';
                        btn.className = 'inline-flex items-center px-3 py-1 bg-gray-300 border border-transparent rounded-md text-xs font-medium text-gray-700 cursor-not-allowed';
                        btn.textContent = 'View Details';
                        a.replaceWith(btn);
                    }
                });

            } catch (e) {
                // fail silently
                console.error('Failed to fetch borrow usage', e);
            }
        }

        // If Laravel Echo is available, subscribe to private user channel for real-time updates
        const CURRENT_USER_ID = {{ auth()->check() ? auth()->id() : 'null' }};
        if (window.Echo && CURRENT_USER_ID) {
            try {
                window.Echo.private('users.' + CURRENT_USER_ID)
                    .listen('BorrowUsageUpdated', function(e) {
                        // Update badges/progress based on event payload
                        const weeklyBorrows = e.weeklyBorrows;
                        const tierLimit = e.tierLimit;
                        const monthlyBorrows = e.monthlyBorrows;
                        const monthlyLimit = e.monthlyLimit;
                        const atMonthlyLimit = e.atMonthlyLimit;
                        const pct = tierLimit ? Math.min(100, Math.floor((weeklyBorrows / tierLimit) * 100)) : 0;
                        document.querySelectorAll('[data-weekly-progress]').forEach(el => {
                            el.style.width = pct + '%';
                            const parent = el.closest('.w-28');
                            if (parent) {
                                const text = parent.querySelector('.flex.items-center.justify-between.text-xs');
                                if (text && text.querySelector('span')) {
                                    text.querySelector('span').textContent = weeklyBorrows + '/' + tierLimit;
                                }
                            }
                        });

                        const disableForLimit = e.atLimit || atMonthlyLimit;
                        if (disableForLimit) {
                            document.querySelectorAll('.view-details-link').forEach(a => {
                                const btn = document.createElement('button');
                                btn.type = 'button';
                                btn.disabled = true;
                                btn.title = atMonthlyLimit ? 'Reached monthly limit' : 'Reached weekly limit';
                                btn.className = 'inline-flex items-center px-3 py-1 bg-gray-300 border border-transparent rounded-md text-xs font-medium text-gray-700 cursor-not-allowed';
                                btn.textContent = 'View Details';
                                a.replaceWith(btn);
                            });
                        }
                    });
            } catch (e) {
                // ignore
            }
        }

        // Initial fetch and periodic polling (every 30s)
        fetchUsage();
        setInterval(fetchUsage, 30000);
    })();
</script>