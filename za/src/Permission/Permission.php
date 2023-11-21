<?php

namespace Za\Support\Permission;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'za_permissions';

    protected $fillable = ['name', 'slug', 'group', 'type'];

    protected $casts = [
        'name' => 'json',
    ];

    public static function getForRoleAttachment($slugs)
    {
        return static::whereIn('slug', $slugs)->get()->map(function ($item) {
            return $item->slug;
        })->toArray();
    }
}
