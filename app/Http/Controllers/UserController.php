<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Org;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class UserController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        $users = User::with(['roles', 'org'])->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'role' => ['required', 'string', 'exists:roles,name'],
                'org_name' => ['required', 'string', 'max:255'],
                'abn' => ['nullable', 'string', 'max:20'],
                'website' => ['nullable', 'url', 'max:255'],
            ]);

            if ($validator->fails()) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Create the user
            $user = new User();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            Log::info('User created successfully', ['user_id' => $user->id]);

            // Create organization
            try {
                $org = new Org();
                $org->name = $request->org_name;
                $org->slug = Str::slug($request->org_name);
                $org->user_id = $user->id;
                $org->status = 'active';
                $org->abn = $request->abn;
                $org->website = $request->website;
                $org->date_joined = now();
                $org->save();
                
                Log::info('Organization created successfully', ['org_id' => $org->id, 'org_name' => $org->name]);
            } catch (Exception $orgException) {
                Log::error('Organization creation error: ' . $orgException->getMessage());
                Log::error($orgException->getTraceAsString());
                // Continue with user creation even if org creation fails
            }

            // Assign role
            $user->assignRole($request->role);

            // Return response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'User created successfully.']);
            }

            return redirect()->route('admin.users')->with('success', 'User created successfully.');
        } catch (Exception $e) {
            Log::error('User creation error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Failed to create user: ' . $e->getMessage()], 500);
            }

            return back()->withInput()->withErrors(['error' => 'Failed to create user: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {
        $user = User::with(['roles', 'org'])->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) {
        $user = User::with('roles')->findOrFail($id);
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {
        try {
            $user = User::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
                'role' => ['required', 'string', 'exists:roles,name'],
                'org_name' => ['required', 'string', 'max:255'],
                'abn' => ['nullable', 'string', 'max:20'],
                'website' => ['nullable', 'url', 'max:255'],
                'address_line1' => ['nullable', 'string', 'max:255'],
                'address_line2' => ['nullable', 'string', 'max:255'],
                'city' => ['nullable', 'string', 'max:255'],
                'state' => ['nullable', 'string', 'max:255'],
                'postal_code' => ['nullable', 'string', 'max:20'],
                'country' => ['nullable', 'string', 'max:255'],
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Update user data
            $userData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $request->validate([
                    'password' => ['confirmed', Rules\Password::defaults()],
                ]);
                
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            // Update or create organization
            if ($user->org) {
                // Update existing organization
                $user->org->update([
                    'name' => $request->org_name,
                    'slug' => Str::slug($request->org_name),
                    'abn' => $request->abn,
                    'website' => $request->website,
                    'address_line1' => $request->address_line1,
                    'address_line2' => $request->address_line2,
                    'city' => $request->city,
                    'state' => $request->state,
                    'postal_code' => $request->postal_code,
                    'country' => $request->country,
                ]);
                
                Log::info('Organization updated successfully', ['org_id' => $user->org->id, 'org_name' => $user->org->name]);
            } else {
                // Create new organization if it doesn't exist
                $org = new Org();
                $org->name = $request->org_name;
                $org->slug = Str::slug($request->org_name);
                $org->user_id = $user->id;
                $org->status = 'active';
                $org->abn = $request->abn;
                $org->website = $request->website;
                $org->address_line1 = $request->address_line1;
                $org->address_line2 = $request->address_line2;
                $org->city = $request->city;
                $org->state = $request->state;
                $org->postal_code = $request->postal_code;
                $org->country = $request->country;
                $org->date_joined = now();
                $org->save();
                
                Log::info('Organization created successfully', ['org_id' => $org->id, 'org_name' => $org->name]);
            }

            // Sync roles (remove all roles and assign the new one)
            $user->syncRoles([$request->role]);

            return redirect()->route('admin.users')->with('success', 'User updated successfully.');
        } catch (Exception $e) {
            Log::error('User update error: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to update user: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {
        try {
            $user = User::findOrFail($id);
            
            // First delete the associated organization if it exists
            if ($user->org) {
                $user->org->delete();
            }
            
            // Now delete the user
            $user->delete();
            
            return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
        } catch (Exception $e) {
            Log::error('User deletion error: ' . $e->getMessage());
            return redirect()->route('admin.users')->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }
}
