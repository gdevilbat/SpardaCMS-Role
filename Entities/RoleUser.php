<?php

namespace Gdevilbat\SpardaCMS\Modules\Role\Entities;

use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model
{
    protected $fillable = [];
    protected $table = 'role_users';
    protected $primaryKey = \Gdevilbat\SpardaCMS\Modules\Core\Entities\User::FOREIGN_KEY;

    public function role()
    {
    	return $this->belongsTo("\Gdevilbat\SpardaCMS\Modules\Role\Entities\Role", \Gdevilbat\SpardaCMS\Modules\Role\Entities\Role::FOREIGN_KEY);
    }
}
