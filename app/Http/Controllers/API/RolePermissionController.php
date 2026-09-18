<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    //
    public function roles()
    {
        return Role::where('name', '!=', 'admin')->get();
    }

    public function permissions()
    {
        return Permission::all()->groupBy(function ($p) {
            return explode('.', $p->name)[0]; // module wise
        });
    }
    public function rolePermissions($id)
{
    $role = Role::findById($id);
    return $role->permissions->pluck('name');
}

    // public function assign(Request $request)
    // {
    //     $role = Role::findById($request->role_id);
    //     $role->syncPermissions($request->permissions);

    //     return response()->json(['message' => 'Permissions updated']);
    // }

    public function grouped()
    {
        return Permission::all()
            ->groupBy(fn ($p) => explode('.', $p->name)[0]);
    }

    public function assign(Request $request)
    {
        $role = Role::findById($request->role_id, 'sanctum');

        $role->syncPermissions($request->permissions);

        return response()->json([
            'status' => true,
            'message' => 'Permissions assigned'
        ]);
    }
}
