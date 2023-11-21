<?php

namespace Za\Support\Permission\Traits;

use Za\Support\Permission\Role;

trait HasRoleAndPermission
{
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function isRole($role)
    {
        $roleModel = $this->role;

        return $roleModel ? $roleModel->isRole($role) : false;
    }

    public function assignRole(Role $role)
    {
        $this->role_id = $role->id;

        return $this->save();
    }

    public function canAccess($permission)
    {
        $role = $this->role;

        return $role ? $role->canAccess($permission) : false;
    }

    public function permit($permission)
    {
        return $this->canAccess($permission);
    }
}
