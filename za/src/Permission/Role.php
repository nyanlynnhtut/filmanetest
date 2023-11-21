<?php

namespace Za\Support\Permission;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'za_roles';

    protected $fillable = ['name', 'slug', 'permissions', 'level', 'group', 'type', 'region'];

    protected $casts = [
        'name' => 'json',
        'permissions' => 'json',
    ];

    public static function getForRoleAttachment($slugs)
    {
        return static::whereIn('slug', $slugs)->get()->map(fn ($item) => $item->slug)->toArray();
    }

    public function attachPermissions(array $permissions)
    {
        $this->permissions = $permissions;
        $this->save();

        return $this;
    }

    public function isRole($role)
    {
        // @TODO May be need to check with name
        return $this->slug === $role;
    }

    public function canAccess($permission)
    {
        return in_array($permission, $this->permissions);
    }

    public function scopeOnlyUnderLevel($query, $level)
    {
        return $query->where('level', '>', $level);
    }
}
