<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUserStoreRequest;
use App\Http\Requests\AdminUserUpdateRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $users = User::with('roles')->latest()->paginate(15);
        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store(AdminUserStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = $request->boolean('is_active', true);

        $user = User::create($data);

        if (! empty($data['role_id'])) {
            $user->roles()->sync([$data['role_id']]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function edit(string $id)
    {
        // handled via modal
    }

    /**
     * Display the specified resource.
     */
    public function update(AdminUserUpdateRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $data['is_active'] = $request->boolean('is_active', true);

        $user->update($data);

        if (! empty($data['role_id'])) {
            $user->roles()->sync([$data['role_id']]);
        } else {
            $user->roles()->detach();
        }

        return redirect()->route('admin.users.index')->with('success', 'User diperbarui');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->roles()->detach();
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User dihapus');
    }
}
