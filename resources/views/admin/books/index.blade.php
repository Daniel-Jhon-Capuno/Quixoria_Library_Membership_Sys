<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-semibold text-xl leading-tight text-gray-100">Books</h2>
            <a href="{{ route('admin.books.create') }}" class="inline-flex items-center px-4 py-2 rounded-md bg-gray-700 text-gray-100 hover:bg-gray-600 transition">Add Book</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 rounded-md border border-slate-700 bg-slate-800 text-green-200">{{ session('success') }}</div>
            @endif

            <div class="shadow-sm rounded-lg overflow-hidden bg-slate-800 border border-slate-700">
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
                                    <form action="{{ route('admin.books.archive', $book) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ $book->is_archived ? 'Unarchive' : 'Archive' }} this book?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="hover:opacity-80 transition text-yellow-400">{{ $book->is_archived ? 'Unarchive' : 'Archive' }}</button>
                                    </form>
                                    <form action="{{ route('admin.books.destroy', $book) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this book?');">
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
</x-app-layout>