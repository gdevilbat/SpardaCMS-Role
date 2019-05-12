<?php

namespace Gdevilbat\SpardaCMS\Modules\Role\Entities;

use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model
{
    protected $fillable = [];
    protected $table = 'role_users';

    public function role()
    {
    	return $this->belongsTo("\Gdevilbat\SpardaCMS\Modules\Role\Entities\Role", 'role_id');
    }
}
