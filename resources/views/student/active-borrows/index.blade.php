<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Active Borrows</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Sort Options -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('student.active-borrows.index') }}" class="flex flex-wrap gap-4 items-end">
                        <div>
                            <label for="sort" class="block text-sm font-medium text-gray-700">Sort by</label>
                            <select name="sort" id="sort" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="soonest" {{ request('sort', 'soonest') == 'soonest' ? 'selected' : '' }}>Soonest Due</option>
                                <option value="furthest" {{ request('sort') == 'furthest' ? 'selected' : '' }}>Furthest Due</option>
                                <option value="overdue" {{ request('sort') == 'overdue' ? 'selected' : '' }}>Overdue First</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">Sort</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Active Borrows -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($activeBorrows as $borrow)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Book Cover -->
                        <div class="aspect-w-3 aspect-h-4 bg-gray-200 mb-4">
                            @if($borrow->book->cover_image)
                                <img src="{{ asset('storage/' . $borrow->book->cover_image) }}"
                                     alt="{{ $borrow->book->title }}"
                                     class="w-full h-32 object-cover rounded-lg">
                            @else
                                <div class="w-full h-32 bg-gray-300 rounded-lg flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Book Info -->
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $borrow->book->title }}</h3>
                        <p class="text-sm text-gray-600 mb-3">by {{ $borrow->book->author }}</p>

                        <!-- Due Date & Countdown -->
                        <div class="mb-4">
                            <div class="text-sm text-gray-500 mb-1">Due Date</div>
                            <div class="text-sm font-medium">
                                {{ $borrow->due_at->format('M j, Y') }}
                            </div>
                            <div class="mt-1">
                                @if($borrow->due_at->isPast())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        OVERDUE
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ $borrow->due_at->diffForHumans() }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Renewal Info -->
                        <div class="mb-4">
                            <div class="text-sm text-gray-500">Renewals Used</div>
                            <div class="text-sm font-medium">
                                {{ $borrow->renewals_used }} / {{ auth()->user()->subscription->membershipTier->renewal_limit }}
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex space-x-2">
                            @php
                                $canRenew = $borrow->renewals_used < auth()->user()->subscription->membershipTier->renewal_limit
                                          && !$borrow->due_at->isPast();
                            @endphp

                            @if($canRenew)
                                <form method="POST" action="{{ route('student.active-borrows.renew', $borrow->id) }}" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full inline-flex justify-center items-center px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white hover:bg-blue-700">
                                        Renew Book
                                    </button>
                                </form>
                            @else
                                <div class="flex-1 px-3 py-2 bg-gray-300 border border-transparent rounded-md text-xs text-gray-500 text-center">
                                    @if($borrow->due_at->isPast())
                                        Cannot renew overdue books
                                    @else
                                        Max renewals reached
                                    @endif
                                </div>
                            @endif

                            <a href="{{ route('student.book-catalog.show', $borrow->book) }}"
                               class="inline-flex items-center px-3 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white hover:bg-gray-700">
                                View Book
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No active borrows</h3>
                            <p class="mt-1 text-sm text-gray-500">You don't have any books currently borrowed.</p>
                            <div class="mt-6">
                                <a href="{{ route('student.book-catalog.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                    Browse Books
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Summary Stats -->
            @if($activeBorrows->count() > 0)
            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Borrow Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $activeBorrows->count() }}</div>
                            <div class="text-sm text-gray-500">Total Active Borrows</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600">{{ $activeBorrows->where('due_at', '<', now())->count() }}</div>
                            <div class="text-sm text-gray-500">Overdue Books</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $activeBorrows->where('due_at', '>=', now())->count() }}</div>
                            <div class="text-sm text-gray-500">On Time</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>