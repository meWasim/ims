<div class="flex h-full w-full flex-1 flex-col gap-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">
                Users & Roles
            </flux:heading>

            <flux:text class="mt-1">
                Assign roles to users, then manage role permissions.
            </flux:text>
        </div>

        @if ($activeTab === 'users' && $canViewUsers)
            @can('users.create')
            <flux:button
                variant="primary"
                wire:click="createUser"
                icon="plus"
            >
                Create User
            </flux:button>
            @endcan
        @elseif ($activeTab === 'roles' && $canViewRoles)
            @can('roles.create')
            <flux:button
                variant="primary"
                wire:click="createRole"
                icon="plus"
            >
                Create Role
            </flux:button>
            @endcan
        @endif
    </div>

    {{-- Success Message --}}
    @if (session()->has('success'))
        <flux:callout
            variant="success"
            icon="check-circle"
        >
            {{ session('success') }}
        </flux:callout>
    @endif

    {{-- Tabs --}}
    <div class="flex w-fit rounded-lg border border-neutral-700 p-1">
        @if ($canViewUsers)
            <button
                type="button"
                wire:click="showUsers"
                @class([
                    'rounded-md px-4 py-2 text-sm font-medium transition',
                    'bg-neutral-700 text-white' => $activeTab === 'users',
                    'text-neutral-400 hover:text-white' => $activeTab !== 'users',
                ])
            >
                Users
            </button>
        @endif

        @if ($canViewRoles)
            <button
                type="button"
                wire:click="showRoles"
                @class([
                    'rounded-md px-4 py-2 text-sm font-medium transition',
                    'bg-neutral-700 text-white' => $activeTab === 'roles',
                    'text-neutral-400 hover:text-white' => $activeTab !== 'roles',
                ])
            >
                Roles & Permissions
            </button>
        @endif
    </div>

    @if ($activeTab === 'users' && $canViewUsers)
        {{-- Users Table --}}
        <div class="overflow-hidden rounded-xl border border-neutral-700 bg-neutral-900">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-neutral-700 bg-neutral-800">
                        <tr>
                            <th class="px-6 py-4 font-medium text-neutral-300">User</th>
                            <th class="px-6 py-4 font-medium text-neutral-300">Roles</th>
                            <th class="w-40 px-6 py-4 text-right font-medium text-neutral-300">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-700">
                        @forelse ($users as $user)
                            <tr wire:key="user-{{ $user->id }}" class="transition hover:bg-neutral-800/70">
                                <td class="px-6 py-5">
                                    <div class="font-medium text-white">#{{ $user->id }} — {{ $user->name }}</div>
                                </td>
                                <td class="px-6 py-5">
                                    @if ($user->roles->isNotEmpty())
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($user->roles as $role)
                                                <flux:badge size="sm" variant="outline">{{ $role->name }}</flux:badge>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-neutral-500">No roles assigned</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-right">
                                    @can('users.update')
                                        <flux:button size="sm" variant="ghost" wire:click="editUserRoles({{ $user->id }})">
                                            Assign Roles
                                        </flux:button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-16 text-center text-neutral-500">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @elseif ($activeTab === 'roles' && $canViewRoles)
    {{-- Roles Table --}}
    <div class="overflow-hidden rounded-xl border border-neutral-700 bg-neutral-900">

        <div class="overflow-x-auto">

            <table class="w-full text-left text-sm">

                {{-- Table Header --}}
                <thead class="border-b border-neutral-700 bg-neutral-800">
                    <tr>
                        <th class="w-20 px-6 py-4 font-medium text-neutral-300">
                            ID
                        </th>

                        <th class="w-64 px-6 py-4 font-medium text-neutral-300">
                            Role
                        </th>

                        <th class="px-6 py-4 font-medium text-neutral-300">
                            Permissions
                        </th>

                        <th class="w-64 px-6 py-4 text-right font-medium text-neutral-300">
                            Actions
                        </th>
                    </tr>
                </thead>

                {{-- Table Body --}}
                <tbody class="divide-y divide-neutral-700">

                    @forelse ($roles as $role)

                        <tr
                            wire:key="role-{{ $role->id }}"
                            class="transition hover:bg-neutral-800/70"
                        >

                            {{-- ID --}}
                            <td class="px-6 py-5">
                                <span class="font-medium text-neutral-400">
                                    #{{ $role->id }}
                                </span>
                            </td>

                            {{-- Role --}}
                            <td class="px-6 py-5">
                                <div class="font-medium text-white">
                                    {{ $role->name }}
                                </div>

                                <div class="mt-1 text-xs text-neutral-500">
                                    Guard: {{ $role->guard_name }}
                                </div>

                                <div class="mt-1 text-xs text-neutral-500">
                                    {{ $role->users_count }} {{ \Illuminate\Support\Str::plural('user', $role->users_count) }} assigned
                                </div>
                            </td>

                            {{-- Permissions --}}
                            <td class="px-6 py-5">

                                @if ($role->permissions->isNotEmpty())

                                    <div class="flex flex-wrap gap-2">

                                        @foreach ($role->permissions as $permission)

                                            <flux:badge
                                                size="sm"
                                                variant="outline"
                                            >
                                                {{ $permission->name }}
                                            </flux:badge>

                                        @endforeach

                                    </div>

                                @else

                                    <span class="text-xs text-neutral-500">
                                        No permissions assigned
                                    </span>

                                @endif

                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-5">

                                <div class="flex justify-end gap-2">

                                    {{-- Assign users --}}
                                    @can('roles.update')
                                        <flux:button
                                            size="sm"
                                            variant="ghost"
                                            wire:click="assignUsers({{ $role->id }})"
                                        >
                                            Users
                                        </flux:button>
                                    @endcan

                                    {{-- Edit --}}
                                    @can('roles.update')
                                        <flux:button
                                            size="sm"
                                            variant="ghost"
                                            wire:click="editRole({{ $role->id }})"
                                        >
                                            Edit
                                        </flux:button>
                                    @endcan

                                    {{-- Delete --}}
                                    @can('roles.delete')
                                        <flux:button
                                            size="sm"
                                            variant="danger"
                                            wire:click="deleteRole({{ $role->id }})"
                                            wire:confirm="Are you sure you want to delete this role?"
                                        >
                                            Delete
                                        </flux:button>
                                    @endcan

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">

                                <div class="flex flex-col items-center">

                                    <flux:icon
                                        name="shield-exclamation"
                                        class="mb-3 size-10 text-neutral-500"
                                    />

                                    <flux:heading size="sm">
                                        No roles found
                                    </flux:heading>

                                    <flux:text class="mt-1">
                                        Create your first role to get started.
                                    </flux:text>

                                    <flux:button
                                        class="mt-4"
                                        size="sm"
                                        variant="primary"
                                        wire:click="createRole"
                                        icon="plus"
                                    >
                                        Create Role
                                    </flux:button>

                                </div>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>
    @endif

    {{-- Create / Edit Role Modal --}}
    <flux:modal
        name="role-form"
        wire:model="showModal"
        @close="closeModal"
        class="w-full max-w-2xl"
    >
        <form wire:submit="saveRole" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ $editingRoleId ? 'Edit Role' : 'Create Role' }}
                </flux:heading>

                <flux:subheading class="mt-1">
                    Give the role a name and choose the permissions it should have.
                </flux:subheading>
            </div>

            <flux:input
                wire:model="roleName"
                label="Role name"
                placeholder="e.g. Manager"
                required
                autofocus
            />

            <flux:field>
                <flux:label>Permissions</flux:label>

                <div class="mt-3 grid max-h-72 grid-cols-1 gap-3 overflow-y-auto rounded-lg border border-neutral-700 p-4 sm:grid-cols-2">
                    @forelse ($permissions as $permission)
                        <flux:checkbox
                            wire:model="selectedPermissions"
                            value="{{ $permission->id }}"
                            label="{{ $permission->name }}"
                        />
                    @empty
                        <flux:text>No permissions have been created yet.</flux:text>
                    @endforelse
                </div>

                <flux:error name="selectedPermissions" />
                <flux:error name="selectedPermissions.*" />
            </flux:field>

            <div class="flex justify-end gap-3">
                <flux:button type="button" variant="ghost" wire:click="closeModal">
                    Cancel
                </flux:button>

                <flux:button type="submit" variant="primary">
                    {{ $editingRoleId ? 'Save Changes' : 'Create Role' }}
                </flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Create User Modal --}}
    <flux:modal
        name="create-user"
        wire:model="showCreateUserModal"
        @close="closeCreateUserModal"
        class="w-full max-w-3xl"
    >
        <form wire:submit="saveUser" class="space-y-6">
            <div>
                <flux:heading size="lg">Create User</flux:heading>
                <flux:subheading class="mt-1">
                    Create an account and optionally assign roles.
                </flux:subheading>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input wire:model="newUserName" label="Name" placeholder="Full name" required autofocus />
                <flux:input wire:model="newUserEmail" label="Email" type="email" placeholder="name@example.com" required />
                <flux:input wire:model="newUserPassword" label="Password" type="password" required />
                <flux:input wire:model="newUserPasswordConfirmation" label="Confirm password" type="password" required />
            </div>

            <flux:field>
                <flux:label>Roles</flux:label>
                <div class="mt-3 max-h-56 space-y-3 overflow-y-auto rounded-lg border border-neutral-700 p-4">
                    @forelse ($roles as $role)
                        <flux:checkbox wire:model="newUserRoleIds" value="{{ $role->id }}" label="{{ $role->name }}" />
                    @empty
                        <flux:text>No roles created yet.</flux:text>
                    @endforelse
                </div>
                <flux:error name="newUserRoleIds" />
                <flux:error name="newUserRoleIds.*" />
            </flux:field>

            <div class="flex justify-end gap-3">
                <flux:button type="button" variant="ghost" wire:click="closeCreateUserModal">Cancel</flux:button>
                <flux:button type="submit" variant="primary">Create User</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Manage User Roles Modal --}}
    <flux:modal
        name="user-roles"
        wire:model="showUserRolesModal"
        @close="closeUserRolesModal"
        class="w-full max-w-2xl"
    >
        <form wire:submit="saveUserRoles" class="space-y-6">
            <div>
                <flux:heading size="lg">Manage User Roles</flux:heading>
                <flux:subheading class="mt-1">
                    Choose the roles for {{ $editingUserName }}.
                </flux:subheading>
            </div>

            <flux:field>
                <flux:label>Roles</flux:label>

                <div class="mt-3 max-h-72 space-y-3 overflow-y-auto rounded-lg border border-neutral-700 p-4">
                    @forelse ($roles as $role)
                        <flux:checkbox
                            wire:model="selectedRoleIds"
                            value="{{ $role->id }}"
                            label="{{ $role->name }}"
                        />
                    @empty
                        <flux:text>Create a role before assigning one to a user.</flux:text>
                    @endforelse
                </div>

                <flux:error name="selectedRoleIds" />
                <flux:error name="selectedRoleIds.*" />
            </flux:field>

            <div class="flex justify-end gap-3">
                <flux:button type="button" variant="ghost" wire:click="closeUserRolesModal">
                    Cancel
                </flux:button>

                <flux:button type="submit" variant="primary">
                    Save Roles
                </flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Assign Users Modal --}}
    <flux:modal
        name="role-users"
        wire:model="showUserModal"
        @close="closeUserModal"
        class="w-full max-w-2xl"
    >
        <form wire:submit="saveUserAssignments" class="space-y-6">
            <div>
                <flux:heading size="lg">Assign Users</flux:heading>
                <flux:subheading class="mt-1">
                    Choose the users who should have the {{ $assigningRoleName }} role.
                </flux:subheading>
            </div>

            <flux:input
                wire:model.live.debounce.300ms="userSearch"
                label="Search users"
                placeholder="Search by name or email"
                icon="magnifying-glass"
            />

            <flux:field>
                <flux:label>Users</flux:label>

                <div class="mt-3 max-h-72 space-y-3 overflow-y-auto rounded-lg border border-neutral-700 p-4">
                    @forelse ($users as $user)
                        <flux:checkbox
                            wire:model="selectedUserIds"
                            value="{{ $user->id }}"
                            label="{{ $user->name }} ({{ $user->email }})"
                        />
                    @empty
                        <flux:text>No users match your search.</flux:text>
                    @endforelse
                </div>

                <flux:error name="selectedUserIds" />
                <flux:error name="selectedUserIds.*" />
            </flux:field>

            <div class="flex justify-end gap-3">
                <flux:button type="button" variant="ghost" wire:click="closeUserModal">
                    Cancel
                </flux:button>

                <flux:button type="submit" variant="primary">
                    Save Users
                </flux:button>
            </div>
        </form>
    </flux:modal>

</div>
