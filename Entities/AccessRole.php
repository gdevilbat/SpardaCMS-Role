<?php

namespace Gdevilbat\SpardaCMS\Modules\Role\Entities;

use Illuminate\Database\Eloquent\Model;

class AccessRole extends Model
{
    protected $fillable = [];
    protected $table= 'access_roles';
    protected $casts = [
        'access_scope' => 'array',
    ];

    public static function getTableName()
    {
        return with(new Static)->getTable();
    }

    public static function getPrimaryKey()
    {
        return with(new Static)->getKeyName();
    }
}
