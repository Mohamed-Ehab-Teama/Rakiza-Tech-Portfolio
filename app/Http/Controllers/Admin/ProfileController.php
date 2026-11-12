<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Dashboard → My profile: your own name, email and password.
 * Changing the email or password needs your current password.
 */
class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('admin.profile.edit', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            // Laravel's current_password rule checks it against the signed-in user.
            'current_password' => [
                Rule::requiredIf(fn () => $request->filled('password') || $request->input('email') !== $user->email),
                'nullable',
                'current_password',
            ],
        ], [
            'current_password.required' => 'Enter your current password to change your email or password.',
            'current_password.current_password' => 'Your current password is not correct.',
        ]);

        $user->fill(['name' => $data['name'], 'email' => $data['email']]);
        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }
        $user->save();

        return redirect()->route('admin.profile.edit')->with('success', 'Your profile was saved.');
    }
}
