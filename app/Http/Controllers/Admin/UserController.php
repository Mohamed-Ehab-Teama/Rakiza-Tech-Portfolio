<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

/**
 * Dashboard → Users: accounts, their role and on/off switch.
 *
 * Safety rules (so nobody can lock the team out or promote themselves):
 *  - only a Super Admin can give the Super Admin role, or edit/delete a Super Admin
 *  - nobody can change their own role, switch themselves off or delete themselves
 *  - the last active Super Admin can never be demoted, switched off or deleted
 */
class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:users.view', only: ['index']),
            new Middleware('can:users.create', only: ['create', 'store']),
            new Middleware('can:users.update', only: ['edit', 'update']),
            new Middleware('can:users.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $users = User::with('roles')
            ->when($search !== '', fn ($q) => $q->where(fn ($inner) => $inner
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('admin.users.index', ['users' => $users, 'search' => $search]);
    }

    public function create(Request $request): View
    {
        return view('admin.users.form', [
            'user' => null,
            'roles' => $this->assignableRoles($request->user()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $roles = $this->assignableRoles($request->user());

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'string', Rule::in($roles)],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user = DB::transaction(function () use ($data, $request) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'is_active' => $request->boolean('is_active'),
            ]);
            $user->forceFill(['email_verified_at' => now()])->save();
            $user->assignRole($data['role']);

            return $user;
        });

        ActivityLog::record('updated', $user, ['role'], "Gave {$user->name} the role \"{$data['role']}\"");

        return redirect()->route('admin.users.index')->with('success', "User \"{$user->name}\" was created.");
    }

    public function edit(Request $request, User $user): View
    {
        $this->ensureCanManage($request->user(), $user);

        return view('admin.users.form', [
            'user' => $user,
            'roles' => $this->assignableRoles($request->user()),
            'isSelf' => $request->user()->is($user),
            'isLastSuperAdmin' => $user->isLastActiveSuperAdmin(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $actor = $request->user();
        $this->ensureCanManage($actor, $user);

        $isSelf = $actor->is($user);
        $roles = $this->assignableRoles($actor);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => [$isSelf ? 'nullable' : 'required', 'string', Rule::in($roles)],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $newRole = $isSelf ? null : $data['role'];
        $active = $isSelf ? true : $request->boolean('is_active');

        if ($user->isLastActiveSuperAdmin()) {
            if ($newRole !== null && $newRole !== config('access.super_admin')) {
                throw ValidationException::withMessages(['role' => 'This is the last active Super Admin — give someone else the Super Admin role first.']);
            }
            if (! $active) {
                throw ValidationException::withMessages(['is_active' => 'The last active Super Admin cannot be switched off.']);
            }
        }

        DB::transaction(function () use ($user, $data, $newRole, $active) {
            $user->fill([
                'name' => $data['name'],
                'email' => $data['email'],
                'is_active' => $active,
            ]);
            if (! empty($data['password'])) {
                $user->password = $data['password'];
            }
            $user->save();

            if ($newRole !== null && ! $user->hasRole($newRole)) {
                $user->syncRoles([$newRole]);
                ActivityLog::record('updated', $user, ['role'], "Gave {$user->name} the role \"{$newRole}\"");
            }
        });

        return redirect()->route('admin.users.index')->with('success', "User \"{$user->name}\" was saved.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $actor = $request->user();
        $this->ensureCanManage($actor, $user);

        if ($actor->is($user)) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        if ($user->isLastActiveSuperAdmin()) {
            return back()->with('error', 'The last active Super Admin cannot be deleted.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', "User \"{$name}\" was deleted.");
    }

    /** Roles this person may hand out: Super Admin only by a Super Admin. */
    private function assignableRoles(User $actor): array
    {
        return Role::query()
            ->orderBy('name')
            ->pluck('name')
            ->reject(fn ($name) => $name === config('access.super_admin') && ! $actor->isSuperAdmin())
            ->values()
            ->all();
    }

    /** Only a Super Admin may touch a Super Admin account. */
    private function ensureCanManage(User $actor, User $target): void
    {
        abort_if($target->isSuperAdmin() && ! $actor->isSuperAdmin(), 403, 'Only a Super Admin can change a Super Admin account.');
    }
}
