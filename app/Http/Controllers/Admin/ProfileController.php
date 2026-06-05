<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user()->load('roles');

        $stats = [
            'last_login'    => $user->last_login_at
                ? \Carbon\Carbon::parse($user->last_login_at)->diffForHumans()
                : 'Never',
            'joined'        => $user->created_at->format('d M Y'),
            'role'          => ucfirst($user->roles->first()?->name ?? 'No role'),
        ];

        return view('admin.profile.show', compact('user', 'stats'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect.'
            ]);
        }

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('password_success', 'Password changed successfully.');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:2048',
        ]);

        $user = auth()->user();

        // Delete old photo
        if ($user->profile_photo && \Storage::disk('public')->exists($user->profile_photo)) {
            \Storage::disk('public')->delete($user->profile_photo);
        }

        $path = $request->file('photo')->store('profile-photos', 'public');
        $user->update(['profile_photo' => $path]);

        return back()->with('success', 'Profile photo updated.');
    }
}
