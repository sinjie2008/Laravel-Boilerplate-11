<?php

namespace  App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use  Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view user', ['only' => ['index']]);
        $this->middleware('permission:create user', ['only' => ['create','store']]);
        $this->middleware('permission:update user', ['only' => ['update','edit']]);
        $this->middleware('permission:delete user', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $users = User::with('roles')
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('id', 'DESC')
            ->paginate(10);
        
        return view('role-permission.user.index', compact('users', 'search'));
    }

    public function create()
    {
        $roles = Role::pluck('name','name')->all();
        return view('role-permission.user.create', ['roles' => $roles]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|max:20',
            'roles' => 'required'
        ]);

        $user = User::create([
                        'name' => $request->name,
                        'email' => $request->email,
                        'password' => Hash::make($request->password),
                    ]);

        $user->assignRole($request->roles);
        
        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->withProperties([
                'roles' => $request->roles,
                'name' => $user->name
            ])
            ->log("User created and assigned roles: {$user->name}");

        return redirect('admin/users')->with('status', 'User Created Successfully');
    }

    public function edit(User $user)
    {
        $roles = Role::pluck('name','name')->all();
        $userRoles = $user->roles->pluck('name','name')->all();
        return view('role-permission.user.edit', [
            'user' => $user,
            'roles' => $roles,
            'userRoles' => $userRoles
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);
        
        if($request->roles) {
            $user->syncRoles($request->roles);
        }
        
        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->withProperties([
                'roles' => $request->roles,
                'name' => $user->name
            ])
            ->log("User updated: {$user->name}");

        return redirect('admin/users')->with('status', 'User Updated Successfully');
    }

    public function destroy($userId)
    {
        $user = User::findOrFail($userId);
        $user->delete();

        return redirect('admin/users')->with('status','User Delete Successfully');
    }

    public function assignRole(Request $request, User $user)
    {
        $user->syncRoles($request->roles);
        
        event('role.assigned', [$user, $request->roles]);
        
        return redirect()->back();
    }
}