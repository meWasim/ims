<?php

namespace App\Modules\Central\RolePermission\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionIndex extends Component
{
    public $roleName = '';

    public array $selectedPermissions = [];

    public ?int $editingRoleId = null;

    public bool $showModal = false;

    public array $selectedUserIds = [];

    public ?int $assigningRoleId = null;

    public string $assigningRoleName = '';

    public string $userSearch = '';

    public bool $showUserModal = false;

    public string $activeTab = 'users';

    public array $selectedRoleIds = [];

    public ?int $editingUserId = null;

    public string $editingUserName = '';

    public bool $showUserRolesModal = false;

    public string $newUserName = '';

    public string $newUserEmail = '';

    public string $newUserPassword = '';

    public string $newUserPasswordConfirmation = '';

    public array $newUserRoleIds = [];

    public bool $showCreateUserModal = false;

    public function mount(): void
    {
        abort_unless(Gate::any(['users.view', 'roles.view']), 403);

        $this->activeTab = Gate::allows('users.view') ? 'users' : 'roles';
    }

    public function render()
    {
        $canViewRoles = Gate::allows('roles.view');
        $canViewUsers = Gate::allows('users.view');

        return view('role-permissions::index', [
            'canViewRoles' => $canViewRoles,
            'canViewUsers' => $canViewUsers,
            'roles' => $canViewRoles ? Role::with('permissions')->withCount('users')->get() : collect(),
            'permissions' => $canViewRoles ? Permission::orderBy('name')->get() : collect(),
            'users' => $canViewUsers ? User::query()
                ->with('roles:id,name')
                ->select(['id', 'name', 'email'])
                ->when($this->userSearch !== '', function ($query): void {
                    $query->where(function ($query): void {
                        $query->where('name', 'like', "%{$this->userSearch}%")
                            ->orWhere('email', 'like', "%{$this->userSearch}%");
                    });
                })
                ->orderBy('name')
                ->get() : collect(),
        ]);
    }

    public function showUsers(): void
    {
        Gate::authorize('users.view');

        $this->activeTab = 'users';
    }

    public function showRoles(): void
    {
        Gate::authorize('roles.view');

        $this->activeTab = 'roles';
    }

    public function createRole(): void
    {
        Gate::authorize('roles.create');

        $this->resetForm();

        $this->showModal = true;
    }

    public function createUser(): void
    {
        Gate::authorize('users.create');

        $this->resetCreateUserForm();

        $this->showCreateUserModal = true;
    }

    public function saveUser(): void
    {
        Gate::authorize('users.create');

        $roleTable = config('permission.table_names.roles');
        $this->validate([
            'newUserName' => ['required', 'string', 'max:255'],
            'newUserEmail' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'newUserPassword' => ['required', 'string', 'min:8', 'same:newUserPasswordConfirmation'],
            'newUserRoleIds' => ['array'],
            'newUserRoleIds.*' => [
                'integer',
                Rule::exists($roleTable, 'id')->where('guard_name', 'web'),
            ],
        ]);

        $user = User::create([
            'name' => $this->newUserName,
            'email' => $this->newUserEmail,
            'password' => Hash::make($this->newUserPassword),
        ]);

        $roles = Role::query()
            ->where('guard_name', 'web')
            ->whereIn('id', $this->newUserRoleIds)
            ->get();
        $user->syncRoles($roles);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->showCreateUserModal = false;
        $this->resetCreateUserForm();

        session()->flash('success', 'User created successfully.');
    }

    public function closeCreateUserModal(): void
    {
        $this->showCreateUserModal = false;

        $this->resetCreateUserForm();
    }

    public function resetCreateUserForm(): void
    {
        $this->reset([
            'newUserName',
            'newUserEmail',
            'newUserPassword',
            'newUserPasswordConfirmation',
            'newUserRoleIds',
        ]);

        $this->resetValidation();
    }

