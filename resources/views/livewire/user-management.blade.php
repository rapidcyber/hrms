<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">User Management</h1>
        <button wire:click="showAddUserModal" class="bg-blue-600 text-white px-4 py-2 rounded">Add User</button>
    </div>
    @if (session('message'))
        <div class="mb-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('message') }}
            </div>
        </div>
    @endif
    <!-- Users Table -->
    <div class="bg-white shadow rounded">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($users as $user)
                <tr>
                    <td class="px-6 py-4">{{ $user->name }}</td>
                    <td class="px-6 py-4">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        <select wire:change="attachRole({{ $user->id }}, $event.target.value)" class="border rounded px-2 py-1">
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" @if($user->roles->contains($role->id)) selected @endif>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button wire:click="showEditUserModal({{ $user->id }})" class="bg-yellow-500 text-white px-3 py-1 rounded mr-2">Edit</button>
                        <button wire:click="showConfirmDeleteModal({{ $user->id }})" class="bg-red-600 text-white px-3 py-1 rounded">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{-- Roles table --}}
    <div class="mt-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Roles</h2>
            <button wire:click="showAddRoleModal" class="bg-blue-600 text-white px-4 py-2 rounded">Add Role</button>
        </div>
        <div class="bg-white shadow rounded">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role Name</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($roles as $role)
                    <tr>
                        <td class="px-6 py-4">{{ $role->name }}</td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="showEditRoleModal({{ $role->id }})" class="bg-yellow-500 text-white px-3 py-1 rounded mr-2">Edit</button>
                            <button wire:click="showConfirmDeleteRoleModal({{ $role->id }})" class="bg-red-600 text-white px-3 py-1 rounded">Delete</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add/Edit Role Modal -->
    <x-modal wire:show="showRoleModal" title="{{ $editRoleMode ? 'Edit Role' : 'Add Role' }}">
        <form wire:submit.prevent="saveRole">
            <div class="mb-4">
                <label class="block text-gray-700">Role Name</label>
                <input type="text" wire:model.defer="roleData.name" class="w-full border rounded px-3 py-2" required>
                @error('roleData.name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="flex justify-end">
                <button type="button" wire:click="closeRoleModal" class="mr-2 px-4 py-2 rounded bg-gray-300">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white">{{ $editRoleMode ? 'Update' : 'Add' }}</button>
            </div>
        </form>
    </x-modal>
    <!-- Confirm Delete Role Modal -->
    <x-modal wire:show="showConfirmDeleteRole" title="Confirm Delete Role">
        <p>Are you sure you want to delete this role?</p>
        <div class="flex justify-end mt-4">
            <button wire:click="closeConfirmDeleteRole" class="mr-2 px-4 py-2 rounded bg-gray-300">Cancel</button>
            <button wire:click="confirmDeleteRole" class="px-4 py-2 rounded bg-red-600 text-white">Delete</button>
        </div>
    </x-modal>
    <!-- Add/Edit User Modal -->
    <x-modal wire:show="showUserModal" title="{{ $editMode ? 'Edit User' : 'Add User' }}">
        <form wire:submit.prevent="{{ $editMode ? 'updateUser' : 'saveUser' }}">
            <div class="mb-4">
                <label class="block text-gray-700">Name</label>
                <input type="text" wire:model.defer="userData.name" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Email</label>
                <input type="email" wire:model.defer="userData.email" class="w-full border rounded px-3 py-2" required>
            </div>
            @if(!$editMode)
            <div class="mb-4">
                <label class="block text-gray-700">Password</label>
                <input type="password" wire:model.defer="userData.password" class="w-full border rounded px-3 py-2" required>
            </div>
            @endif
            <div class="flex justify-end">
                <button type="button" wire:click="closeUserModal" class="mr-2 px-4 py-2 rounded bg-gray-300">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white">{{ $editMode ? 'Update' : 'Add' }}</button>
            </div>
        </form>
    </x-modal>

    <!-- Confirm Delete User Modal -->
    <x-modal wire:show="showConfirmDelete" title="Confirm Delete User">
        <p>Are you sure you want to delete this user?</p>
        <div class="flex justify-end mt-4">
            <button wire:click="closeConfirmDelete" class="mr-2 px-4 py-2 rounded bg-gray-300">Cancel</button>
            <button wire:click="confirmDelete" class="px-4 py-2 rounded bg-red-600 text-white">Delete</button>
        </div>
    </x-modal>
</div>
