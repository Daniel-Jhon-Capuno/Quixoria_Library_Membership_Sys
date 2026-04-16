<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Book</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form method="POST" action="{{ route('admin.books.update', $book) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" name="title" value="{{ old('title', $book->title) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            @error('title')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Author</label>
                            <input type="text" name="author" value="{{ old('author', $book->author) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            @error('author')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">ISBN</label>
                                <input type="text" name="isbn" value="{{ old('isbn', $book->isbn) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('isbn')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Publication Year</label>
                                <input type="number" name="publication_year" value="{{ old('publication_year', $book->publication_year) }}" min="1000" max="{{ date('Y') + 1 }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('publication_year')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Genre</label>
                                <input type="text" name="genre" value="{{ old('genre', $book->genre) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('genre')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Category</label>
                                <input type="text" name="category" value="{{ old('category', $book->category) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('category')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $book->description) }}</textarea>
                            @error('description')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Cover Image</label>
                            @if($book->cover_image)
                                <div class="mt-2 mb-2">
                                    <img src="{{ asset('storage/' . $book->cover_image) }}" alt="Current cover" class="w-32 h-48 object-cover rounded">
                                </div>
                            @endif
                            <input type="file" name="cover_image" accept="image/*" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('cover_image')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total Copies</label>
                            <input type="number" name="total_copies" value="{{ old('total_copies', $book->total_copies) }}" min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            @error('total_copies')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-between">
                        <a href="{{ route('admin.books.index') }}" class="text-gray-600 hover:text-gray-900">Back</a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Update Book</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>