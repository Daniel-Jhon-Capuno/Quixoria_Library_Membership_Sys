<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $book->title }}</h2>
    </x-slot>

    <div class="py-12">
        @if(auth()->check())
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-4">
                @if(auth()->user()->subscription)
                    @if(isset($monthlyBorrows, $monthlyLimit) && $monthlyLimit && $monthlyBorrows >= $monthlyLimit)
                        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-md">You have reached your monthly borrow limit of {{ $monthlyLimit }} books.</div>
                    @endif
                    <div class="bg-green-50 border-l-4 border-green-400 p-3 text-sm text-green-800 rounded">
                        You have an active subscription until {{ auth()->user()->subscription->ends_at->format('M j, Y') }}.
                    </div>
                @else
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 text-sm text-yellow-800 rounded">
                        You do not have an active subscription. You may not be able to borrow books.
                        <a href="{{ route('student.subscription.index') }}" class="ml-1 text-blue-600 underline">Subscribe</a>
                    </div>
                @endif
            </div>
        @endif
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Book Cover and Actions -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <!-- Cover Image -->
                            <div class="aspect-w-3 aspect-h-4 bg-gray-200 mb-6">
                                @if($book->cover_image)
                                    <img src="{{ asset('storage/' . $book->cover_image) }}"
                                         alt="{{ $book->title }}"
                                         class="w-full h-64 object-cover rounded-lg">
                                @else
                                    <div class="w-full h-64 bg-gray-300 rounded-lg flex items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Availability Status -->
                            <div class="mb-6">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700">Availability:</span>
                                    @if($availableCopies > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Available ({{ $availableCopies }} copies)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Unavailable
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600">Total copies: {{ $book->total_copies }}</p>
                            </div>

                            <!-- Action Buttons -->
                            <div class="space-y-3">
                                @php
                                    $atWeeklyLimit = isset($weeklyBorrows, $tierLimit) && $weeklyBorrows >= $tierLimit;
                                @endphp

                                @if($atWeeklyLimit)
                                    <div class="w-full px-4 py-3 bg-yellow-50 border-l-4 border-yellow-400 text-sm text-yellow-800 rounded">
                                        You have reached your weekly borrow limit ({{ $weeklyBorrows }}/{{ $tierLimit }}). You cannot borrow additional books this week.
                                    </div>
                                @endif

                                @if($hasActiveRequest)
                                    <div class="w-full px-4 py-2 bg-gray-300 border border-transparent rounded-md text-sm font-medium text-gray-700 text-center cursor-not-allowed">
                                        Request Already Exists
                                    </div>
                                @else
                                    @if(!$atWeeklyLimit && $canBorrow && $availableCopies > 0)
                                        <form method="POST" action="{{ route('student.borrow-requests.store', $book->id) }}">
                                            @csrf
                                            <button type="submit" data-confirm="Are you sure you want to borrow this book?" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                                Borrow This Book
                                            </button>
                                        </form>
                                    @elseif(!$canBorrow)
                                        <div class="w-full px-4 py-2 bg-gray-300 border border-transparent rounded-md text-sm font-medium text-gray-700 text-center cursor-not-allowed"
                                             title="{{ $borrowDisabledReason }}">
                                            Cannot Borrow - {{ $borrowDisabledReason }}
                                        </div>
                                    @else
                                        <div class="w-full px-4 py-2 bg-gray-300 border border-transparent rounded-md text-sm font-medium text-gray-700 text-center cursor-not-allowed">
                                            No Copies Available
                                        </div>
                                    @endif

                                    @if($canReserve && !$hasReservation && $availableCopies == 0)
                                        <form method="POST" action="{{ route('student.reservations.store') }}">
                                            @csrf
                                            <input type="hidden" name="book_id" value="{{ $book->id }}">
                                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                                Reserve This Book
                                            </button>
                                        </form>
                                    @elseif($hasReservation)
                                        <div class="w-full px-4 py-2 bg-yellow-100 border border-yellow-300 rounded-md text-sm font-medium text-yellow-800 text-center">
                                            Already Reserved
                                        </div>
                                    @elseif(!$canReserve)
                                        <div class="w-full px-4 py-2 bg-gray-300 border border-transparent rounded-md text-sm font-medium text-gray-700 text-center cursor-not-allowed">
                                            Reservation Not Available
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <!-- Back Button -->
                            <div class="mt-6">
                                <a href="{{ route('student.book-catalog.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                                    ← Back to Catalog
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Book Details -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $book->title }}</h3>
                            <p class="text-lg text-gray-600 mb-6">by {{ $book->author }}</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                @if($book->isbn)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">ISBN</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $book->isbn }}</p>
                                    </div>
                                @endif

                                @if($book->genre)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Genre</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $book->genre }}</p>
                                    </div>
                                @endif

                                @if($book->category)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Category</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $book->category }}</p>
                                    </div>
                                @endif

                                @if($book->publication_year)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Publication Year</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ $book->publication_year }}</p>
                                    </div>
                                @endif
                            </div>

                            @if($book->description)
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                    <div class="text-sm text-gray-900 leading-relaxed">
                                        {!! nl2br(e($book->description)) !!}
                                    </div>
                                </div>
                            @endif

                            <!-- Additional Information -->
                            <div class="border-t pt-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h5 class="font-medium text-gray-900 mb-2">Borrowing Information</h5>
                                        <ul class="text-sm text-gray-600 space-y-1">
                                            <li>• Total copies: {{ $book->total_copies }}</li>
                                            <li>• Available copies: {{ $availableCopies }}</li>
                                            @if(auth()->check() && auth()->user()->subscription)
                                                <li>• Your borrow limit: {{ auth()->user()->subscription->membershipTier->borrow_limit_per_week }} per week</li>
                                                <li>• Standard duration: {{ auth()->user()->subscription->membershipTier->borrow_duration_days }} days</li>
                                            @endif
                                        </ul>
                                    </div>

                                    @if(auth()->check())
                                        <div class="mb-4 p-3 rounded-md bg-gray-100 text-sm text-gray-700">
                                            <strong>Subscription:</strong>
                                            @if(auth()->user()->subscription)
                                                {{ ucfirst(auth()->user()->subscription->status) }} — valid until {{ auth()->user()->subscription->ends_at->format('M j, Y') }}
                                                <a href="{{ route('student.subscription.index') }}" class="ml-2 text-blue-600">Manage</a>
                                                @if(auth()->check() && auth()->user()->role === 'admin')
                                                    <form method="POST" action="{{ route('admin.subscriptions.quick-fix', auth()->user()->id) }}" class="inline-block ml-3">
                                                        @csrf
                                                        <button type="button" data-admin-confirm="Activate or extend subscription for {{ addslashes(auth()->user()->name) }}?" class="inline-flex items-center px-3 py-1 bg-gray-800 text-white text-xs rounded">Admin Quick Fix</button>
                                                    </form>
                                                @endif
                                            @else
                                                None <a href="{{ route('student.subscription.index') }}" class="ml-2 text-blue-600">Subscribe</a>
                                            @endif
                                        </div>
                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            <h5 class="font-medium text-gray-900 mb-2">Your Status</h5>
                                            <ul class="text-sm text-gray-600 space-y-1">
                                                @if($canBorrow)
                                                    <li class="text-green-600">✓ Eligible to borrow</li>
                                                @else
                                                    <li class="text-red-600">✗ {{ $borrowDisabledReason }}</li>
                                                @endif

                                                        @if(isset($weeklyBorrows, $tierLimit))
                                                            <li class="text-sm text-gray-700">{{ $weeklyBorrows }} of {{ $tierLimit }} used this week</li>
                                                        @endif

                                                @if($canReserve)
                                                    <li class="text-green-600">✓ Can make reservations</li>
                                                @else
                                                    <li class="text-gray-500">✗ Cannot make reservations</li>
                                                @endif

                                                @if($hasReservation)
                                                    <li class="text-yellow-600">⚠ Already have reservation</li>
                                                @endif
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>