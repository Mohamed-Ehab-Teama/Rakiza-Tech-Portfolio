<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Dashboard → Roles: groups of permissions (Editor, Viewer, your own…).
 * The Super Admin role is protected: it cannot be edited or deleted, because
 * it already passes every check.
 */
class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:roles.view', only: ['index']),
            new Middleware('can:roles.create', only: ['create', 'store']),
            new Middleware('can:roles.update', only: ['edit', 'update']),
            new Middleware('can:roles.delete', only: ['destroy']),
        ];
    }

    public function index(): View
    {
        return view('admin.roles.index', [
            'roles' => Role::withCount(['users', 'permissions'])->orderBy('name')->get(),
            'superAdmin' => config('access.super_admin'),
            'totalPermissions' => Permission::count(),
        ]);
    }

    public function create(): View
    {
        return view('admin.roles.form', ['role' => null, 'granted' => []]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, null);

        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
        $role->syncPermissions($data['permissions'] ?? []);
        $this->refreshPermissionCache();

        ActivityLog::record('created', description: "Created role \"{$role->name}\"");

        return redirect()->route('admin.roles.index')->with('success', "Role \"{$role->name}\" was created.");
    }

    public function edit(Role $role): View
    {
        $this->ensureEditable($role);

        return view('admin.roles.form', [
            'role' => $role,
            'granted' => $role->permissions->pluck('name')->all(),
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $this->ensureEditable($role);
        $data = $this->validated($request, $role);

        $role->update(['name' => $data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);
        $this->refreshPermissionCache();

        ActivityLog::record('updated', description: "Updated role \"{$role->name}\" (".count($data['permissions'] ?? []).' permissions)');

        return redirect()->route('admin.roles.index')->with('success', "Role \"{$role->name}\" was saved.");
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->ensureEditable($role);

        $users = $role->users()->count();
        if ($users > 0) {
            return back()->with('error', "\"{$role->name}\" is used by {$users} ".str('user')->plural($users).'. Give them another role first.');
        }

        $name = $role->name;
        $role->delete();
        $this->refreshPermissionCache();

        ActivityLog::record('deleted', description: "Deleted role \"{$name}\"");

        return redirect()->route('admin.roles.index')->with('success', "Role \"{$name}\" was deleted.");
    }

    private function validated(Request $request, ?Role $role): array
    {
        return $request->validate([
            'name' => [
                'required', 'string', 'max:60',
                Rule::unique('roles', 'name')->ignore($role?->id),
                Rule::notIn([config('access.super_admin')]),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::exists('permissions', 'name')],
        ]);
    }

    private function ensureEditable(Role $role): void
    {
        abort_if($role->name === config('access.super_admin'), 403, 'The Super Admin role is protected.');
    }

    private function refreshPermissionCache(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
