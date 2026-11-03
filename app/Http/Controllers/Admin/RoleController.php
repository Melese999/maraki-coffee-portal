<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->can('roles.view'), 403);

        return view('admin.roles.index', [
            'roles' => Role::query()->with('permissions')->orderBy('name')->get(),
            'permissions' => Permission::query()->orderBy('name')->get()->groupBy(fn (Permission $p) => explode('.', $p->name)[0]),
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        abort_unless($request->user()?->can('roles.create'), 403);

        $role = Role::create(['name' => $request->validated('name')]);
        $role->syncPermissions($request->validated('permissions', []));

        return back()->with('success', __('Role created.'));
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        abort_unless($request->user()?->can('roles.update'), 403);

        $role->update(['name' => $request->validated('name')]);
        $role->syncPermissions($request->validated('permissions', []));

        return back()->with('success', __('Role updated.'));
    }

    public function destroy(Request $request, Role $role): RedirectResponse
    {
        abort_unless($request->user()?->can('roles.delete'), 403);
        abort_if($role->name === 'Admin', 422, __('Admin role cannot be deleted.'));

        $role->delete();

        return back()->with('success', __('Role deleted.'));
    }
}
