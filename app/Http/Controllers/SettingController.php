<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class SettingController extends Controller
{
    public function index()
    {
        $org = $this->org()->load('defaultTemplate');
        $members = $org->users()->orderBy('name')->get();
        $user = auth()->user();

        return view('settings.index', compact('org', 'members', 'user'));
    }

    // ── Organisation Profile ───────────────────────────────────────

    private function org()
    {
        return auth()->user()->organisation;
    }

    public function updateOrganisation(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:1000'],
            'currency' => ['required', Rule::in(['KES', 'USD', 'GBP', 'EUR', 'ZAR', 'UGX', 'TZS'])],
        ]);

        $this->org()->update($data);

        return back()->with('success', 'Organisation profile updated.');
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => ['required', 'image', 'mimes:png,jpg,jpeg,svg', 'max:2048'],
        ]);

        $org = $this->org();

        if ($org->logo_path && Storage::disk('public')->exists($org->logo_path)) {
            Storage::disk('public')->delete($org->logo_path);
        }

        $path = $request->file('logo')->store('logos', 'public');
        $org->update(['logo_path' => $path]);

        return back()->with('success', 'Logo updated.');
    }

    public function removeLogo()
    {
        $org = $this->org();

        if ($org->logo_path && Storage::disk('public')->exists($org->logo_path)) {
            Storage::disk('public')->delete($org->logo_path);
        }

        $org->update(['logo_path' => null]);

        return back()->with('success', 'Logo removed.');
    }

    // ── Team Management ────────────────────────────────────────────

    public function inviteMember(Request $request)
    {
        $this->requireOwner();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['owner', 'member'])],
            'password' => ['required', Password::min(8)],
        ]);

        User::create([
            'organisation_id' => $this->org()->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => Hash::make($data['password']),
            'is_super_admin' => false,
        ]);

        return back()->with('success', "{$data['name']} has been added to the team.");
    }

    private function requireOwner(): void
    {
        if (!auth()->user()->isOwner()) {
            abort(403, 'Only owners can manage team members.');
        }
    }

    public function updateMemberRole(Request $request, User $user)
    {
        $this->requireOwner();
        $this->authoriseMember($user);

        $request->validate([
            'role' => ['required', Rule::in(['owner', 'member'])],
        ]);

        if ($user->id === auth()->id() && $request->role === 'member') {
            $ownerCount = $this->org()->users()->where('role', 'owner')->count();
            if ($ownerCount <= 1) {
                return back()->with('error', 'Cannot demote yourself — you are the only owner.');
            }
        }

        $user->update(['role' => $request->role]);

        return back()->with('success', "{$user->name}'s role updated.");
    }

    // ── Personal Account ───────────────────────────────────────────

    private function authoriseMember(User $user): void
    {
        if ($user->organisation_id !== $this->org()->id) {
            abort(403);
        }
    }

    public function removeMember(User $user)
    {
        $this->requireOwner();
        $this->authoriseMember($user);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot remove yourself.');
        }

        if ($user->role === 'owner' && $this->org()->users()->where('role', 'owner')->count() <= 1) {
            return back()->with('error', 'Cannot remove the last owner.');
        }

        $user->delete();

        return back()->with('success', "{$user->name} removed from the team.");
    }

    // ── Private ────────────────────────────────────────────────────

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update($data);

        return back()->with('success', 'Profile updated.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])
                ->withInput();
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password changed successfully.');
    }
}
