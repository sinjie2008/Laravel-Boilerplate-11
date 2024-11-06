<?php

namespace  App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use JeroenNoten\LaravelAdminLte\Http\Controllers\Controller;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view role', ['only' => ['index']]);
        $this->middleware('permission:create role', ['only' => ['create','store','addPermissionToRole','givePermissionToRole']]);
        $this->middleware('permission:update role', ['only' => ['update','edit']]);
        $this->middleware('permission:delete role', ['only' => ['destroy']]);
    }

    public function index()
    {
        $roles = Role::get();
        return view('role-permission.role.index', ['roles' => $roles]);
    }

    public function create()
    {
        return view('role-permission.role.create');
    }


    public function store(Request $request)
    {
        $role = Role::create(['name' => $request->name]);
        
        activity()
            ->causedBy(auth()->user())
            ->performedOn($role)
            ->withProperties(['name' => $role->name])
            ->log("Role created: {$role->name}");

        return redirect('admin/roles')->with('status', 'Role Created Successfully');
    }

    public function edit(Role $role)
    {
        return view('role-permission.role.edit',[
            'role' => $role
        ]);
    }

    public function update(Request $request, Role $role)
    {

        $request->validate([
            'name' => [
                'required',
                'string',
                'unique:roles,name,'.$role->id
            ]
        ]);

        $role->update([
            'name' => $request->name
        ]);

        return redirect('admin/roles')->with('status','Role Updated Successfully');
    }

    public function destroy($roleId)
    {
        $role = Role::findOrFail($roleId);
        
        activity()
            ->causedBy(auth()->user())
            ->performedOn($role)
            ->withProperties(['name' => $role->name])
            ->log("Role deleted: {$role->name}");
        
        $role->delete();
        return redirect('admin/roles')->with('status', 'Role Deleted Successfully');
    }

    public function addPermissionToRole($roleId)
    {
        $permissions = Permission::get();
        $role = Role::findOrFail($roleId);
        $rolePermissions = DB::table('role_has_permissions')
                                ->where('role_has_permissions.role_id', $role->id)
                                ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
                                ->all();

        return view('role-permission.role.add-permissions', [
            'role' => $role,
            'permissions' => $permissions,
            'rolePermissions' => $rolePermissions
        ]);
    }

    public function givePermissionToRole(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);
        
        if($request->permission) {
            $role->syncPermissions($request->permission);
            
            activity()
                ->causedBy(auth()->user())
                ->performedOn($role)
                ->withProperties([
                    'permissions' => $request->permission,
                    'role' => $role->name
                ])
                ->log("Permissions updated for role {$role->name}");
        }

        return redirect('admin/roles')->with('status', 'Permissions added to Role');
    }
}