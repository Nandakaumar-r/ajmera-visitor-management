<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RolesAndPermissionsController extends Controller
{
    
    public function index()
    {
        $roles = Role::all();
        $permissions = Permission::all();
        $users = User::all(); // Replace with your logic to fetch users
        $usersWithoutRoles = array();
        if(isset($_GET['unset'])){
            $usersWithoutRoles = User::doesntHave('roles')->get(); // Fetch users without roles
        }
        return view('roles.index', compact('roles', 'permissions', 'users', 'usersWithoutRoles'));    
    }

    public function assignRole(Request $request)
    {
        $user = User::find($request->user_id);
        $roleExists = Role::where('name', $request->role)->exists();
        if (!$roleExists) {
            return redirect()->back()->with('error', 'Role does not exist.');
        }
        
        $user->assignRole($request->role);
        return redirect()->back()->with('success', 'Role assigned successfully.');
    }

    public function revokeRole(Request $request)
    {
        // Find the user by ID
        $user = User::find($request->user_id);
    
        // Check if the user exists
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }
    
        // Check if the user has the role and remove it
        if ($user->hasRole($request->role)) {
            $user->removeRole($request->role);
            return redirect()->back()->with('success', 'Role revoked successfully.');
        } else {
            return redirect()->back()->with('error', 'User does not have this role.');
        }
    }    

    public function bulkAssignRoles(Request $request)
    {
        $userIds = $request->user_ids;
        $role = $request->role;

        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if ($user) {
                $user->assignRole($role);
            }
        }

        return redirect()->back()->with('success', 'Roles assigned successfully.');
    }
    
    public function assignPermission(Request $request)
    {
        $role = Role::find($request->role_id);
        $role->givePermissionTo($request->permission);

        return redirect()->back()->with('success', 'Permission assigned successfully.');
    }

    public function revokePermission(Request $request)
    {
        $role = Role::find($request->role_id);
        $role->revokePermissionTo($request->permission);

        return redirect()->back()->with('success', 'Permission revoked successfully.');
    }

    public function deleteRole(Role $role)
    {
        return view('roles.delete-role', compact('role'));
    }

    public function destroyRole(Request $request)
    {
        $role = Role::find($request->role_id);
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
        ]);

        Role::create(['name' => $request->name]);

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function revokeRoleFromUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'role' => 'required',
        ]);

        $user = User::find($request->user_id);

        if ($user) {
            $user->removeRole($request->role);
            return redirect()->back()->with('success', 'Role revoked successfully.');
        }

        return redirect()->back()->with('error', 'User not found.');
    }

    public function switchRole(Request $request)
    {
        // Validate the request to ensure the role exists
        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        // Get the authenticated user
        $user = auth()->user();

        // Remove all roles the user currently has
        $user->syncRoles([$request->role]);

        // Add the new role
        return redirect()->back()->with('status', 'Role switched to ' . ucfirst($request->role));
    }
}
