<?php

namespace Modules\Role\App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionController extends \App\Http\Controllers\Controller // Correct base controller
{
    public function __construct()
    {
        $this->middleware('permission:view permission', ['only' => ['index']]);
        $this->middleware('permission:create permission', ['only' => ['create','store']]);
        $this->middleware('permission:update permission', ['only' => ['update','edit']]);
        $this->middleware('permission:delete permission', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $permissions = DB::table('permissions')
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('id', 'DESC')
            ->paginate(10);
        
        return view('role::permissions.index', compact('permissions', 'search'));
    }

    public function create()
    {
        return view('role::permissions.create');
    }

    public function store(Request $request)
    {
        $permission = Permission::create(['name' => $request->name]);

        return redirect('admin/permissions')->with('status', 'Permission Created Successfully');
    }

    public function edit(Permission $permission)
    {
        return view('role::permissions.edit', ['permission' => $permission]);
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'unique:permissions,name,'.$permission->id
            ]
        ]);

        $permission->update([
            'name' => $request->name
        ]);

        return redirect('admin/permissions')->with('status','Permission Updated Successfully');
    }

    public function destroy($permissionId)
    {
        $permission = Permission::findOrFail($permissionId);
        
        $permission->delete();
        return redirect('admin/permissions')->with('status', 'Permission Deleted Successfully');
    }
}
