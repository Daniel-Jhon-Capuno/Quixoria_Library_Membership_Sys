<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-semibold text-xl leading-tight" style="color: rgb(var(--text-primary));">Books</h2>
            <a href="{{ route('admin.books.create') }}" class="inline-flex items-center px-4 py-2 rounded-md hover:opacity-80 transition" style="background-color: rgb(var(--accent-secondary)); color: rgb(var(--text-primary));">Add Book</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 rounded-md border" style="background-color: rgba(0, 255, 200, 0.1); border-color: rgb(var(--accent-primary)); color: rgb(var(--accent-primary));">{{ session('success') }}</div>
            @endif

            <div class="shadow-sm rounded-lg overflow-hidden" style="background-color: rgb(var(--surface-primary)); border: 1px solid rgb(var(--border-primary));">
                <table class="min-w-full divide-y" style="border-color: rgb(var(--border-primary));">
                    <thead style="background-color: rgb(var(--bg-secondary));">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--text-secondary));">Title</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--text-secondary));">Author</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--text-secondary));">Total Copies</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--text-secondary));">Available Copies</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--text-secondary));">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--text-secondary));">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="border-color: rgb(var(--border-primary));">
                        @foreach($books as $book)
                            <tr style="border-bottom-color: rgb(var(--border-primary)); background-color: rgb(var(--surface-primary));" class="border-b hover:opacity-80 transition">
                                <td class="px-4 py-4 whitespace-nowrap text-sm" style="color: rgb(var(--text-primary));">{{ $book->title }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm" style="color: rgb(var(--text-primary));">{{ $book->author }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm" style="color: rgb(var(--text-primary));">{{ $book->total_copies }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm" style="color: rgb(var(--text-primary));">{{ $book->available_copies }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" style="background-color: {{ $book->is_archived ? 'rgba(239, 68, 68, 0.2)' : 'rgba(0, 255, 200, 0.2)' }}; color: {{ $book->is_archived ? '#ef4444' : '#00ffc8' }};">
                                        {{ $book->is_archived ? 'Archived' : 'Active' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="{{ route('admin.books.edit', $book) }}" class="hover:opacity-80 transition" style="color: rgb(var(--accent-secondary));">Edit</a>
                                    <form action="{{ route('admin.books.archive', $book) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ $book->is_archived ? 'Unarchive' : 'Archive' }} this book?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="hover:opacity-80 transition" style="color: {{ $book->is_archived ? 'rgb(var(--accent-primary))' : '#fbbf24' }};">{{ $book->is_archived ? 'Unarchive' : 'Archive' }}</button>
                                    </form>
                                    <form action="{{ route('admin.books.destroy', $book) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this book?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="hover:opacity-80 transition" style="color: #ef4444;">Delete</button>
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