<?php

namespace Gdevilbat\SpardaCMS\Modules\Role\Entities;

use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model
{
    protected $fillable = [];
    protected $table = 'role_users';
    protected $primaryKey = 'user_id';

    public function role()
    {
    	return $this->hasOne("\Gdevilbat\SpardaCMS\Modules\Role\Entities\Role", 'id', 'role_id');
    }
}