    public function saveRole(): void
    {
        Gate::authorize($this->editingRoleId ? 'roles.update' : 'roles.create');

        $roleTable = config('permission.table_names.roles');
        $permissionTable = config('permission.table_names.permissions');

        $this->validate([
            'roleName' => [
                'required',
                'string',
                'max:255',
                Rule::unique($roleTable, 'name')
                    ->where('guard_name', 'web')
                    ->ignore($this->editingRoleId),
            ],
            'selectedPermissions' => ['array'],
            'selectedPermissions.*' => [
                'integer',
                Rule::exists($permissionTable, 'id')->where('guard_name', 'web'),
            ],
        ]);

        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->whereIn('id', $this->selectedPermissions)
            ->get();

        if ($this->editingRoleId) {
            $role = Role::findOrFail($this->editingRoleId);

            $role->update([
                'name' => $this->roleName,
            ]);
        } else {
            $role = Role::create([
                'name' => $this->roleName,
                'guard_name' => 'web',
            ]);
        }

        $role->syncPermissions($permissions);

        $this->resetForm();

        $this->showModal = false;

        session()->flash('success', 'Role saved successfully.');
    }

    public function editRole(int $roleId): void
    {
        Gate::authorize('roles.update');

        $role = Role::with('permissions')->findOrFail($roleId);

        $this->editingRoleId = $role->id;
        $this->roleName = $role->name;

        $this->selectedPermissions = $role->permissions
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->toArray();

        $this->showModal = true;
    }

    public function deleteRole(int $roleId): void
    {
        Gate::authorize('roles.delete');

        $role = Role::findOrFail($roleId);

        $role->delete();

        session()->flash('success', 'Role deleted successfully.');
    }

    public function assignUsers(int $roleId): void
    {
        Gate::authorize('roles.update');

        $role = Role::with('users:id')->findOrFail($roleId);

        $this->assigningRoleId = $role->id;
        $this->assigningRoleName = $role->name;
        $this->selectedUserIds = collect($role->users->modelKeys())
            ->map(fn ($id) => (string) $id)
            ->toArray();
        $this->userSearch = '';
        $this->showUserModal = true;
    }

    public function saveUserAssignments(): void
    {
        Gate::authorize('roles.update');

        $this->validate([
            'selectedUserIds' => ['array'],
            'selectedUserIds.*' => ['integer', Rule::exists('users', 'id')],
        ]);

        $role = Role::findOrFail($this->assigningRoleId);

        $role->users()->sync($this->selectedUserIds);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->closeUserModal();

        session()->flash('success', 'Users assigned to the role successfully.');
    }

    public function closeUserModal(): void
    {
        $this->showUserModal = false;

        $this->reset([
            'assigningRoleId',
            'assigningRoleName',
            'selectedUserIds',
            'userSearch',
        ]);

        $this->resetValidation();
    }

    public function editUserRoles(int $userId): void
    {
        Gate::authorize('users.update');

        $user = User::with('roles:id,name')->findOrFail($userId);

        $this->editingUserId = $user->id;
        $this->editingUserName = $user->name;
        $this->selectedRoleIds = collect($user->roles->modelKeys())
            ->map(fn ($id) => (string) $id)
            ->toArray();
        $this->showUserRolesModal = true;
    }

    public function saveUserRoles(): void
    {
        Gate::authorize('users.update');

        $roleTable = config('permission.table_names.roles');

        $this->validate([
            'selectedRoleIds' => ['array'],
            'selectedRoleIds.*' => [
                'integer',
                Rule::exists($roleTable, 'id')->where('guard_name', 'web'),
            ],
        ]);

        $user = User::findOrFail($this->editingUserId);
        $roles = Role::query()
            ->where('guard_name', 'web')
            ->whereIn('id', $this->selectedRoleIds)
            ->get();

        $user->syncRoles($roles);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->closeUserRolesModal();

        session()->flash('success', 'User roles updated successfully.');
    }

    public function closeUserRolesModal(): void
    {
        $this->showUserRolesModal = false;

        $this->reset([
            'editingUserId',
            'editingUserName',
            'selectedRoleIds',
        ]);

        $this->resetValidation();
    }

    public function closeModal(): void
    {
        $this->showModal = false;

        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset([
            'roleName',
            'selectedPermissions',
            'editingRoleId',
        ]);

        $this->resetValidation();
    }
}
