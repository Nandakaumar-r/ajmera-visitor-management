<!-- resources/views/roles/index.blade.php -->

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Roles and Permissions
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
            <!-- Display Roles and Permissions Lists -->
            <div class="grid grid-cols-3 gap-4">

            <div class="container">
                <h2>Users with Associated Roles</h2>

                @foreach ($users as $user)
                    <div class="mb-4">
                        <strong>{{ $user->name }}</strong> - Associated Roles: 
                        @if ($user->roles->isNotEmpty())
                            @foreach ($user->roles as $role)
                                <span>{{ $role->name }}</span>
                                    <form action="{{ route('revoke-role') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                                        <input type="hidden" name="role" value="{{ $role->name }}">
                                        <button type="submit" class="text-red-500">Revoke Role</button>
                                    </form>
                                {{ !$loop->last ? ',' : '' }}
                            @endforeach
                        @else
                            <span>No roles assigned</span>
                        @endif
                    </div>
                @endforeach
            </div>


                <!-- Roles List -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Roles</h3>
                    <ul class="list-disc list-inside">
                        @foreach ($roles as $role)
                        <li>{{ $role->name }}</li>
                        @endforeach
                    </ul>
                </div>

                <!-- Permissions List -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Permissions</h3>
                    <ul class="list-disc list-inside">
                        @foreach ($permissions as $permission)
                        <li>{{ $permission->name }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Assign/Revoke Forms -->
            <div class="grid grid-cols-4 gap-4 mt-8">

                <form action="{{ route('bulk-assign-roles') }}" method="POST" class="mb-4 hidden">
                    @csrf
                    <div class="mb-4">
                        <label for="users" class="block text-gray-700 text-sm font-bold mb-2">Bulk Assign Roles : Select Users (No Roles):</label>
                        <select size="50" name="user_ids[]" id="users" class="mt-1 p-2 border border-gray-300 rounded-md w-full" multiple>
                            @foreach($usersWithoutRoles as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-6">
                        <label for="role" class="block text-gray-700 text-sm font-bold mb-2">Select Role:</label>
                        <select name="role" id="role" class="mt-1 p-2 border border-gray-300 rounded-md w-full">
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center justify-between">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Assign Roles
                        </button>
                    </div>
                </form>


                <!-- Assign Role Form -->
                <form action="{{ route('assign-role') }}" method="post" class="mb-4">
                    @csrf
                    <div class="mb-4">
                        <label for="user_id_assign" class="block text-sm font-medium text-gray-700">Select User:</label>
                        <select name="user_id" id="user_id_assign"
                                class="mt-1 p-2 border border-gray-300 rounded-md w-full">
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} (ID: {{ $user->id }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="role_assign" class="block text-sm font-medium text-gray-700">Select Role:</label>
                        <select name="role" id="role_assign"
                                class="mt-1 p-2 border border-gray-300 rounded-md w-full">
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Assign Role</button>
                </form>

                <!-- Revoke Role Form -->
                <form action="{{ route('revoke-role') }}" method="post" class="mb-4">
                    @csrf
                    <div class="mb-4">
                        <label for="user_id_revoke" class="block text-sm font-medium text-gray-700">Select User:</label>
                        <select name="user_id" id="user_id_revoke"
                                class="mt-1 p-2 border border-gray-300 rounded-md w-full">
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} (ID: {{ $user->id }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="role_revoke" class="block text-sm font-medium text-gray-700">Select Role:</label>
                        <select name="role" id="role_revoke"
                                class="mt-1 p-2 border border-gray-300 rounded-md w-full">
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">Revoke Role</button>
                </form>

                <!-- Assign Permission Form -->
                <form action="{{ route('assign-permission') }}" method="post" class="mb-4">
                    @csrf
                    <div class="mb-4">
                        <label for="role_id" class="block text-sm font-medium text-gray-700">Select Role:</label>
                        <select name="role_id" id="role_id" class="mt-1 p-2 border border-gray-300 rounded-md w-full">
                            @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="permission" class="block text-sm font-medium text-gray-700">Select Permission:</label>
                        <select name="permission" id="permission" class="mt-1 p-2 border border-gray-300 rounded-md w-full">
                            @foreach ($permissions as $permission)
                            <option value="{{ $permission->name }}">{{ $permission->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Assign Permission</button>
                </form>

                <!-- Revoke Permission Form -->
                <form action="{{ route('revoke-permission') }}" method="post" class="mb-4">
                    @csrf
                    <div class="mb-4">
                        <label for="role_id" class="block text-sm font-medium text-gray-700">Select Role:</label>
                        <select name="role_id" id="role_id" class="mt-1 p-2 border border-gray-300 rounded-md w-full">
                            @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="permission" class="block text-sm font-medium text-gray-700">Select Permission:</label>
                        <select name="permission" id="permission" class="mt-1 p-2 border border-gray-300 rounded-md w-full">
                            @foreach ($permissions as $permission)
                            <option value="{{ $permission->name }}">{{ $permission->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">Revoke Permission</button>
                </form>

                <div class="container">
                    <h2>Create Role</h2>
                    <form action="{{ route('roles.storeRole') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Role Name:</label>
                            <input type="text" name="name" id="name" class="mt-1 p-2 border border-gray-300 rounded-md w-full">
                            @error('name')
                                <p class="text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Create Role</button>
                    </form>
                </div>

                <!-- Delete Role Form -->
                <form action="{{ route('roles.destroyRole', $role) }}" method="post" class="mb-4">
                    @csrf
                    @method('DELETE')
                    <div class="mb-4">
                        <label for="role_id" class="block text-sm font-medium text-gray-700">Select Role to Delete:</label>
                        <select name="role_id" id="role_id" class="mt-1 p-2 border border-gray-300 rounded-md w-full">
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">Delete Role</button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>