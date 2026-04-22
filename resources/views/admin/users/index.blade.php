<x-app-layout>
            <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-100 leading-tight">Users</h2>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-cyan-600 text-white rounded-md hover:bg-cyan-500">Create User</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-800 rounded-md">{{ session('success') }}</div>
            @endif

            <div class="card mb-6">
                <div class="card-body">
                <form method="GET" action="{{ route('admin.users.index') }}" class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-200">Role</label>
                        <select name="role" class="mt-1 block w-full bg-slate-900 border border-slate-700 text-gray-100 rounded-md shadow-sm">
                            <option value="">All</option>
                            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Student</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-200">Status</label>
                        <select name="status" class="mt-1 block w-full bg-slate-900 border border-slate-700 text-gray-100 rounded-md shadow-sm">
                            <option value="">All</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="btn-primary">Filter</button>
                        <a href="{{ route('admin.users.index') }}" class="btn-secondary">Reset</a>
                    </div>
                </form>
                </div>
            </div>
            <div class="card">
                <div class="card-body p-0">
                <table class="min-w-full divide-y divide-slate-700">
                    <thead class="bg-slate-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Role</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-transparent divide-y divide-slate-700">
                        @foreach($users as $user)
                            <tr>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-200">{{ $user->name }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-200">{{ $user->email }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-200">{{ ucfirst($user->role) }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-200">{{ $user->is_active ? 'Active' : 'Inactive' }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="text-gray-200 hover:text-gray-300">Edit</a>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600">Delete</button>
                                    </form>
                                    <form action="{{ route('admin.users.reset-password', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Send password reset email?');">
                                        @csrf
                                        <button type="submit" class="text-gray-200 hover:text-gray-300">Reset Password</button>
                                    </form>
                                    <form action="{{ route('admin.subscriptions.quick-fix', $user) }}" method="POST" class="inline-block">
                                        @csrf
                                        <input type="hidden" name="extend_days" value="30">
                                        <button type="button" data-admin-confirm="Activate or extend subscription for {{ addslashes($user->name) }}?" class="text-green-300 hover:text-green-500">Quick Fix Sub</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
            </div>
        </div>
    </div>
</x-app-layout>
