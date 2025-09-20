<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Role;

class UserManagement extends Component
{
    public $users;
    public $roles;
    public $editingUser = null;
    public $editingRole = null;
    public $userData = [];
    public $roleData = [];
    public $showUserModal = false;
    public $showRoleModal = false;
    public $showConfirmDelete = false;
    public $showConfirmDeleteRole = false;
    public $editMode = false;
    public $editRoleMode = false;

    public function mount()
    {
        $this->refreshUsers();
        $this->refreshRoles();
    }

    public function render()
    {
        return view('livewire.user-management');
    }

    // --- User Methods ---

    public function createUser()
    {
        $this->validate([
            'userData.name' => 'required|string|max:255',
            'userData.email' => 'required|email|unique:users,email',
            // Add more validation rules as needed
        ]);
        User::create($this->userData);
        $this->refreshUsers();
        $this->userData = [];
        session()->flash('success', 'User created successfully.');
        $this->closeUserModal();
    }

    public function editUser($userId)
    {
        $this->editingUser = User::findOrFail($userId);
        $this->userData = $this->editingUser->toArray();
    }

    public function updateUser()
    {
        if ($this->editingUser) {
            $this->validate([
                'userData.name' => 'required|string|max:255',
                'userData.email' => 'required|email|unique:users,email,' . $this->editingUser->id,
                // Add more validation rules as needed
            ]);
            $this->editingUser->update($this->userData);
            $this->refreshUsers();
            log_activity('update_user', 'Updated user details', $this->editingUser);
            $this->editingUser = null;
            $this->userData = [];
            $this->editMode = false;
            session()->flash('message', 'User updated successfully.');
            $this->closeUserModal();
        }
    }

    public function saveUser()
    {
        if ($this->editingUser) {
            $this->updateUser();
        } else {
            $this->createUser();
        }
    }

    public function deleteUser($userId)
    {
        $user = User::find($userId);
        if ($user) {
            $user->delete();
            $this->refreshUsers();
        }
    }

    // --- Role Methods ---

    public function createRole()
    {
        $this->validate([
            'roleData.name' => 'required|string|max:255|unique:roles,name',
            // Add more validation rules as needed
        ]);
        $role = Role::create($this->roleData);
        log_activity('create_role', 'Created a new role', $role);
        $this->refreshRoles();
        $this->roleData = [];

        session()->flash('message', 'Role created successfully.');

        $this->closeRoleModal();
    }

    public function editRole($roleId)
    {
        $this->editingRole = Role::findOrFail($roleId);
        $this->roleData = $this->editingRole->toArray();
    }

    public function updateRole()
    {
        if ($this->editingRole) {
            $this->validate([
                'roleData.name' => 'required|string|max:255|unique:roles,name,' . $this->editingRole->id,
                // Add more validation rules as needed
            ]);
            $this->editingRole->update($this->roleData);
            log_activity('create_role', 'Updated a new role', $this->editingRole);
            $this->refreshRoles();
            $this->editingRole = null;
            $this->roleData = [];
            session()->flash('message', 'Role updated successfully.');
            $this->closeRoleModal();
        }
    }

    public function saveRole()
    {
        if ($this->editingRole) {
            $this->updateRole();
        } else {
            $this->createRole();
        }
    }

    public function deleteRole($roleId)
    {
        $role = Role::find($roleId);
        if ($role) {
            $role->delete();
            $this->refreshRoles();
        }
    }

    //--- User Modals
    public function showAddUserModal()
    {
        $this->editingUser = null;
        $this->userData = [];
        $this->showUserModal = true;
    }

    public function showAddRoleModal()
    {
        $this->editingRole = null;
        $this->roleData = [];
        $this->showRoleModal = true;
    }
    public function closeUserModal()
    {
        $this->showUserModal = false;
        $this->editingUser = null;
        $this->userData = [];
    }
    public function showEditUserModal($userId)
    {
        $this->editMode = true;
        $this->editingUser = User::findOrFail($userId);
        $this->userData = $this->editingUser->toArray();
        $this->showUserModal = true;
    }
    public function showEditRoleModal($roleId)
    {
        $this->editRole($roleId);
        $this->editRoleMode = true;
        $this->showRoleModal = true;
    }
    public function showConfirmDeleteModal($userId)
    {
        $this->editingUser = User::findOrFail($userId);
        $this->showConfirmDelete = true;
    }

    public function closeConfirmDelete()
    {
        $this->showConfirmDelete = false;
        $this->editingUser = null;
    }

    public function confirmDelete()
    {
        if ($this->editingUser) {
            $this->editingUser->delete();
            $this->refreshUsers();
            $this->showConfirmDelete = false;
            $this->editingUser = null;
        }
    }

    public function closeRoleModal()
    {
        $this->showRoleModal = false;
        $this->editingRole = null;
        $this->editRoleMode = false;
        $this->roleData = [];
    }
    public function showConfirmDeleteRoleModal($roleId)
    {
        $this->editingRole = Role::findOrFail($roleId);
        $this->showConfirmDeleteRole = true;
    }

    public function closeConfirmDeleteRole()
    {
        $this->showConfirmDeleteRole = false;
        $this->editingRole = null;
    }

    public function confirmDeleteRole()
    {
        if ($this->editingRole) {
            $this->editingRole->delete();
            log_activity('delete_role', 'Deleted a role', $this->editingRole);
            $this->refreshRoles();
            $this->showConfirmDeleteRole = false;
            $this->editingRole = null;
            session()->flash('message', 'Role deleted successfully.');
        }
    }
    // --- Attach Role to User ---

    public function attachRole($userId, $roleId)
    {
        $user = User::find($userId);
        $role = Role::find($roleId);

        if ($user && $role) {
            // Prevent duplicate attachment
            if (!$user->roles->contains($roleId)) {
                $user->roles()->attach($roleId);
            }
            $this->refreshUsers();
        }
    }

    // --- Helpers ---

    private function refreshUsers()
    {
        $this->users = User::with('roles')->get();
    }

    private function refreshRoles()
    {
        $this->roles = Role::all();
    }
}
