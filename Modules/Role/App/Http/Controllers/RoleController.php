<?php

namespace Modules\Role\App\Http\Controllers; // Updated namespace

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
// Assuming AdminLTE controller is still needed globally or within the module
use JeroenNoten\LaravelAdminLte\Http\Controllers\Controller; 

class RoleController extends Controller
{
    public function __construct()
    {
        // Permissions remain the same for now
        $this->middleware('permission:view role', ['only' => ['index']]);
        $this->middleware('permission:create role', ['only' => ['create','store','addPermissionToRole','givePermissionToRole']]);
        $this->middleware('permission:update role', ['only' => ['update','edit']]);
        $this->middleware('permission:delete role', ['only' => ['destroy']]);
    }

    public function index(Request $request) // Request might still be needed for other purposes
    {
        // Remove search and pagination logic
        // $search = $request->input('search');
        // $roles = DB::table('roles')
        //     ->when($search, function ($query) use ($search) {
        //         return $query->where('name', 'like', "%{$search}%");
        //     })
        //     ->orderBy('id', 'DESC')
        //     ->paginate(10);

        // Fetch all roles - DataTables will handle searching/pagination client-side
        $roles = Role::orderBy('id', 'DESC')->get(); // Use Eloquent model for consistency

        // Updated view path, remove 'search' compact variable
        return view('role::index', compact('roles'));
    }

    public function create()
    {
        // Updated view path
        return view('role::create'); 
    }


    public function store(Request $request)
    {
        $role = Role::create(['name' => $request->name]);

        // Updated redirect path (assuming '/admin/role' prefix later)
        return redirect('/admin/role')->with('status', 'Role Created Successfully'); 
    }

    public function edit(Role $role)
    {
        // Updated view path
        return view('role::edit',[ 
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

        // Updated redirect path
        return redirect('/admin/role')->with('status','Role Updated Successfully'); 
    }

    public function destroy($roleId)
    {
        $role = Role::findOrFail($roleId);
        
        $role->delete();
        // Updated redirect path
        return redirect('/admin/role')->with('status', 'Role Deleted Successfully'); 
    }

    public function addPermissionToRole($roleId)
    {
        $permissions = Permission::get();
        $role = Role::findOrFail($roleId);
        $rolePermissions = DB::table('role_has_permissions')
                                ->where('role_has_permissions.role_id', $role->id)
                                ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
                                ->all();

        // Updated view path
        return view('role::add-permissions', [ 
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
        }

        // Updated redirect path
        return redirect('/admin/role')->with('status', 'Permissions added to Role'); 
    }
}
