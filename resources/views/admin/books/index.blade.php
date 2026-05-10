<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-gray-100">Books</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 rounded-md border border-slate-700 bg-slate-800 text-green-200">{{ session('success') }}</div>
            @endif

            <!-- Add Book Button -->
            <div class="mb-6 flex justify-end">
                <a href="{{ route('admin.books.create') }}" class="inline-flex items-center px-6 py-3 bg-gray-700 hover:bg-gray-600 text-gray-100 rounded-lg font-medium transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add New Book
                </a>
            </div>

            <div class="shadow-sm rounded-lg overflow-hidden bg-slate-800 border border-slate-700">
                <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-700">
                    <thead class="bg-slate-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Title</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Author</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Total Copies</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Available Copies</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-transparent divide-y divide-slate-700">
                        @foreach($books as $book)
                            <tr class="border-b border-slate-700 hover:opacity-90 transition bg-slate-800">
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-100">{{ $book->title }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-100">{{ $book->author }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-100">{{ $book->total_copies }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-100">{{ $book->available_copies }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $book->is_archived ? 'bg-red-800 text-red-200' : 'bg-gray-700 text-gray-100' }}">
                                        {{ $book->is_archived ? 'Archived' : 'Active' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="{{ route('admin.books.edit', $book) }}" class="hover:opacity-80 transition text-gray-100">Edit</a>
                                    <form action="{{ route('admin.books.archive', $book) }}" method="POST" class="inline-block" data-confirm="{{ $book->is_archived ? 'Unarchive this book?' : 'Archive this book?' }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="hover:opacity-80 transition text-yellow-400">{{ $book->is_archived ? 'Unarchive' : 'Archive' }}</button>
                                    </form>
                                    <form action="{{ route('admin.books.destroy', $book) }}" method="POST" class="inline-block" data-confirm="Delete this book?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="hover:opacity-80 transition text-red-400">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>